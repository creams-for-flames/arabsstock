<?php

namespace App\Http\Controllers;

use App\Models\AdminImageSettings;
use App\Models\AdminVideoSettings;
use App\Models\Contributor;
use App\Models\EmailSubscribe;
use App\Models\ImageSearchKey;
use App\Models\SearchImage;
use App\Models\Tag;
use App\Models\Vector;
use App\Models\VectorSearchKey;
use App\Models\VideoSearchKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\Query;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $typePage = 'images';
        $lang = app()->getLocale();
        $name = 'name_' . $lang;
        $categories = cache()->tags(['category', 'image_category'])->remember("home_image_categories_{$name}", now()->addHours(5), function () use ($name) {
            return DB::table('image_categories')->where('in_home', 1)
                ->where('mode', 'on')
                ->where('cities_and_landmarks', '!=', 'on')
                ->where('people', '0')
                ->orderBy('sort')
                ->orderBy($name)
                ->select('slug', "$name as name", 'thumbnail')
                ->paginate(12);
        });


        $categoriesTrending = DB::table('image_categories')->where('show_in_trending_list', 1)
            ->orderBy('sort')
            ->orderBy($name)
            ->limit(6)
            ->select('slug', "$name as name", 'thumbnail')
            ->get();

        $categoriesCities = DB::table('image_categories')->where('cities_and_landmarks', 'on')
            ->where('mode', 'on')
            //     ->where('cities_and_landmarks', 'on')
            ->where('in_home', 1)
            ->orderBy('sort')
            ->orderBy($name)
            ->limit(4)
            ->select('slug', "$name as name", 'thumbnail')
            ->get();


        $categoriesPeople = DB::table('image_categories')->where('in_home', 1)
            ->where('mode', 'on')
            //->where('in_home', 1)
            ->where('people', '1')
            ->orderBy('sort')
            ->orderBy($name)
            ->limit(4)
            ->select('slug', "$name as name", 'thumbnail')
            ->get();

        // $modern_Images = Query::modernImages();
        $images = cache()->remember("landPage_images", now()->addMinutes(30), function () {
            return Query::latestImages(8);
        });
        $videos = cache()->remember("landPage_videos", now()->addMinutes(30), function () {
            return Query::latestVideos(8);
        });


        $col_name = 'tags_' . app()->getLocale() . '_in_home';
        $image_tags = DB::table('admin_video_settings')->select($col_name)->first();
        $tag = explode(',', $image_tags->$col_name);

        $ContributorVectorId = DB::table('contributors')->where('show_land_vectors', true)->pluck('id');
        $vectors = cache()->remember("landPage_vectors", now()->addMinutes(30), function () use ($ContributorVectorId) {
            return Vector::whereHas('category', function ($query) {
                $query->where('vector_categories.in_random_home_vector', '1');
            })->where('vectors.status', 'active')
                // ->whereDate('vectors.date', '>', Carbon::today()->subMonths(3))
                ->where(function ($q) use ($ContributorVectorId) {
                    $q->whereIn('vectors.user_id', $ContributorVectorId)->orWhere('vectors.contributor_vector_id', 0);
                })
                ->select('vectors.*', 'admin_collections.*', 'vectors.id as id')
                ->leftJoin('admin_collection_vectors', 'admin_collection_vectors.vector_id', '=', 'vectors.id')
                ->leftJoin('admin_collections', 'admin_collections.id', '=',
                    'admin_collection_vectors.admin_collection_id')
                ->where(function ($query) {
                    return $query->where('admin_collections.in_random_home', null)
                        ->orwhere('admin_collections.in_random_home', '1');
                })
                ->groupBy('vectors.id')
                ->where('vectors.is_uploaded', 1)// TODO always show when is_uploaded = 1 in frontend
                ->inRandomOrder()
                ->tinySelection()
                ->take(8)->get();
        })->shuffle()->slice(0, 20);

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


        $header_national_day = [
            asset('img/1.webp'),
            asset('img/2.webp'),
            asset('img/4.webp'),
            asset('img/5.webp'),
        ];
        $key = array_rand($header_national_day);
        return view('index.landPage', [
            'typePage' => $typePage,
            'categories' => $categories,
            'images' => $images,
            'tag' => $tag,
            'videos' => $videos,
            'vectors' => $vectors,
            'search_word_images' => $search_word_images,
            'search_word_videos' => $search_word_videos,
            'search_word_vectors' => $search_word_vectors,
            'categoriesTrending' => $categoriesTrending,
            'categoriesCities' => $categoriesCities,
            'categoriesPeople' => $categoriesPeople,
            // 'modern_Images' => $modern_Images,
            'header_national_day' => $header_national_day[$key] ?? $header_national_day[1],
        ]);

    }// End Method


    public function image()
    {
        $data = cache()->remember('photos_home_' . app()->getLocale(), now()->addMinutes(5), function () {
            $user_subscription_remaining = '';
            $days_left = '';
            /* if (\auth()->check()) { */
            /*     $user_subscription_remaining = \auth()->user()->subscriptions_active()->with('plan')->orderBy('created_at')->first(); */

            /*     $fdate = $user_subscription_remaining->starts_at; */
            /*     $tdate = now(); */
            /*     $datetime1 = new DateTime($fdate); */
            /*     $datetime2 = new DateTime($tdate); */
            /*     $interval = $datetime1->diff($datetime2); */
            /*     $days_left = $interval->format('%a');//now do whatever you like with $days */
            /* } */
            $title_plan = 'images';


            $typePage = 'images';


            $top_categories = ImageCategory::where('cities_and_landmarks', '!=', 'on')
                ->where('mode', 'on')
                // ->where('cities_and_landmarks', '!=', 'on')
                ->where('people', '0')
                ->orderBy('sort')
                ->orderBy('name_en')->get();

            $categories = ImageCategory::where('in_home', 1)
                //where('mode', 'on')
                //    ->where('in_home', 1)
                ->where('cities_and_landmarks', '!=', 'on')
                ->where('people', '0')
                ->orderBy('sort')
                ->orderBy('name_en')->paginate(12);

            $categoriesTrending = ImageCategory::where('show_in_trending_list', 1)
                ->orderBy('sort')
                ->orderBy('name_en')->limit(6)->get();

            $categoriesCities = ImageCategory::where('cities_and_landmarks', 'on')
                ->where('mode', 'on')
                // ->where('cities_and_landmarks', 'on')
                ->where('in_home', 1)
                ->orderBy('sort')
                ->orderBy('name_en')->limit(4)->get();


            $categoriesPeople = ImageCategory::where('in_home', 1)
                ->where('mode', 'on')
                //    ->where('in_home', 1)
                ->where('people', '1')
                ->orderBy('sort')
                ->orderBy('name_en')->limit(4)->get();


            $image_tags = image_settings();
            $col_name = 'tags_' . app()->getLocale() . '_in_home';
            $tag = explode(',', $image_tags->$col_name);

            $modern_Images = Query::modernImages();

            $search_word_images = ImageSearchKey::where('lang', app()->getLocale())->orderby('count', 'desc')->groupBy('key_word')->limit(10)->get();
            $search_word_videos = '';
            $search_word_vectors = '';

            return [
                'top_categories' => $top_categories,
                'typePage' => $typePage,
                'categories' => $categories,
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
                'categoriesTrending' => $categoriesTrending,
            ];
        });
        if (!session()->has('random_order'))
            session()->put('random_order', rand());
//        $data['canReserve'] = \App\Models\Image::active()->tinySelection()->withoutGlobalScope('reserved')->canReserve()->take(15)->inRandomOrder(session()->get('random_order'))->get();
        $data['canReserve'] = collect([]);
        $data['images'] = Query::latestImages(20);
        return view('index.home', $data);

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
        $word = '';
        $q = str_replace('++', 'XBBB', $q);
        $q = str_replace('+', 'XCC', $q);
        $q = htmlentities($q);
        $q = str_replace('XCC', ' ', $q);
        $q = str_replace('XBBB', '+', $q);
        $q = filterSearchKeyword($q);
        $word = $q;
        $q = mb_strtolower($q);
        // dd($name = str_replace(' ', '+', preg_replace('/\s\s+/', ' ', $q)));
        if ((filter_var($q, FILTER_VALIDATE_INT) !== false) && Image::where('status', 'active')->find($q)) {
            $image = Image::find($q);
            return redirect()->to($image->post_link);
        } elseif (startsWith($q, 'P') == true || startsWith($q, 'p') == true) {
            $qu = strtoupper($q);
            $id = ltrim($qu, 'P');
            if ((filter_var($id, FILTER_VALIDATE_INT) !== false) && Image::find($id)) {
                $image = Image::find($id);
                return redirect()->to($image->post_link);
            }

        } elseif (mb_strlen($q) > 49) {
            return redirect()->route('landPage')->with('status', trans('misc.sentence_is_to_long'));
        }

        $images = \App\Helper::search_in_elasticsearch('images', $q, ['sort_categories_at_last' => [92, 84]], 106);
        $categories = $images->total() ? collect([]) : ImageCategory::where('in_home', 1)->where('cities_and_landmarks', '!=', 'on')->where('people', '0')->orderBy('sort')->orderBy('name_en')->paginate(12);
        $images = [
            'images' => $images,
            'page' => $request->get('page', 1),
            'title' => trans('misc.result_of') . ' ' . $word . ' - ',
            'total' => $images->total(),
            'q' => $word,
        ];

        $image_tags = image_settings();  // TODO optimize
        $col_name = 'tags_' . app()->getLocale() . '_in_home';
        $tags = explode(',', $image_tags->$col_name);

        //<--- * If $q is empty or is minus to 1 * ---->
        if ($q == '' || mb_strlen($q) <= 1) {
            return redirect('/');
        }

        if ($images['total'] > 0) {
            $searchCheck = ImageSearchKey::where('key_word', $q)->first();
            if ($searchCheck) {
                $searchCheck->count = $searchCheck->count + 1;
                $searchCheck->save();
            } else {
                $search = ImageSearchKey::create([
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
            $images['term_alternative'] = \App\Helper::check_spelling_in_elasticsearch('images', $q);
            if ($images['term_alternative'] === $q) {
                unset($images['term_alternative']);
            }
        }
        $user_subscription_remaining_array = Query::user_subscription_remaining();
        $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
        $days_left = $user_subscription_remaining_array['days_left'];
        $title_plan = $user_subscription_remaining_array['title_plan'];
        $qq = $q;
        return view('default.search', compact('user_subscription_remaining', 'days_left', 'title_plan', 'word'))->with($images);
    }// End Method

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
            return ['status' => 1, 'redirect' => route('ris', $search_image->hash)];
        }
        $search_image = SearchImage::whereHash($hash)->firstOrFail();
        $ids = cache()->remember("ris_$search_image->hash", now()->addDay(), function () use ($search_image) {
            $url = urlencode(cdn($search_image->path));
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('services.search_by_image.url') . "/image-search/get_ids?number_required=1000&image_url={$url}&key_token=" . config('services.search_by_image.key'),
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
        $images = Image::where('status', 'active')->whereIn('id', $ids->items())->get()->keyBy('id');
        $results = collect([]);
        foreach ($ids as $id)
            if ($images->get($id))
                $results->push($images->get($id));
        return view('images.ris', compact('search_image', 'results', 'ids'));
    }


    public function latest(Request $request)
    {
        $images = Query::latestImages();
        return view('index.latest', ['images' => $images, 'total' => $images->total()]);
    }

    public function can_reserve(Request $request)
    {
        if (!session()->has('random_order'))
            session()->put('random_order', rand());
        $images = \App\Models\Image::active()->tinySelection()->withoutGlobalScope('reserved')->canReserve()->inRandomOrder(session()->get('random_order'))->paginate(30);
        if ($request->ajax()) {
            $view = view('includes.latestImagesAjax', compact('images'))->render();
            if (count($images) == 0) {
                return response()->json(['html' => ""]);
            }
            return response()->json(['html' => $view]);
        }
        return view('index.can-reserve', ['images' => $images]);
    }

    public function featured()
    {

        $images = Query::featuredImages();

        return view('index.featured', ['images' => $images]);

    }// End Method

    public function popular()
    {

        $images = Query::popularImages();

        return view('index.popular', ['images' => $images]);

    }// End Method

    public function commented()
    {

        $images = Query::commentedImages();

        return view('index.commented', ['images' => $images]);

    }// End Method

    public function viewed()
    {

        $images = Query::viewedImages();

        return view('index.viewed', ['images' => $images]);

    }// End Method

    public function downloads()
    {

        $images = Query::downloadsImages();

        return view('index.downloads', ['images' => $images]);

    }// End Method

    public function categories()
    {


        $user_subscription_remaining_array = Query::user_subscription_remaining();
        $user_subscription_remaining = $user_subscription_remaining_array['user_subscription_remaining'];
        $days_left = $user_subscription_remaining_array['days_left'];
        $title_plan = $user_subscription_remaining_array['title_plan'];


        $data = ImageCategory::where('mode', 'on')->orderBy('sort')->orderBy('name_en')->get();


        $categoriesCities = ImageCategory::where('mode', 'on')
            ->where('cities_and_landmarks', 'on')->first();
        // dd($categoriesCities->toArray());
        $categoriesPeople = ImageCategory::where('mode', 'on')
            ->orderBy('sort')
            ->where('people', '=', '1')->first();
        $setting = image_settings();
        return view('default.categories',
            compact('days_left', 'user_subscription_remaining', 'title_plan', 'data', 'categoriesCities',
                'categoriesPeople', 'setting'));
    }

    public function category($slug, Request $request)
    {

        $slug = trim($slug);

        $imagesColl = Query::categoryImages($slug, null, 106);
        $setting = image_settings();
        $images = $imagesColl['images'];
        $category = $imagesColl['category'];
        $classArray = [
            "grid-item grid-item--width",
            "grid-item grid-item--height2",
            "grid-item",
            "grid-item grid-item--width2 grid-item--height2"
        ];
        return view('default.category', compact('images', 'category', 'classArray', 'setting'));

    }// End Method


    public function tag($slug, Request $request)
    {
        if (mb_strlen($slug) > 0) {
            $tag = Tag::where('slug', $slug)->first();
            if ($tag) {
                $tag = $tag->title;
                $results = \App\Helper::search_in_elasticsearch('images', $tag, [], 106);

            }
        }
        if (!isset($results)) {
            $results = collect([]);
            $tag = $slug;
        }
        return view('default.tags-show', compact('results', 'tag'));
    }


    public function collections(Request $request)
    {

        $settings = image_settings();

        $title = trans('misc.collections') . ' - ';

        $data = Collection::has('collection_images')->selectRaw('collections.*,(select thumbnail from images join collection_image on (collection_image.image_id=images.id) where collection_image.collection_id = collections.id  and images.status = "active" limit 1 ) AS thumbnail')
            ->orderBy('collections.id', 'desc')
            ->groupBy('collections.id')
            ->paginate($settings->result_request);


        if ($request->input('page') > $data->lastPage()) {
            abort('404');
        }

        return view('default.collections', compact('title', 'data'));
    }//<--- End Method


    public function emailSubscribe(Request $request)
    {

        $user = Auth::user();


        $rules = [
            'email' => 'required|email',

        ];


        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return 'false';
        }

        $user = EmailSubscribe::create([
            'user_id' => $user ? $user->id : null,
            'email' => $request->get('email')
        ]);


        \Session::flash('success_email_subscribe_message', trans('misc.send'));

        return 'true';

        return redirect()->route('landPage')->with('status', 'تم ارسال طلب الاشتراك البريدي بنجاح');

    }


    public function EmailUnSubscribe($id, $username, $token)
    {
        $user = User::where([
            ['id', $id],
            ['username', $username],
            ['token', $token]
        ])->firstOrFail();
        $message = __("misc.Sorry_you_have_previously_unsubscribed_from_the_newsletter");

        $user_auth = Auth::user();
        if ($user_auth && ($user_auth->id !== $user->id)) {
            $message = __("misc.Sorry_you_do_not_have_permissions_to_unsubscribe_from_the_newsletter");
        }
        if ($user->receive_newsletters) {
            $user->receive_newsletters = 0;
            $user->save();
            $message = __("misc.The_newsletter_has_been_successfully_unsubscribed");

        }

        return view('users.unsubscribe', compact('message', 'user'));
        \Session::flash('success_email_subscribe_message', trans('misc.send'));


        return redirect()->route('landPage')->with('status', 'تم ارسال طلب الاشتراك البريدي بنجاح');

    }


}
