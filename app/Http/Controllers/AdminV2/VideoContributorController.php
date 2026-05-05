<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\Contributor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ContributorImageSubmission;
use App\Models\ContributorVideoSubmission;

class VideoContributorController extends Controller
{

public function index_submissions(Request $request)
{

    $status = $request->get('status');
    if ($request->has('status') and $status === "update") {
        $index_url = route('admin.videos.contributors.submissions.datatable_submissions_update_after_publish');
        $review_url = route('admin.videos.contributors.submissions.review', [0,'status'=>"update"]);
    }else{
        $index_url = route('admin.videos.contributors.submissions.datatable');
        $review_url = route('admin.videos.contributors.submissions.review', 0);
    }


    $html_breadcrumbs = [
        'title' => __('views.Submissions'),
        'subtitle' => __('views.Index'),
        'datatable' => true,
    ];

    $subheader_actions = [];

    $is_videos_site = true;

    return view(
        'admin_v2.submission.index',
        compact(
            'is_videos_site',
            'html_breadcrumbs',
            'subheader_actions',
            'index_url',
            'review_url'
        )
    );
}

    public function datatable_submissions(Request $request)
    {

        $query = ContributorVideoSubmission::whereHas('items.file', function ($q) {
            $q->whereIn('contributor_stage', [1, 2]);
        })->with('contributor')->withCount(['items' => function ($q) {
            $q->whereHas('file', function ($q) {
                $q->whereIn('contributor_stage', [1, 2]);
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

        $is_videos_site = true;

        return view(
            'admin_v2.submission.review.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'routes',
                'user'
            )
        );
    }

    public function datatable_submissions_update_after_publish(Request $request)
    {
        $query = ContributorVideoSubmission::with(['contributor','items.file']);
        if ($request->contributor)
            $query->where('contributor_id', $request->contributor);
        if ($request->date)
            $query->whereDate('created_at', $request->date);
        $query = $query->whereHas('items.file', function ($q) {
            $q->whereIn('contributor_stage', [8])
                ->whereHas('file', function ($q) {
                    $q->whereHas('contributor_file', function ($q) {
                        $q->where('stage_edit', 3);
                    });
                });
        });
        $result_set = $query->withCount(['items as items_count'=> function($q){
            $q->whereHas('file', function ($q) {
                $q->whereIn('contributor_stage', [8])
                    ->whereHas('file', function ($q) {
                        $q->whereHas('contributor_file', function ($q) {
                            $q->where('stage_edit', 3);
                        });
                    });
            });
        }]);
        if ($request->sort)
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('updated_at', 'desc');
        $result_set = $result_set->paginate($request->input('pagination.perpage', 20), ['*'], 'pagination.page');
        return [
            'meta' => [
                "page" => $result_set->currentPage(),
                "pages" => $result_set->lastPage(),
                "perpage" => $result_set->perPage(),
                "total" => $result_set->total(),
                "sort" => "desc",
                "field" => "id"
            ],
            'data' => $result_set->items()
        ];

    }
}
