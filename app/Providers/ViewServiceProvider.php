<?php

namespace App\Providers;

use App\Models\Pages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\ContributorImageSubmission;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use App\Models\ContributorVideoSubmission;
use App\Models\ContributorVectorSubmission;


class ViewServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Admin Settings
        $settings = settings();
        if (!$settings)
            return;
        View::composer('admin_v2.layout.app', function ($view) {
            if (Auth::check() && \in_array(Auth::user()->role, ['admin_video', 'admin', 'admin_vector', 'admin_image_editor', 'admin_video_editor', 'admin_vector_editor'])) {
                $role = Auth::user()->role;
                if (in_array($role, ["admin_video", 'admin_video_editor']))
                    $notifications = $this->notification(ContributorVideoSubmission::class,
                        [
                            'contributor' => function ($query) {
                                $query->select('id', 'name');
                            }
                        ], "file", 'items.file', "file.file", "فيديو", "admin.videos.contributors.submissions.review");
                elseif (in_array($role, ["admin", 'admin_image_editor']))
                    $notifications = $this->notification(ContributorImageSubmission::class,
                        [
                            'contributor' => function ($query) {
                                $query->select('id', 'name');
                            }
                        ], "file", 'items.image', "images.file", "صورة", "admin.contributors.submissions.review");
                elseif (in_array($role, ["admin_vector", 'admin_vector_editor']))
                    $notifications = $this->notification(ContributorVectorSubmission::class,
                        [
                            'contributor' => function ($query) {
                                $query->select('id', 'name');
                            }
                        ], "file", 'items.file', "file.file", "فيكتور", "admin.vectors.contributors.submissions.review");


                $view->with(['notifications' => $notifications]);
            }
        });
        View()->share('settings', $settings);
        $pages = cache()->tags(['page'])->remember('legal_pages', now()->addHours(12), function () {
            $slugs = ['privacy', 'license-agreement', 'terms-of-service'];
            return Pages::whereIn('slug', $slugs)->select('id', 'slug', 'title_en', 'title_ar')->get();
        });
        View()->share('pages', $pages);
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {

    }

    function notification($model, $relationShips = [], $relationFile, $nestedRelationFile, $nestedRelationFilesCountPending, $type, $reviewUrlRouteName)
    {

        $model = app()->make($model);
        $getRelation = \explode('.', $nestedRelationFile);

        $query = cache()->remember("panel_notification_" . get_class($model), now()->addMinutes(10), function () use ($model, $relationShips, $relationFile, $nestedRelationFile, $nestedRelationFilesCountPending, $type, $reviewUrlRouteName, $getRelation) {
            if (count($relationShips) > 0)
                $query = $model->with($relationShips);
            $query = $query->select('id', 'contributor_id')->whereHas($nestedRelationFile, function ($q) use ($relationFile) {
                $q->whereIn('contributor_stage', [1, 2, 8, 6])
                    ->doesntHave($relationFile)
                    ->orWhereHas($relationFile, function ($q) {
                        $q->whereHas('contributor_file', function ($q) {
                            $q->where('contributor_stage', 6);
                        });
                    });

            })->withCount(
                [
                    'items as files_count_pending' => function ($query) use ($getRelation) {
                        $query->whereHas($getRelation[1], function ($q) {
                            $q->where('contributor_stage', 2)
                                ->doesntHave('file');

                        });
                    },
                    'items as files_count_pending_error_published' => function ($query) use ($getRelation) {
                        $query->whereHas($getRelation[1], function ($q) {
                            $q->where('contributor_stage', 8)
                                ->doesntHave('file');

                        });

                    },
                    'items as files_count_pending_after_rejected' => function ($query) use ($getRelation) {
                        $query->whereHas($getRelation[1], function ($q) {
                            $q->where('contributor_stage', 6);

                        });

                    },
                ]
            );
            return $query->orderBy('updated_at', 'desc')->get();
        });
        $notifications['data'] = $query = $query->transform(function ($val) use ($type, $reviewUrlRouteName) {
            $username = @$val->contributor->name ? @$val->contributor->name : "admin";
            $countItems = 0;
            $countItems = $val->files_count_pending + $val->files_count_pending_after_rejected + $val->files_count_pending_error_published;
            $data = ['type' => $type ?? '', 'name' => $username, 'countNumber' => $countItems];
            if (($val->files_count_pending_error_published > 0 && $val->files_count_pending_after_rejected > 0 && $val->files_count_pending > 0) || ($val->files_count_pending_error_published > 0 && $val->files_count_pending > 0) || ($val->files_count_pending_after_rejected > 0 && $val->files_count_pending > 0))
                $mesage = __('views.error_or_rejected_photos_were_added_count', $data);
            elseif ($val->files_count_pending_error_published > 0 && $val->files_count_pending_after_rejected > 0)
                $mesage = __('views.error_or_rejected_photos_were_added_count', $data);
            elseif ($val->files_count_pending_error_published > 0 && $val->files_count_pending_after_rejected === 0)
                $mesage = __('views.update_photos_were_added_count', $data);
            elseif ($val->files_count_pending_after_rejected > 0 && $val->files_count_pending_error_published === 0)
                $mesage = __('views.published_after_rejected_photos_were_added_count', $data);
            else
                $mesage = __('views.new_photos_were_added_count', $data);

            return [
                'message' => $mesage,
                'review_url' => route($reviewUrlRouteName, $val->id)
            ];
        });
        $notifications['count'] = count($query);
        return $notifications;
    }
}
