<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Contributor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ContributorImageSubmission;
use App\Models\ContributorVectorSubmission;

class VectorContributorController extends Controller
{

    public function index_submissions(Request $request)
    {

        $status = $request->get('status');
        if ($request->has('status') and $status === "update") {
            $index_url = route('admin.vectors.contributors.submissions.datatable_submissions_update_after_publish');
            $review_url = route('admin.vectors.contributors.submissions.review', [0,'status'=>"update"]);
        }else{
            $index_url = route('admin.vectors.contributors.submissions.datatable');
            $review_url = route('admin.vectors.contributors.submissions.review', 0);
        }

        $html_breadcrumbs = [
            'title' => __('views.Submissions'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];

        $subheader_actions = [];

        $is_vectors_site = true;

        return view(
            'admin_v2.submission.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'subheader_actions',
                'index_url',
                'review_url'
            )
        );
    }

    public function datatable_submissions(Request $request)
    {
        $query = ContributorVectorSubmission::whereHas('items.file', function ($q) {
            $q->whereIn('contributor_stage', [1, 2])
                ->doesntHave('file')
                ->orWhereHas('file', function ($q) {
                    $q->whereHas('contributor_file', function ($q) {
                        $q->where('contributor_stage', 6);
                    });
                });
        })->with('contributor')->withCount(['items' => function ($q) {
            $q->whereHas('file', function ($q) {
                $q->whereIn('contributor_stage', [1, 2])
                    ->doesntHave('file')
                    ->orWhereHas('file', function ($q) {
                        $q->whereHas('contributor_file', function ($q) {
                            $q->where('contributor_stage', 6);
                        });
                    });
            });
        }]);

        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('id', 'DESC');
        });

        return $data;
    }

    public function index_review(Request $request, $id)
    {


        $routes = get_vue_image_store_routes($id);

        $html_breadcrumbs = [
            'title' => __('views.review'),
            'subtitle' => __('views.Index'),
        ];

        $user = Auth::user()->only(
            'id',
            'email',
            'api_token'
        );
        $is_vectors_site = true;

        return view(
            'admin_v2.submission.review.index',
            compact(
                'is_vectors_site',
                'html_breadcrumbs',
                'routes',
                'user'
            )
        );
    }

    public function datatable_submissions_update_after_publish(Request $request)
    {
        $query = ContributorVectorSubmission::with('contributor')->withCount(['items as items_count'=> function($q){
            $q->whereHas('file', function ($q) {
                $q->whereIn('contributor_stage', [8])
                    ->whereHas('file', function ($q) {
                        $q->whereHas('contributor_file', function ($q) {
                            $q->where('stage_edit', 3);
                        });
                    });
            });
        }]);
        $query = $query->whereHas('items.file', function ($q) {
            $q->whereIn('contributor_stage', [8])
                ->whereHas('file', function ($q) {
                    $q->whereHas('contributor_file', function ($q) {
                        $q->where('stage_edit', 3);
                    });
                });
        });
        $data = process_datatable_query($query, function (
            $query,
            $search
        ) {
            return $query
                ->orderBy('id', 'DESC');
        });

        return $data;

    }

}
