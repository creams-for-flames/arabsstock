<?php

namespace App\Http\Controllers\AdminV2;

use App\Models\AdminCollectionImage;
use App\Models\Download;
use App\Models\ImageCategory;
use App\Models\Purchase;
use App\Models\VideoCategory;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Image;
use App\Models\Video;
use App\Models\Vector;
use Illuminate\Http\Request;
use App\Models\CategoryVideo;
use App\Models\AdminCollection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AdminCollectionVector;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as RequestGuzzle;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $statistics = DB::table('statistics')->get()->keyBy('key')->map(function ($r) {
            return $r->value;
        })->toArray();

        $purchases = json_decode($statistics['dashboard.images.purchases'], 0);
        $purchases_count = $statistics['dashboard.images.purchases_count'];


        $imges = json_decode($statistics['dashboard.images.chart'], 0);

        $imgesActive = $statistics['dashboard.images.active_count'];
        $imgesnotActive = $statistics['dashboard.images.pending_count'];

        $usersDash = json_decode($statistics['dashboard.usrs.cahrt'], 0);
        $userDashCount = $statistics['dashboard.usrs.count'];
        $categories = ImageCategory::where('mode', 'on')->get();
        $imagesCategory = json_decode($statistics['dashboard.image_categories'], 0);
        $totalImages = 0;
        foreach ($imagesCategory as $imagesCategoryItem) {
            $totalImages += $imagesCategoryItem->images_count;
        }

        $imagesCategoryPaying = json_decode($statistics['dashboard.images.paying_per_category'], 0);

        $totalImagesPaying = 0;
        foreach ($imagesCategoryPaying as $imagesCategoryPayingItem) {
            $totalImagesPaying += $imagesCategoryPayingItem->images_count;
        }

        if ($request->category_id || $request->from_date) {
            $top_downloading = Image::tinySelection()->withCount(['downloads' => function ($q) use ($request) {
                if ($request->from_date)
                    $q->where('downloads.created_at', '>=', Carbon::parse($request->from_date));
                if ($request->to_date)
                    $q->where('downloads.created_at', '<=', Carbon::parse($request->to_date));
            }])->where(function ($q) {
                if (request('category_id'))
                    $q->whereHas('category', function ($q) {
                        $q->where('image_categories.id', request('category_id'));
                    });
                if (request('admin_collection_id'))
                    $q->whereHas('adminCollection', function ($q) {
                        $q->where('admin_collections.id', request('admin_collection_id'));
                    });
            })->orderBy('downloads_count', 'desc')->take(5)->get();
        } else
            $top_downloading = collect(json_decode($statistics['dashboard.images.top_downloading'], 0));
        $last_update = $statistics['last_update'];
        $html_breadcrumbs = [
            'title' => __('views.Dashboard'),
            'subtitle' => __('views.Index'),
        ];
        $api_removebg = json_decode($statistics['dashboard.api.removebg']);
        $removebg = json_decode($statistics['dashboard.removebg'], 0);
        return view(
            'admin_v2.dashboard.index',
            compact(
                'html_breadcrumbs',
                'imges',
                'imgesActive',
                'imgesnotActive',
                'removebg',
                'purchases',
                'purchases_count',
                'usersDash',
                'userDashCount',
                'imagesCategory',
                'totalImages',
                'totalImagesPaying',
                'imagesCategoryPaying',
                'top_downloading',
                'api_removebg',
                'last_update',
                'categories'
            )
        );
    }

    public function index_video(Request $request)
    {
        $statistics = DB::table('statistics')->get()->keyBy('key')->map(function ($r) {
            return $r->value;
        })->toArray();
        $purchases = json_decode($statistics['dashboard.videos.purchases'], 0);
        $purchases_count = $statistics['dashboard.videos.purchases_count'];

        $videos = json_decode($statistics['dashboard.videos.chart'], 0);

        $videosActive = $statistics['dashboard.videos.active_count'];
        $videosnotActive = $statistics['dashboard.videos.pending_count'];

        $usersDash = json_decode($statistics['dashboard.usrs.cahrt'], 0);
        $userDashCount = $statistics['dashboard.usrs.count'];

        $videosCategory = json_decode($statistics['dashboard.video_categories'], 0);
        $totalImages = 0;
        foreach ($videosCategory as $videosCategoryItem) {
            $totalImages += $videosCategoryItem->videos_count;
        }

        $videosCategoryPaying = json_decode($statistics['dashboard.videos.paying_per_category'], 0);

        $totalImagesPaying = 0;
        foreach ($videosCategoryPaying as $videosCategoryPayingItem) {
            $totalImagesPaying += $videosCategoryPayingItem->videos_count;
        }

        $categories = VideoCategory::where('mode', 'on')->get();
        if ($request->category_id || $request->from_date) {
            $top_downloading = Video::tinySelection()->withCount(['downloads' => function ($q) use ($request) {
                if ($request->from_date)
                    $q->where('downloads.created_at', '>=', Carbon::parse($request->from_date));
                if ($request->to_date)
                    $q->where('downloads.created_at', '<=', Carbon::parse($request->to_date));
            }])->where(function ($q) {
                if (request('category_id'))
                    $q->whereHas('category', function ($q) {
                        $q->where('video_categories.id', request('category_id'));
                    });
            })->orderBy('downloads_count', 'desc')->take(5)->get();
        } else
            $top_downloading = collect(json_decode($statistics['dashboard.videos.top_downloading'], 0));
        $html_breadcrumbs = [
            'title' => __('views.Dashboard'),
            'subtitle' => __('views.Index'),
        ];

        $is_videos_site = true;
        $last_update = $statistics['last_update'];
        return view(
            'admin_v2_videos.dashboard.index',
            compact(
                'is_videos_site',
                'html_breadcrumbs',
                'videos',
                'videosActive',
                'videosnotActive',
                'purchases',
                'purchases_count',
                'usersDash',
                'userDashCount',
                'videosCategory',
                'totalImages',
                'totalImagesPaying',
                'videosCategoryPaying',
                'categories',
                'top_downloading',
                'last_update'
            )
        );
    }


    public function index_vectors(Request $request)
    {

        $val = 0;
        $paying = \App\Models\VectorDownload::select(
            DB::raw('count(id) as `data`'),
            DB::raw("DATE_FORMAT(date, '%m-%Y') new_date"),
            DB::raw('YEAR(date) year, MONTH(date) month'),
            DB::raw('MONTHNAME(date) name')
        )->groupby('year', 'month')
            ->orderby('year', 'asc')
            ->orderby('month', 'asc')
            ->whereHas('plan', function ($q) use ($val) {
                $q->where('free', $val);
            })
            ->where('user_id', '!=', 0)
            ->get();


        $payingCount = \App\Models\VectorDownload::where('id', '>', 0)
            ->whereHas('plan', function ($q) use ($val) {
                $q->where('free', $val);
            })
            ->where('user_id', '!=', 0)
            ->count();


        $imges = Vector::select(
            DB::raw('count(id) as `data`'),
            DB::raw("DATE_FORMAT(date, '%m-%Y') new_date"),
            DB::raw('YEAR(date) year, MONTH(date) month'),
            DB::raw('MONTHNAME(date) name')
        )->groupby('year', 'month')
            ->orderby('year', 'asc')
            ->orderby('month', 'asc')
            ->get();


        $imgesActive = Vector::where('status', 'active')->count();
        $imgesnotActive = Vector::where('status', 'pending')->count();

        $usersDash = User::select(
            DB::raw('count(id) as `data`'),
            DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month'),
            DB::raw('MONTHNAME(created_at) nameMo')
        )->groupby('year', 'month')
            ->orderby('year', 'asc')
            ->orderby('month', 'asc')
            ->get();


        $imagesCategory = ImageCategory::where('image_categories.id', '!=', 1)
            ->leftJoin(
                'category_vector',
                'category_vector.category_id', '=', 'image_categories.id')->select(
                DB::raw('count(category_vector.vector_id) as `data`'),
                'name_' . app()->getLocale()
            )->groupby('category_vector.category_id')
            ->limit(5)
            ->get();


        $totalImages = 0;
        foreach ($imagesCategory as $imagesCategoryItem) {
            $totalImages += $imagesCategoryItem->data;
        }

        $imagesCategoryPaying = ImageCategory::where('image_categories.id', '!=', 1)
            ->leftJoin(
                'category_vector',
                'category_vector.category_id',
                '=',
                'image_categories.id'
            )
            ->leftJoin(
                'vector_downloads',
                'category_vector.vector_id',
                '=',
                'vector_downloads.vector_id'
            )
            ->select(
                DB::raw('count(vector_downloads.vector_id) as `data`'),
                'name_' . app()->getLocale(),
                'vector_downloads.*',
                'image_categories.id'
            )
            ->groupby('category_id')
            ->limit(5)
            ->get();

        $totalImagesPaying = 0;
        foreach ($imagesCategoryPaying as $imagesCategoryPayingItem) {
            $totalImagesPaying += $imagesCategoryPayingItem->data;
        }

        $userDashCount = User::where('id', '>', 0)->count();

        $adminCollection = AdminCollection::where('status', 1)->get();

        $images_ids = AdminCollectionVector::query();
        $collection_name = '';
        if ($request->get('admin_collection_id')) {
            $images_ids = $images_ids->where(
                'admin_collection_id',
                $request->get('admin_collection_id')
            );
            $collection_name = AdminCollection::findOrFail(
                $request->get('admin_collection_id')
            );
        }
        $images_ids = $images_ids->pluck('vector_id');
        $imagesD = \App\Models\VectorDownload::whereIn(
            'vector_downloads.vector_id',
            $images_ids->toArray()
        );
        $query = $imagesD
            ->Join('vectors', 'vector_downloads.vector_id', '=', 'vectors.id')
            ->select(
                DB::raw('count(vector_downloads.vector_id) as `data`'),
                'vectors.*'
            );

        if ($request->get('from_date') && $request->get('to_date')) {
            $query->whereDate(
                'vector_downloads.date',
                '>=',
                Carbon::parse($request->get('from_date'))
            );
            $query->whereDate(
                'vector_downloads.date',
                '<=',
                Carbon::parse($request->get('to_date'))
            );
        }

        $query = $query
            ->groupBy('vector_downloads.vector_id')
            ->orderBy('data', 'desc')
            ->limit(5)
            ->get();

        $html_breadcrumbs = [
            'title' => __('views.Dashboard'),
            'subtitle' => __('views.Index'),
        ];
        $is_vectors_site = true;
        return view(
            'admin_v2_vectors.vectors.dashboard.index',
            compact(
                'html_breadcrumbs',
                'is_vectors_site',
                'imges',
                'imgesActive',
                'imgesnotActive',
                'paying',
                'payingCount',
                'usersDash',
                'userDashCount',
                'imagesCategory',
                'totalImages',
                'totalImagesPaying',
                'imagesCategoryPaying',
                'adminCollection',
                'query',
                'collection_name'
            )
        );
    }

    public function index_models(Request $request)
    {

        $html_breadcrumbs = [
            'title' => __('views.Dashboard'),
            'subtitle' => __('views.Index'),
        ];

        $is_models_site = true;

        return view(
            'admin_v2.models.dashboard.index',
            compact(
                'is_models_site'
            )
        );
    }

    public function lang(Request $request, $lang)
    {
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'ar';
        }
        \Session::put('locale', $lang);
        return back();
    }

    public function update_statistics()
    {
        Artisan::call('statistics:update');
        return redirect()->back();
    }
}
