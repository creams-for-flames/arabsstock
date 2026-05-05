<?php

namespace App\Http\Controllers\Vectors;

use App\Models\AdminVectorSettings;
use App\Models\Image;
use App\Models\SearchImage;
use App\Models\Tag;
use App\Models\User;
use App\Models\Query;
use App\Models\VectorCategory;
use App\Models\Vector;
use App\Models\VectorSearchKey;
use App\Models\VectorPlan;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\VectorSubscription;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
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

        $typePage = 'vectors';

        $top_categories = VectorCategory::where('cities_and_landmarks', '!=', 'on')
            //where('mode', 'on')
            //  ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->get();


        $categories = VectorCategory::where('in_home', 1)
            ->where('mode', 'on')
            //    ->where('in_home', 1)
            ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->paginate(12);

        $vectors = Query::latestVectors();

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

        $settings = vector_settings();
        $col_name = 'tags_' . app()->getLocale() . '_in_home';
        $tag = explode(',', $settings->$col_name);
        if (!session()->has('random_order'))
            session()->put('random_order', rand());
//        $canReserve = \App\Models\Vector::active()->tinySelection()->canReserve()->take(15)->inRandomOrder(session()->get('random_order'))->get();
        $canReserve = collect([]);
        return view('vector.index.home', [
            'top_categories' => $top_categories,
            'typePage' => $typePage,
            'categories' => $categories,
            'vectors' => $vectors,
            'search_word_videos' => $search_word_videos,
            'search_word_vectors' => $search_word_vectors,
            'search_word_images' => $search_word_images,
            'tag' => $tag,
            'canReserve' => $canReserve,
        ]);

    }

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
    }

    public function getSearch($q, Request $request)
    {


        $word = '';
        $q = str_replace('++', 'XBBB', $q);
        $q = str_replace('+', 'XCC', $q);
        $q = htmlentities($q);
        $q = str_replace('XCC', ' ', $q);
        $q = str_replace('XBBB', '+', $q);
        $q = filterSearchKeyword($q);
        $word = $q;
        $q = strtolower($q);
        // dd($name = str_replace(' ', '+', preg_replace('/\s\s+/', ' ', $q)));
        if ((filter_var($q, FILTER_VALIDATE_INT) !== false) && Vector::where('status', 'active')->find($q)) {
            $image = Vector::find($q);
            return redirect()->to($image->post_link);
        } elseif (startsWith($q, 'I') == true || startsWith($q, 'i') == true) {
            $qu = strtoupper($q);
            $id = ltrim($qu, 'I');
            if ((filter_var($id, FILTER_VALIDATE_INT) !== false) && Vector::find($id)) {
                $image = Vector::find($id);
                return redirect()->to($image->post_link);
            }

        } elseif (mb_strlen($q) > 49) {
            return redirect()->route('landPage')->with('status', trans('misc.sentence_is_to_long'));
        }

        $images = \App\Helper::search_in_elasticsearch('vectors', $q, ['sort_categories_at_last' => [76]], 106);
        $categories = $images->total() ? collect([]) : VectorCategory::where('in_home', 1)
            ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->paginate(12);
        $images = [
            'images' => $images,
            'page' => $request->get('page', 1),
            'title' => trans('misc.result_of') . ' ' . $word . ' - ',
            'total' => $images->total(),
            'q' => $word,
        ];


        $image_tags = vector_settings();  // TODO optimize
        $col_name = 'tags_' . app()->getLocale() . '_in_home';
        $tags = explode(',', $image_tags->$col_name);

        //<--- * If $q is empty or is minus to 1 * ---->
        if ($q == '' || mb_strlen($q) <= 1) {
            return redirect('/');
        }

        if ($images['total'] > 0) {
            $searchCheck = VectorSearchKey::where('key_word', $q)->first();
            if ($searchCheck) {
                $searchCheck->count = $searchCheck->count + 1;
                $searchCheck->save();
            } else {
                $search = VectorSearchKey::create([
                    'key_word' => $q,
                    'count' => 1,
                    'lang' => app()->getLocale(),
                ]);
            }
        }

        $images['categories'] = $categories;
        $images['tags'] = $tags;

        // check spelling and suggest term_alternative ?
        if ($images['total'] === 0) {
            $images['term_alternative'] = \App\Helper::check_spelling_in_elasticsearch('vectors', $q);
            if ($images['term_alternative'] === $q) {
                unset($images['term_alternative']);
            }
        }
        $user_subscription_remaining_array = Query::user_subscription_remaining();
        $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
        $days_left = $user_subscription_remaining_array['days_left'];
        $title_plan = $user_subscription_remaining_array['title_plan'];
        $qq = $q;
        $word = $q;
        return view('vector.default.search', compact('user_subscription_remaining', 'days_left', 'title_plan', 'word'))->with($images);
    }

    public function latest(Request $request)
    {
        $vectors = Query::latestVectors();
        return view('vector.index.latest', ['vectors' => $vectors, 'total' => $vectors->total()]);
    }

    public function can_reserve(Request $request)
    {
        if (!session()->has('random_order'))
            session()->put('random_order', rand());
        $vectors = \App\Models\Vector::active()->tinySelection()->withoutGlobalScope('reserved')->canReserve()->inRandomOrder(session()->get('random_order'))->paginate(30);
        if ($request->ajax()) {
            $view = view('includes.CategoryVectorAjax', compact('vectors'))->render();
            if (count($vectors) == 0) {
                return response()->json(['html' => ""]);
            }
            return response()->json(['html' => $view]);
        }
        return view('vector.index.can-reserve', ['vectors' => $vectors]);
    }

    public function popular()
    {

        $images = Query::popularVideos();

        return view('video.index.popular', ['videos' => $images]);

    }

    public function commented()
    {

        $images = Query::commentedImages();

        return view('index.commented', ['videos' => $images]);

    }

    public function viewed()
    {

        $videos = Query::viewedVideos();


        return view('video.index.viewed', ['videos' => $videos]);

    }

    public function downloads()
    {

        $images = Query::downloadsVideos();

        return view('video.index.downloads', ['videos' => $images]);

    }


    public function categories()
    {
        $data = new \App\Models\VectorCategory();
        $data = $data->where('mode', 'on')
            ->orderBy('sort')
            ->orderBy('name_en')->get();
        $setting = vector_settings();

        $categoriesCities = new \App\Models\VectorCategory();
        $categoriesCities = $categoriesCities->where('mode', 'on')
            ->orderBy('sort')
            ->where('cities_and_landmarks', 'on')->first();

        $categoriesPeople = new \App\Models\VectorCategory();
        $categoriesPeople = $categoriesPeople->where('mode', 'on')
            ->orderBy('sort')
            ->where('people', '=', '1')->first();

        return view('vector.default.categories', compact('data', 'categoriesPeople', 'categoriesCities', 'setting'));
    }


    public function category($slug, Request $request)
    {
        \DB::enableQueryLog();
        $vectors = Query::categoryVector($slug, );
        $setting = vector_settings();
        $category = VectorCategory::where('slug', '=', $slug)->firstOrFail();
        $total = $vectors["total"];
        return view('vector.default.category', compact('vectors', 'category', 'total', 'setting'));

    }

    public function tag($slug, Request $request)
    {
        if (mb_strlen($slug) > 0) {
            $tag = Tag::where('slug', $slug)->first();
            if ($tag) {
                $tag = $tag->title;
                $results = \App\Helper::search_in_elasticsearch('vectors', $tag, [], 106);

            }
        }
        if (!isset($results)) {
            $results = collect([]);
            $tag = $slug;
        }
        return view('vector.default.tags-show', compact('results', 'tag'));
    }

    public function cameras($slug)
    {

        if (mb_strlen($slug) > 3) {
            $images = Query::camerasImages($slug);
            return view('default.cameras')->with($images);

        } else {
            abort('404');
        }
    }

    public function colors($slug)
    {
        if (mb_strlen($slug) == 6) {
            $images = Query::colorsImages($slug);
            return view('default.colors')->with($images);
        } else {
            abort('404');
        }
    }

    public function collections(Request $request)
    {


        $settings = vector_settings();

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


    public function vector()
    {
        $user_subscription_remaining = '';
        $days_left = '';

        $title_plan = 'vectors';


        $typePage = 'vectors';


        $top_categories = VectorCategory::where('cities_and_landmarks', '!=', 'on')
            ->where('mode', 'on')
            // ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->get();

        $categories = VectorCategory::where('in_home', 1)
            //where('mode', 'on')
            //    ->where('in_home', 1)
            ->where('cities_and_landmarks', '!=', 'on')
            ->where('people', '0')
            ->orderBy('sort')
            ->orderBy('name_en')->paginate(12);

        $categoriesTrending = VectorCategory::where('show_in_trending_list', 1)
            ->orderBy('sort')
            ->orderBy('name_en')->limit(6)->get();

        $categoriesCities = VectorCategory::where('cities_and_landmarks', 'on')
            ->where('mode', 'on')
            // ->where('cities_and_landmarks', 'on')
            ->where('in_home', 1)
            ->orderBy('sort')
            ->orderBy('name_en')->limit(4)->get();


        $categoriesPeople = VectorCategory::where('in_home', 1)
            ->where('mode', 'on')
            //    ->where('in_home', 1)
            ->where('people', '1')
            ->orderBy('sort')
            ->orderBy('name_en')->limit(4)->get();


        $images = Query::randomImages();

        $image_tags = vector_settings();
        $col_name = 'tags_' . app()->getLocale() . '_in_home';
        $tag = explode(',', $image_tags->$col_name);

        $modern_Images = Query::modernImages();

        $search_word_images = VectorSearchKey::where('lang', app()->getLocale())->orderby('count', 'desc')->limit(10)->get();
        $search_word_videos = '';
        $search_word_vectors = '';

        return view('index.home',
            [


                'top_categories' => $top_categories,
                'typePage' => $typePage,
                'categories' => $categories,
                'images' => $images,
                'tag' => $tag,
                'search_word_images' => $search_word_images,
                'search_word_videos' => $search_word_videos,
                'search_word_vectors' => $search_word_vectors,
                'categoriesCities' => $categoriesCities,
                'categoriesPeople' => $categoriesPeople,
                'modern_Images' => $modern_Images,
                'user_subscription_remaining' => $user_subscription_remaining,
                'title_plan' => $title_plan,
                'days_left' => $days_left,
                // 'categoriesTrending'          => $categoriesTrending,


            ]);

    }


    function plans_vectors()
    {

        $user_subscription_remaining_array = Query::user_subscription_remaining_vector();
        $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
        $days_left = $user_subscription_remaining_array['days_left'];
        $title_plan = $user_subscription_remaining_array['title_plan'];
        $your_plans_monthly = [];
        $your_plans_package = [];


        if (Auth::check()) {


            $your_plans_monthly = VectorSubscription::join('vector_plans', 'vector_plans.id', 'vector_subscriptions.plan_id')
                ->where([['download_remaining', '>', 0], ['user_id', '=', Auth::id()], ['ends_at', '>=', date('Y-m-d H:i:s')], ['vector_plans.type', '=', 'monthly']])
                ->whereIn('vector_subscriptions.status', [VectorSubscription::STATUS_ACTIVE, VectorSubscription::STATUS_CANCEL])
                ->pluck('vector_subscriptions.plan_id')
                ->toArray();
        }

        $locale = app()->getLocale();

        $plantitle = 'الشهرية';
        if ($locale == 'en') {
            $plantitle = 'monthly';
        }
        $plans = VectorPlan::where('status', true)
            ->where('type', 'monthly')
            ->orderBy('downloads_count')
            ->get();

        $image_plans = VectorPlan::where('status', true)
            ->where('type', 'package')
            ->orderBy('downloads_count')
            ->get();


        return view('vector.plans.index', compact('days_left', 'title_plan', 'user_subscription_remaining', 'your_plans_monthly', 'your_plans_package', 'plans', 'image_plans', 'plantitle'));
    }


    public function success()
    {
        return view('payment-callback', ['status' => true, 'url' => route('me.vectors'), 'type' => 'vector']);
    }

    public function cancel()
    {
        return view('payment-callback', ['status' => false, 'url' => route('me.vectors'), 'type' => 'vector']);
    }

    public function ris(Request $request, $hash = '')
    {
        if ($request->method() == 'POST') {
            $validator = Validator::make($request->all(), [
                'image' => ['required', 'file', 'max:20480', 'mimes:jpg,jpeg,png']
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
            return ['status' => 1, 'redirect' => route('vectors.ris', $search_image->hash)];
        }
        $search_image = SearchImage::whereHash($hash)->firstOrFail();
        $ids = cache()->remember("ris_$search_image->hash", now()->addDay(), function () use ($search_image) {
            $url = urlencode(cdn($search_image->path));
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => config('services.search_by_image.url') . "/vector-search/get_ids?number_required=1000&vector_url={$url}&key_token=" . config('services.search_by_image.key'),
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
                return json_decode($response);
            }
            return [];
        });
        $ids = paginate_array($ids, 60);
        $images = Vector::where('status', 'active')->whereIn('id', $ids->items())->get()->keyBy('id');
        $results = collect([]);
        foreach ($ids as $id)
            if ($images->get($id))
                $results->push($images->get($id));
        return view('images.ris', compact('search_image', 'results', 'ids'));
    }

}
