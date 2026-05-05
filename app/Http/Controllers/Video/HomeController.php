<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\SearchImage;
use App\Models\Tag;
use App\Models\VideoSearchKey;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminVideoSettings;
use App\Models\VideoCategory;
use App\Models\Query;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $typePage = 'videos';
        $top_categories = VideoCategory::where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->get();

        $categories = VideoCategory::where('in_home', 1)
            ->where('mode', 'on')
            ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->paginate(12);

        $categoriesTrending = VideoCategory::where('show_in_trending_list', 1)
            ->orderBy('sort')
            ->orderBy('name_en')->limit(6)->get();


        \DB::enableQueryLog();
        $categoriesCities = VideoCategory::where('cities_and_landmarks', 'on')
            ->where('mode', 'on')
            //  ->where('cities_and_landmarks', 'on')
            ->where('in_home', 1)
            ->orderBy('sort')
            ->orderBy('name_en')->paginate(12);

        $categoriesPeople = VideoCategory::where('in_home', 1)
            ->where('mode', 'on')
            //  ->where('in_home', 1)
            ->where('people', '1')
            ->orderBy('sort')
            ->orderBy('name_en')->limit(4)->get();

        $videos = Query::latestVideos(16);

        $local = app()->getLocale();
        $search_word_images = cache()->remember("image_search_keys_{$local}", now()->addDay(), function () use ($local) {
            return DB::table('image_search_keys')
                ->select('key_word', DB::raw('MAX(count) as max_count'))
                ->where('lang', $local)
                ->orderByDesc('max_count')
                ->groupBy('key_word')->limit(10)->get();
        });
        $search_word_videos = cache()->remember("video_search_keys_{$local}", now()->addDay(), function () use ($local) {
            return DB::table('video_search_keys')
                ->select('key_word', DB::raw('MAX(count) as max_count'))
                ->where('lang', $local)
                ->orderByDesc('max_count')
                ->groupBy('key_word')->limit(10)->get();
        });

        $search_word_vectors = cache()->remember("vector_search_keys_{$local}", now()->addDay(), function () use ($local) {
            return DB::table('vector_search_keys')
                ->select('key_word', DB::raw('MAX(count) as max_count'))
                ->where('lang', $local)
                ->orderByDesc('max_count')
                ->groupBy('key_word')->limit(10)->get();;
        });


        $video_tags = video_settings();
        $col_name = 'tags_' . app()->getLocale() . '_in_home';
        $tag = explode(',', $video_tags->$col_name);
//        $canReserve = \App\Models\Video::active()->tinySelection()->withoutGlobalScope('reserved')->canReserve()->whereNull('parent_id')
//            ->inRandomOrder(session()->get('random_order'))->take(15)->get();
        $canReserve = collect([]);
        return view('video.index.home', [
            'top_categories' => $top_categories,
            'typePage' => $typePage,
            'categories' => $categories,
            'videos' => $videos,
            'search_word_videos' => $search_word_videos,
            'search_word_vectors' => $search_word_vectors,
            'search_word_images' => $search_word_images,
            'tag' => $tag,
            'categoriesTrending' => $categoriesTrending,
            'categoriesCities' => $categoriesCities,
            'categoriesPeople' => $categoriesPeople,
            'canReserve' => $canReserve,
        ]);

    }// End Method

    public function getVerifyAccount($confirmation_code)
    {


        if (Auth::guest()
            || Auth::check()
            && Auth::user()->activation_code == $confirmation_code
            && Auth::user()->status == 'pending'
        ) {
            $user = User::where('activation_code', $confirmation_code)->where('status', 'pending')->first();

            if ($user) {

                $update = User::where('activation_code', $confirmation_code)
                    ->where('status', 'pending')
                    ->update(['status' => 'active', 'activation_code' => '']);


                Auth::loginUsingId($user->id);

                return redirect('/')
                    ->with([
                        'success_verify' => true,
                    ]);
            } else {
                return redirect('/')
                    ->with([
                        'error_verify' => true,
                    ]);
            }
        } else {
            return redirect('/');
        }
    }// End Method

    public function getSearch($q, Request $request)
    {

        $q = str_replace('++', 'XBBB', $q);
        $q = str_replace('+', 'XCC', $q);
        $q = htmlentities($q);
        $q = str_replace('XCC', ' ', $q);
        $q = str_replace('XBBB', '+', $q);
        $q = filterSearchKeyword($q);
        $word = $q;
        $q = mb_strtolower($q);

        if ((filter_var($q, FILTER_VALIDATE_INT) !== false) && Video::whereNull('parent_id')->where('status', 'active')->find($q)) {
            $video = Video::find($q);
            return redirect()->to($video->post_link);
        } elseif (startsWith($q, 'V') == true || startsWith($q, 'v') == true) {
            $qu = strtoupper($q);
            $id = ltrim($qu, 'V');
            if ((filter_var($id, FILTER_VALIDATE_INT) !== false) && Video::find($id)) {
                $video = Video::find($id);
                return redirect()->to($video->post_link);
            }

        } elseif (mb_strlen($q) > 49) {
            return redirect()->route('video.home')->with('status', trans('misc.sentence_is_to_long'));
        }


        $videos = \App\Helper::search_in_elasticsearch('videos', $q, ['sort_categories_at_last' => [71]], 100);
        $videos = [
            'videos' => ['images' => $videos],
            'page' => $request->get('page', 1),
            'title' => trans('misc.result_of') . ' ' . $word . ' - ',
            'total' => $videos->total(),
            'q' => $word,
        ];

        if ($q == '' || mb_strlen($q) <= 1) {
            return redirect('/');
        }

        if ($videos['total'] > 0) {
            $searchCheck = VideoSearchKey::where('key_word', '=', $q)->first();

            if ($searchCheck) {
                $searchCheck->count = $searchCheck->count + 1;
                $searchCheck->save();
            } else {
                $search = VideoSearchKey::create([
                    'key_word' => $q,
                    'count' => 1,
                    'lang' => app()->getLocale(),
                ]);
            }
        }

//        if ($request->ajax()) {
//            $view = view('includes.categoryVideosAjax')->with([
//                'videos' => $videos['videos']['images']
//            ])->render();
//            if (count($videos) == 0) {
//                return response()->json(['html' => ""]);
//
//            }
//            return response()->json(['html' => $view, 'pagination' => $videos['videos']['images']->links('pagination.search-pagination') . ' ']);
//        }

        // check spelling and suggest term_alternative ?
        if ($videos['total'] === 0) {
            $videos['term_alternative'] = \App\Helper::check_spelling_in_elasticsearch('videos', $q);
            if ($videos['term_alternative'] === $q) {
                unset($videos['term_alternative']);
            }
        }

        $categories = $videos['total'] ? collect([]) : VideoCategory::where('in_home', 1)
            ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->paginate(12);
        $videos['categories'] = $categories;

        $video_tags = video_settings();
        $col_name = 'tags_' . app()->getLocale() . '_in_home'; // optimize
        $tags = explode(',', $video_tags->$col_name);

        $videos['tags'] = $tags;
        $videos['word'] = $q;
// check spelling and suggest term_alternative ?
        if ($videos['total'] === 0) {
            $videos['term_alternative'] = \App\Helper::check_spelling_in_elasticsearch('videos', $q);
            if ($videos['term_alternative'] === $q) {
                unset($videos['term_alternative']);
            }
        }
        return view('video.default.search')->with($videos);
    }// End Method

    public function latest(Request $request)
    {
        $videos = Query::latestVideos();
        return view('video.index.latest', ['videos' => $videos, 'total' => $videos->total()]);

    }// End Method

    public function can_reserve(Request $request)
    {
        if (!session()->has('random_order'))
            session()->put('random_order', rand());
        $videos = \App\Models\Video::active()->tinySelection()->withoutGlobalScope('reserved')->canReserve()->whereNull('parent_id')->inRandomOrder(session()->get('random_order'))->paginate(30);
        if ($request->ajax()) {
            $view = view('video.ajax.videos-ajax', compact('videos'))->render();
            if (count($videos) == 0) {
                return response()->json(['html' => ""]);
            }
            return response()->json(['html' => $view]);
        }
        return view('video.index.can-reserve', ['videos' => $videos]);
    }

    public function popular()
    {

        $images = Query::popularVideos();

        return view('video.index.popular', ['videos' => $images]);

    }// End Method

    public function commented()
    {

        $images = Query::commentedImages();

        return view('index.commented', ['videos' => $images]);

    }// End Method

    public function viewed()
    {

        $videos = Query::viewedVideos();


        return view('video.index.viewed', ['videos' => $videos]);

    }// End Method

    public function downloads()
    {

        $images = Query::downloadsVideos();

        return view('video.index.downloads', ['videos' => $images]);

    }// End Method


    public function categories()
    {
        $data = new \App\Models\VideoCategory();
        $data = $data->where('mode', 'on')
            ->orderBy('sort')
            ->orderBy('name_en')->get();
        $setting = video_settings();

        $categoriesCities = new \App\Models\VideoCategory();
        $categoriesCities = $categoriesCities->where('mode', 'on')
            ->orderBy('sort')
            ->where('cities_and_landmarks', 'on')->first();

        $categoriesPeople = new \App\Models\VideoCategory();
        $categoriesPeople = $categoriesPeople->where('mode', 'on')
            ->orderBy('sort')
            ->where('people', '=', '1')->first();

        return view('video.default.categories', compact('data', 'categoriesPeople', 'categoriesCities', 'setting'));
    }


    public function category($slug, Request $request)
    {
        $videos = Query::categoryVideo($slug, null, 100);
        $setting = video_settings();
        $category = VideoCategory::where('slug', '=', $slug)->firstOrFail();
        $total = $videos["total"];
        return view('video.default.category', compact('videos', 'category', 'total', 'setting'));

    }// End Method


    public function tag($slug, Request $request)
    {
        if (mb_strlen($slug) > 0) {
            $tag = Tag::where('slug', $slug)->first();
            if ($tag) {
                $tag = $tag->title;
                $results = \App\Helper::search_in_elasticsearch('videos', $tag, [], 106);

            }
        }
        if (!isset($results)) {
            $results = collect([]);
            $tag = $slug;
        }
        return view('video.default.tags-show', compact('results', 'tag'));
    }

    public function cameras($slug)
    {

        if (mb_strlen($slug) > 3) {
            $settings = video_settings();

            $images = Query::camerasImages($slug);

            return view('default.cameras')->with($images);

        } else {
            abort('404');
        }
    }// End Method

    public function colors($slug)
    {

        if (mb_strlen($slug) == 6) {

            $settings = video_settings();

            $images = Query::colorsImages($slug);

            return view('default.colors')->with($images);

        } else {
            abort('404');
        }
    }// End Method

    public function collections(Request $request)
    {


        $settings = video_settings();

        $title = trans('misc.collections') . ' - ';

        $data = Collection::has('collection_videos')->selectRaw('collections.*,(select thumbnail from videos join collection_videos on (collection_videos.video_id=videos.id) where collection_videos.collection_id = collections.id  and videos.status = "active" limit 1 ) AS thumbnail')
            ->orderBy('collections.id', 'desc')
            ->groupBy('collections.id')
            ->paginate($settings->result_request);


        if ($request->input('page') > $data->lastPage()) {
            abort('404');
        }

        return view('video.default.collections', compact('title', 'data'));
    }//<--- End Method

    public function ris(Request $request, $hash = '')
    {
        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'image' => ['required', 'file', 'max:20480', 'mimes:jpg,jpeg,png,mp4,avi']
            ], [
                'image.max' => str_replace(['{{filesize}}', '{{maxFilesize}}'], [intval(formatBytes($request->file('image')->getSize())), 20], __('dropzone.dictFileTooBig'))
            ]);
            if ($validator->fails())
                return ['status' => 0, 'message' => $validator->errors()->first()];

            $file = $request->file('image');
            $path = $file->hashName('uploads/search-images');
            $hash = Str::random(50);
            Storage::disk('s3')->put($path, $file->get());
            $search_image = SearchImage::create([
                'user_id' => auth()->id(),
                'path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'hash' => $hash,
                'ip' => $request->ip(),
            ]);
            return ['status' => 1, 'redirect' => route('video.ris', $search_image->hash)];
        }
        $search_image = SearchImage::whereHash($hash)->firstOrFail();
        $ids = cache()->remember("ris_$search_image->hash", now()->addDay(), function () use ($search_image) {
            $url = urlencode(cdn($search_image->path));
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('services.search_by_image.url') . "/video-search/get_ids?number_required=1000&url={$url}&key_token=" . config('services.search_by_image.key'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            if ($response) {
                $ids = json_decode($response);
                return DB::table('videos')->whereNull('deleted_at')->where('status', 'active')->whereNull('parent_id')->whereIn('id', $ids)->pluck('id')->toArray();;
            }
            return [];
        });
        $ids = paginate_array($ids, 60);
        $videos = Video::where('status', 'active')->whereNull('parent_id')->whereIn('id', $ids->items())->get()->keyBy('id');
        $results = collect([]);
        foreach ($ids as $id)
            if ($videos->get($id))
                $results->push($videos->get($id));
        return view('video.videos.ris', compact('search_image', 'results', 'ids'));
    }

}
