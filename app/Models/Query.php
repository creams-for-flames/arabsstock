<?php

namespace App\Models;


use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input as Input;

class Query extends Model
{

    protected $guarded = [];
    public $timestamps = false;


    public static function users()
    {

        $settings = image_settings();

        $page = Input::get('page');
        $sort = Input::get('sort');
        $location = Input::get('location');

        if ($sort == 'latest') {
            $sortQuery = 'users.id';
        } else {
            if ($sort == 'photos') {
                $sortQuery = 'COUNT(images.id)';
            } else {
                $sortQuery = 'COUNT(followers.id)';
            }
        }

        $data = User::where('users.status', 'active');

        // lOCATION
        if (isset($location) && $location != '') {
            $data->where('users.country_id', $location);
        }

        // PHOTOS
        if ($sort == 'photos') {
            $data->leftjoin('images', 'users.id', '=', \DB::raw('images.user_id AND images.status = "active"'));
        }

        // POPULAR
        if ($sort == 'popular' || !$sort) {
            $data->leftjoin('followers', 'users.id', '=', \DB::raw('followers.following AND followers.status = "1"'));
        }

        $query = $data->where('users.status', '=', 'active')
            ->groupBy('users.id')
            ->orderBy(\DB::raw($sortQuery), 'DESC')
            ->orderBy('users.id', 'ASC')
            ->select('users.*')
            ->paginate($settings->result_request);

        return ['data' => $query, 'page' => $page, 'sort' => $sort, 'location' => $location];

    }


    public static function latestImages($perpage = 40)
    {
        $rand = cache()->remember('rand_id', now()->addHour(), function () {
            return rand();
        });
        $route_name = request()->route()->getName();
        $data = cache()->tags(['image', 'contributor'])->remember("latestImages_{$rand}_{$route_name}_" . request('page', 1), now()->addHour(), function () use ($route_name, $perpage, $rand) {
            $ContributorImageId = DB::table('contributors')->where('show_land_images', true)->pluck('id');
            $query = Image::where('images.status', 'active')
                ->where(function ($q) use ($ContributorImageId) {
                    $q->whereIn('images.user_id', $ContributorImageId)->orWhere('images.contributor_image_id', 0);
                });
            $last = clone $query;
            $latest_count = DB::table('admin_image_settings')->first()->latest_count;
            $last = $last->select('images.id')->whereDoesntHave('category', function ($query) {
                $query->whereIn('image_categories.id', [84]);
            })->orderBy('id', 'desc')->offset($latest_count)->first();
            $data = $query->tinySelection()->where('id', '>', $last->id)->whereDoesntHave('category', function ($query) {
                $query->whereIn('image_categories.id', [84]);
            });
            if ($route_name == 'landPage')
                $data->inRandomOrder();
            else
                $data->inRandomOrder($rand);
            $data = $data->paginate($perpage);
            return $data;
        });

        return $data;

    }

    public static function latestVideos($perpage = 40)
    {
        $rand = cache()->remember('rand_id', now()->addHour(), function () {
            return rand();
        });
        $route_name = request()->route()->getName();
        return cache()->tags(['video', 'contributor'])->remember("latestVideos_{$rand}_{$route_name}_" . request('page', 1), now()->addHour(), function () use ($route_name, $perpage, $rand) {
            $ContributorVideoId = DB::table('contributors')->where('show_land_videos', true)->pluck('id');
            /**@var $query \Illuminate\Database\Eloquent\Builder */
            $query = Video::where('videos.status', 'active')
                ->where('videos.video_fail', 0)
                ->where('videos.parent_id', null)
                ->where(function ($q) use ($ContributorVideoId) {
                    $q->whereIn('videos.user_id', $ContributorVideoId)->orWhere('videos.contributor_video_id', 0);
                });
            $last = clone $query;
            $latest_count = video_settings()->latest_count;
            $last = $last->select('videos.id')->whereDoesntHave('category', function ($query) {
                $query->whereIn('video_categories.id', [71, 60]);
            })->orderBy('id', 'desc')->offset($latest_count)->first();
            $data = $query->tinySelection()->where('id', '>', $last->id)->whereDoesntHave('category', function ($query) {
                $query->whereIn('video_categories.id', [71, 60]);
            });
            if (request()->route()->getName() == 'landPage')
                $data->inRandomOrder($rand);
            else
                $data->inRandomOrder($rand);
            $data = $data->paginate($perpage);
            return $data;
        });

    }


    public static function latestVectors($perpage = 40)
    {
        $rand = cache()->remember('rand_id', now()->addHour(), function () {
            return rand();
        });
        $route_name = request()->route()->getName();
        return cache()->tags(['vector', 'contributor'])->remember("latestVectors_{$rand}_{$route_name}_" . request('page', 1), now()->addHour(), function () use ($route_name, $perpage, $rand) {
            $ContributorVectorId = DB::table('contributors')->where('show_land_vectors', true)->pluck('id');
            /**@var $query \Illuminate\Database\Eloquent\Builder */
            $query = Vector::where('vectors.status', 'active')
                ->where(function ($q) use ($ContributorVectorId) {
                    $q->whereIn('vectors.user_id', $ContributorVectorId)->orWhere('vectors.contributor_vector_id', 0);
                });
            $last = clone $query;
            $latest_count = DB::table('admin_vector_settings')->first()->latest_count;
            $last = $last->select('vectors.id')->orderBy('id', 'desc')->offset($latest_count)->first();
            $data = $query->tinySelection()->where('id', '>', $last->id);
            if (request()->route()->getName() == 'landPage')
                $data->inRandomOrder();
            else
                $data->inRandomOrder($rand);
            $data = $data->paginate($perpage);
            return $data;
        });

    }

    public static function randomImages()
    {

        $settings = image_settings();
        $ContributorImageId = DB::table('contributors')->where('show_land_images', true)->pluck('id');
        $ContributorImageId[] = 0;
        $data = Image::whereHas('category', function ($query) use ($ContributorImageId) {
            $query->where('image_categories.in_random_home_image', '1');
        })->where('images.status', 'active')
            ->whereDate('images.date', '>', Carbon::today()->subMonths(2))
            ->whereHas('adminCollection', function ($query) {
                return $query->where('admin_collections.in_random_home', null)
                    ->orwhere('admin_collections.in_random_home', '1');
            })
            ->orWhereIn('images.user_id', $ContributorImageId)
            ->groupBy('images.id')
            ->inRandomOrder()
            ->select(
                'images.id',
                'images.title_ar',
                'images.title_en',
                'images.sort',
                'images.thumbnail',
                'images.height_thumbnail',
                'images.width_thumbnail'
            )
            ->paginate($settings->result_request);


        return $data;

    }

    public static function modernImages()
    {
        $data = Image::with('category')->where('status', 'active')
            ->select('images.*')
            ->where('in_home', 1)
            ->orderBy('images.id', 'desc')
            ->groupBy('images.id')->inRandomOrder()->take(6)->get();

        return $data;

    }

    public static function randomVideos()
    {

        $settings = video_settings();
        $ContributorVideoId = Contributor::where('show_land_videos', true)->pluck('id');
        $ContributorVideoId[] = 0;
        $data = Video::whereHas('category', function ($query) use ($ContributorVideoId) {
            $query->where('video_categories.in_random_home_video', '1');
        })->where('videos.status', 'active')
            ->where('videos.video_fail', 0)
            ->whereDate('videos.date', '>', Carbon::today()->subMonths(2))
            ->whereHas('adminCollection', function ($query) {
                $query->where('admin_collections.in_random_home', null)
                    ->orwhere('admin_collections.in_random_home', '1');
            })
            ->orWhereIn('videos.user_id', $ContributorVideoId)
            ->where('videos.is_uploaded', 1)
            ->groupBy('videos.id')
            ->where('videos.parent_id', null)
            ->inRandomOrder()
            ->paginate($settings->result_request);

        return $data;

    }


    public static function randomVectors()
    {
        $settings = vector_settings();
        $ContributorVectorId = Contributor::where('show_land_vectors', true)->pluck('id');
        $ContributorVectorId[] = 0;
        $data = Vector::whereHas('category', function ($query) use ($ContributorVectorId) {
            $query->where('vector_categories.in_random_home_vector', '1');
        })->where('vectors.status', 'active')
            ->whereDate('vectors.date', '>', Carbon::today()->subMonths(5))
            ->has('adminCollection')
            ->orWhereIn('vectors.user_id', $ContributorVectorId)
            ->groupBy('vectors.id')
            ->inRandomOrder()
            ->paginate($settings->result_request);

        return $data;

    }


    public static function featuredImages()
    {

        $settings = image_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Image::where('featured', 'yes')->where('status', 'active')->select('images.*')
            ->orderBy('images.featured_date', 'DESC')->groupBy('images.id')->paginate($settings->result_request);

        return $data;

    }


    public static function popularImages()
    {

        $settings = image_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Image::select('images.*')
            ->join('likes', function ($join) {
                $join->on('likes.image_id', '=', 'images.id')->where('likes.status', '=', '1')->where('images.status',
                    'active');
            })
            ->groupBy('likes.image_id')
            ->orderBy(\DB::raw('COUNT(likes.image_id)'), 'desc')
            ->paginate($settings->result_request);


        return $data;

    }


    public static function popularVideos()
    {

        $settings = video_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Video::select('videos.*')
            ->where('videos.video_fail', 0)
            ->join('video_likes', function ($join) {
                $join->on('video_likes.video_id', '=', 'videos.id')->where('video_likes.status', '=',
                    '1')->where('videos.status',
                    'active');
            })
            ->groupBy('video_likes.video_id')
            ->orderBy(\DB::raw('COUNT(video_likes.video_id)'), 'desc')
            ->paginate($settings->result_request);


        return $data;

    }

    public static function commentedImages()
    {

        $settings = image_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Image::select('images.*')
            ->join('comments', 'images.id', '=', 'comments.image_id')
            ->where('images.status', 'active')
            ->groupBy('comments.image_id')
            ->orderBy(\DB::raw('COUNT(comments.image_id)'), 'desc')
            ->paginate($settings->result_request);

        return $data;

    }


    public static function commentedVideos()
    {

        $settings = video_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Video::select('videos.*')
            ->where('videos.video_fail', 0)
            ->join('comments', 'videos.id', '=', 'comments.video_id')
            ->where('videos.status', 'active')
            ->groupBy('comments.video_id')
            ->orderBy(\DB::raw('COUNT(comments.video_id)'), 'desc')
            ->paginate($settings->result_request);

        return $data;

    }


    public static function viewedImages()
    {

        $settings = image_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Image::select('images.*')->join('visits', 'images.id', '=', 'visits.image_id')
            ->where('images.status', 'active')
            ->groupBy('visits.image_id')
            ->orderBy(\DB::raw('COUNT(visits.image_id)'), 'desc')
            ->paginate($settings->result_request);

        return $data;

    }


    public static function viewedVideos()
    {

        $settings = video_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Video::select('videos.*')
            ->where('videos.video_fail', 0)
            ->join('visit_videos', 'videos.id', '=', 'visit_videos.video_id')
            ->where('videos.status', 'active')
            ->groupBy('visit_videos.video_id')
            ->orderBy(\DB::raw('COUNT(visit_videos.video_id)'), 'desc')
            ->paginate($settings->result_request);

        return $data;

    }

    public static function downloadsImages()
    {

        $settings = image_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Image::where('status', 'active')->select('images.*')
            ->join('downloads', 'images.id', '=', 'downloads.image_id')
            ->orderBy(\DB::raw('COUNT(downloads.image_id)'),
                'desc')->groupBy('downloads.image_id')->paginate($settings->result_request);

        return $data;

    }


    public static function downloadsVectors()
    {

        $settings = vector_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Vector::where('status', 'active')->select('vectors.*')
            ->join('vector_downloads', 'vectors.id', '=', 'vector_downloads.vector_id')
            ->orderBy(\DB::raw('COUNT(vector_downloads.vector_id)'),
                'desc')->groupBy('vector_downloads.vector_id')->paginate($settings->result_request);

        return $data;

    }


    public static function downloadsVideos()
    {

        $settings = video_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $data = Video::where('status', 'active')
            ->where('videos.video_fail', 0)->select('videos.*')
            ->join('video_downloads', 'videos.id', '=', 'video_downloads.video_id')
            ->orderBy(\DB::raw('COUNT(video_downloads.video_id)'),
                'desc')->groupBy('video_downloads.video_id')->paginate($settings->result_request);

        return $data;

    }

    public static function categoryImages($slug, $type = null, $perpage = 0)
    {
        $category = ImageCategory::where('slug', $slug)->firstOrFail();
        $images = cache()->tags(['category', 'image'])->remember("image_category_{$category->id}_{$perpage}perpage_" . request('page', 1), now()->addHours(3), function () use ($category, $perpage) {
            return $category->images()->where('images.status', 'active')->inRandomOrder()->paginate(($perpage ?: 20));
        });
        return ['images' => $images, 'category' => $category];

    }


    public static function categoryVideo($slug, $type = null, $perpage = 0)
    {

        $category = VideoCategory::where('slug', $slug)->firstOrFail();
        $videos = cache()->tags(['category', 'video'])->remember("video_category_{$category->id}_{$perpage}perpage_" . request('page', 1), now()->addHours(3), function () use ($category, $perpage) {
            return $category->videos()->where('videos.status', 'active')->whereNull('parent_id')->inRandomOrder()->paginate(($perpage ?: 20));
        });
        return ['images' => $videos, 'category' => $category, 'total' => $videos->total()];
    }


    public static function tagsImages2($tags, $perpage = 20)
    {


        //dd(get_soundex($tags));
        $settings = image_settings();

        $page = Input::get('page');

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $images = Image::whereIn('id', function ($q) use ($tags) {
            $q->select('image_id')->from('image_tags')->where('image_tags.local', app()->getLocale())
                ->whereIn('image_tags.slug', [get_soundex($tags), $tags]);
        })
            ->orderBy('sort')
            ->paginate($perpage);
        $title = trans('misc.tags') . ' - ' . str_replace('-', ' ', ucfirst($tags));

        $total = $images->total();


        return ['images' => $images, 'title' => $title, 'total' => $total, 'tags' => $tags];

    }


    public static function tagsVideo($tags, $size = 0)
    {
        $settings = video_settings();

        $page = Input::get('page');

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        \DB::enableQueryLog();
        $images = Video::join('video_tags', 'videos.id', 'video_tags.video_id')
            ->where('videos.video_fail', 0)
            ->where('video_tags.slug', $tags)
            ->where('video_tags.local', app()->getLocale())
            ->select('videos.*')
            ->orderBy('sort')
            ->paginate(($size ?: $settings->result_request));

        $title = trans('misc.tags') . ' - ' . str_replace('-', ' ', ucfirst($tags));

        $total = $images->total();

        return ['images' => $images, 'title' => $title, 'total' => $total, 'tags' => $tags];

    }

    public static function tagsVector($tags, $perpage = 20)
    {

        $settings = vector_settings();

        $page = Input::get('page');

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        \DB::enableQueryLog();

        $data = Vector::join('vectors_tags', 'vectors.id', 'vectors_tags.vector_id')
            ->where('vectors.status', 'active')
            ->where('vectors.is_uploaded', 1)
            ->where('vectors_tags.slug', $tags)
            ->where('vectors_tags.local', app()->getLocale())
            ->select('vectors.*')
            ->orderBy('sort')
            ->paginate($perpage);

        $title = trans('misc.tags') . ' - ' . str_replace('-', ' ', ucfirst($tags));

        $total = $data->total();

        return ['vectors' => $data, 'title' => $title, 'total' => $total, 'tags' => $tags];

    }

    public static function camerasImages($camera)
    {

        $settings = image_settings();

        $page = Input::get('page');

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $images = Image::where('camera', 'LIKE', '%' . $camera . '%')->where('status', 'active')->select('images.*')
            ->orderBy('images.id', 'DESC')->groupBy('images.id')->paginate($settings->result_request);

        $title = trans('misc.photos_taken_with') . ' ' . ucfirst($camera);

        $total = $images->total();

        return ['images' => $images, 'title' => $title, 'total' => $total, 'camera' => $camera];

    }

    public static function colorsImages($colors)
    {

        $settings = image_settings();

        $page = Input::get('page');

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $images = Image::where('colors', 'LIKE', '%' . $colors . '%')->where('status', 'active')->select('images.*')
            ->orderBy('images.id', 'DESC')->groupBy('images.id')->paginate($settings->result_request);


        $title = trans('misc.colors') . ' #' . $colors;

        $total = $images->total();

        return ['images' => $images, 'title' => $title, 'total' => $total, 'colors' => $colors];

    }

    public static function userImages($id)
    {

        $settings = image_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $images = Image::whereHas('user')->where('images.user_id', $id)->where('status', 'active')->select('images.*')
            ->orderBy('images.id', 'DESC')->groupBy('images.id')->paginate($settings->result_request);

        return $images;

    }

    public static function userVideos($id)
    {


        $settings = video_settings();

        $user_id = 0;
        if (\Auth::check()) {
            $user_id = \Auth::id();
        }

        $images = Video::where('videos.user_id', $id)
            ->where('videos.video_fail', 0)->where('status', 'active')->select('videos.*')
            ->orderBy('videos.id', 'DESC')->groupBy('videos.id')->paginate($settings->result_request);

        return $images;

    }


    public static function user_subscription_remaining()
    {

        $user_subscription_remaining = '';
        $days_left = '';
        $title_plan = 'images';
        if (auth()->check()) {


            $user_subscription_remaining = auth()->user()->subscriptions_active()->with('plan')->orderBy('created_at')->first();

            if ($user_subscription_remaining) {
                $fdate = $user_subscription_remaining->starts_at;
                $tdate = now();
                $datetime1 = new DateTime($fdate);
                $datetime2 = new DateTime($tdate);
                $interval = $datetime1->diff($datetime2);
                $days_left = $interval->format('%a');//now do whatever you like with $days


                return [
                    'user_subscription_remaining' => $user_subscription_remaining,
                    'days_left' => $days_left,
                    'title_plan' => $title_plan
                ];

            }

        }
        return ['user_subscription_remaining' => null, 'days_left' => null, 'title_plan' => 'images'];


    }


    public static function user_subscription_remaining_video()
    {

        $user_subscription_remaining = '';
        $days_left = '';
        $title_plan = 'videos';
        if (auth()->check()) {


            $user_subscription_remaining = auth()->user()->active_video_subscriptions()->with('plan')->orderBy('created_at')->first();

            if ($user_subscription_remaining) {
                $fdate = $user_subscription_remaining->starts_at;
                $tdate = now();
                $datetime1 = new DateTime($fdate);
                $datetime2 = new DateTime($tdate);
                $interval = $datetime1->diff($datetime2);
                $days_left = $interval->format('%a');//now do whatever you like with $days


                return [
                    'user_subscription_remaining' => $user_subscription_remaining,
                    'days_left' => $days_left,
                    'title_plan' => $title_plan
                ];

            }

        }
        return ['user_subscription_remaining' => null, 'days_left' => null, 'title_plan' => 'images'];


    }


    public static function user_subscription_remaining_vector()
    {

        $user_subscription_remaining = '';
        $days_left = '';
        $title_plan = 'vectors';
        if (auth()->check()) {
            $user_subscription_remaining = auth()->user()->active_vector_subscriptions()->with('plan')->orderBy('created_at')->first();
            if ($user_subscription_remaining) {
                $fdate = $user_subscription_remaining->starts_at;
                $tdate = now();
                $datetime1 = new DateTime($fdate);
                $datetime2 = new DateTime($tdate);
                $interval = $datetime1->diff($datetime2);
                $days_left = $interval->format('%a');//now do whatever you like with $days
                return [
                    'user_subscription_remaining' => $user_subscription_remaining,
                    'days_left' => $days_left,
                    'title_plan' => $title_plan
                ];
            }
        }
        return ['user_subscription_remaining' => null, 'days_left' => null, 'title_plan' => 'images'];
    }


    public static function categoryVector($slug, $perpage = 0)
    {

        $category = VectorCategory::where('slug', $slug)->firstOrFail();
        $vectors = cache()->tags(['category', 'vector'])->remember("vector_category_{$category->id}_{$perpage}perpage_" . request('page', 1), now()->addHours(3), function () use ($category, $perpage) {
            return $category->vectors()->where('vectors.status', 'active')->inRandomOrder()->paginate(($perpage ?: 20));
        });
        return ['vectors' => $vectors, 'category' => $category, 'total' => $vectors->total()];

    }
}
