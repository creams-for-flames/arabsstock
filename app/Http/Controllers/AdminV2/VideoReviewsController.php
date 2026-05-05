<?php

namespace App\Http\Controllers\AdminV2;

use App\Category;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VideoReviewsController extends Controller
{
    /**
     * Display a listing of Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->datatable) {
            $index_url = route('admin.video-reviews.index');

            $html_breadcrumbs = [
                'title' => __('Reviewed Video'),
                'subtitle' => __('views.Index'),
                'datatable' => true,
            ];

            return view(
                'admin_v2.video-reviews.index',
                compact(
                    'html_breadcrumbs',
                    'index_url'
                )
            );
        }
        $result_set = Video::query();
        if ($request->input('query.reviewer')) {
            $result_set->whereIn('reviewer_id', User::where(function ($q) {
                $q->where('name', 'like', '%' . \request('query.reviewer') . '%')
                    ->orWhere('email', 'like', '%' . \request('query.reviewer') . '%')
                    ->orWhere('username', 'like', '%' . \request('query.reviewer') . '%');
            })->pluck('id')->toArray());
        }
        if ($request->input('query.publisher')) {
            $result_set->whereIn('publisher_id', User::where(function ($q) {
                $q->where('name', 'like', '%' . \request('query.publisher') . '%')
                    ->orWhere('email', 'like', '%' . \request('query.publisher') . '%')
                    ->orWhere('username', 'like', '%' . \request('query.publisher') . '%');
            })->pluck('id')->toArray());
        }
        if ($request->sort && $result_set->getModel()->isFillable($request->input('sort.field')))
            $result_set->orderBy($request->input('sort.field'), $request->input('sort.sort', 'desc'));
        else
            $result_set->orderBy('id', 'desc');
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
            'data' => collect($result_set->items())->each(function (&$r) {
                $r->reviewer = $r->reviewer();
                $r->publisher = $r->publisher();
            })
        ];
    }

}
