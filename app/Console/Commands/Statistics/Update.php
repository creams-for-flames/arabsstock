<?php

namespace App\Console\Commands\Statistics;

use App\Models\ContributorImageSubmission;
use App\Models\ContributorVectorSubmission;
use App\Models\ContributorVideoSubmission;
use App\Models\Image;
use App\Models\ImageCategory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoCategory;
use App\Providers\ViewServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as RequestGuzzle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->removebg();
        $this->api_removebg();
        $this->images();
        $this->videos();
        $this->users();
        DB::table('statistics')->updateOrInsert(['key' => 'last_update'], ['value' => now()]);
        $this->info('Statistics updated successfully');
    }


    private function removebg()
    {

        $removebg = [];
        $images_tablee = DB::table('images');
        $removebg[] = (object)['name' => "all", 'data' => DB::table('images')->where([
            ['removebg_status', '!=', NULL],
        ])->count()];
        $removebg[] = (object)['name' => "free", 'data' => DB::table('images')->where([
            ['removebg_type', "free"],
            ['removebg_status', '!=', NULL],

        ])->count()];
        $removebg[] = (object)['name' => "paid", 'data' => DB::table('images')->where([
            ['removebg_type', "paid"],
        ])->count()];
        $removebg[] = (object)['name' => "manual", 'data' => DB::table('images')->where([
            ['removebg_type', "manual"],
        ])->count()];
        $removebg[] = (object)['name' => "free_pending", 'data' => DB::table('images')->where([
            ['removebg_type', "free"],
            ['removebg_status', '!=', NULL],
            ['removebg_status_disply', "pending"],
        ])->count()];
        $removebg[] = (object)['name' => "paid_pending", 'data' => DB::table('images')->where([
            ['removebg_type', "paid"],
            ['removebg_status_disply', "pending"],
        ])->count()];
        $removebg[] = (object)['name' => "active", 'data' => DB::table('images')->where([
            ['removebg_status_disply', "active"],
        ])->count()];
        $removebg[] = (object)['name' => "pending", 'data' => DB::table('images')->where([
            ['removebg_status', '!=', NULL],
            ['removebg_status_disply', "pending"],
        ])->count()];
        $removebg = collect($removebg);
        $data = json_encode($removebg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.removebg'], ['value' => $data]);
    }

    private function api_removebg()
    {
        $config = config('services.removebg');
        $client = new Client();
        $headers = [
            'X-Api-Key' => $config['paid']['api_key'],
            'Content-Type' => 'application/json'
        ];
        $request = new RequestGuzzle('GET', 'https://api.remove.bg/v1.0/account', $headers);
        $res = $client->sendAsync($request)->wait();
        $data = json_encode(json_decode($res->getBody()), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.api.removebg'], ['value' => $data]);
    }

    private function users()
    {
        $usersDash = User::select(
            DB::raw('count(id) as `count`'),
            DB::raw("DATE_FORMAT(created_at, '%m-%Y') new_date"),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month'),
            DB::raw('MONTHNAME(created_at) nameMo')
        )
            ->groupby('year', 'month')
            ->orderby('year', 'asc')
            ->orderby('month', 'asc')
            ->get();
        $userDashCount = User::where('id', '>', 0)->count();
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.usrs.cahrt'], ['value' => json_encode($usersDash, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.usrs.count'], ['value' => $userDashCount]);

    }

    private function images()
    {
        $data = ImageCategory::where('image_categories.id', '!=', 1)
            ->leftJoin(
                'category_image',
                'category_image.category_id',
                '=',
                'image_categories.id'
            )
            ->select(
                DB::raw('count(category_image.image_id) as `images_count`'),
                'name_' . app()->getLocale()
            )
            ->groupby('category_id')
            ->limit(5)
            ->get();
        $data = json_encode($data->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.image_categories'], ['value' => $data]);

        $imges = Image::select(
            DB::raw('count(id) as `images_count`'),
            DB::raw("DATE_FORMAT(date, '%m-%Y') new_date"),
            DB::raw('YEAR(date) year, MONTH(date) month'),
            DB::raw('MONTHNAME(date) name')
        )
            ->groupby('year', 'month')
            ->orderby('year', 'asc')
            ->orderby('month', 'asc')
            ->get();
        $data = json_encode(json_decode($imges), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.chart'], ['value' => $data]);
        $imgesActive = Image::where('status', 'active')->count();
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.active_count'], ['value' => $imgesActive]);
        $imgesnotActive = Image::where('status', 'pending')->count();
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.pending_count'], ['value' => $imgesnotActive]);

//        الصور الاكثر مبيعا بالنسبة لتصنيف

        $imagesCategoryPaying = ImageCategory::where('image_categories.id', '!=', 1)
            ->leftJoin(
                'category_image',
                'category_image.category_id',
                '=',
                'image_categories.id'
            )
            ->leftJoin(
                'image_downloads',
                'category_image.image_id',
                '=',
                'image_downloads.image_id'
            )
            ->select(
                DB::raw('count(image_downloads.image_id) as `images_count`'),
                'name_' . app()->getLocale(),
                'image_downloads.*',
                'image_categories.id'
            )
            ->groupby('category_id')
            ->limit(5)
            ->get();

        $data = json_encode(json_decode($imagesCategoryPaying), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.paying_per_category'], ['value' => $data]);


//      image purchases
        $purchases = Purchase::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->where('purchaseable_type', Image::class)
            ->get()->toArray();
        $purchases_count = Purchase::where('purchaseable_type', Image::class)->count();

        $data = json_encode($purchases, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.purchases'], ['value' => $data]);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.purchases_count'], ['value' => $purchases_count]);

        $top_downloading = Image::tinySelection()->withCount('downloads')->orderBy('downloads_count', 'desc')->take(5)->get();
        $data = json_encode($top_downloading->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.images.top_downloading'], ['value' => $data]);
    }

    private function videos()
    {
        $data = VideoCategory::where('video_categories.id', '!=', 1)
            ->leftJoin(
                'category_video',
                'category_video.category_id',
                '=',
                'video_categories.id'
            )
            ->select(
                DB::raw('count(category_video.video_id) as `videos_count`'),
                'name_' . app()->getLocale()
            )
            ->groupby('category_id')
            ->limit(5)
            ->get();
        $data = json_encode($data->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.video_categories'], ['value' => $data]);

        $imges = Video::select(
            DB::raw('count(id) as `videos_count`'),
            DB::raw("DATE_FORMAT(date, '%m-%Y') new_date"),
            DB::raw('YEAR(date) year, MONTH(date) month'),
            DB::raw('MONTHNAME(date) name')
        )
            ->groupby('year', 'month')
            ->orderby('year', 'asc')
            ->orderby('month', 'asc')
            ->get();
        $data = json_encode(json_decode($imges), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.chart'], ['value' => $data]);
        $imgesActive = Video::where('status', 'active')->count();
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.active_count'], ['value' => $imgesActive]);
        $imgesnotActive = Video::where('status', 'pending')->count();
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.pending_count'], ['value' => $imgesnotActive]);

//        الفيديو الاكثر مبيعا بالنسبة لتصنيف

        $videosCategoryPaying = VideoCategory::where('video_categories.id', '!=', 1)
            ->leftJoin(
                'category_video',
                'category_video.category_id',
                '=',
                'video_categories.id'
            )
            ->leftJoin(
                'video_downloads',
                'category_video.video_id',
                '=',
                'video_downloads.video_id'
            )
            ->select(
                DB::raw('count(video_downloads.video_id) as `videos_count`'),
                'name_' . app()->getLocale(),
                'video_downloads.*',
                'video_categories.id'
            )
            ->groupby('category_id')
            ->limit(5)
            ->get();

        $data = json_encode(json_decode($videosCategoryPaying), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.paying_per_category'], ['value' => $data]);


//      video purchases
        $purchases = Purchase::select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->where('purchaseable_type', Video::class)
            ->get()->toArray();
        $purchases_count = Purchase::where('purchaseable_type', Video::class)->count();

        $data = json_encode($purchases, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.purchases'], ['value' => $data]);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.purchases_count'], ['value' => $purchases_count]);

        $top_downloading = Video::tinySelection()->withCount('downloads')->orderBy('downloads_count', 'desc')->take(5)->get();
        $data = json_encode($top_downloading->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        DB::table('statistics')->updateOrInsert(['key' => 'dashboard.videos.top_downloading'], ['value' => $data]);
    }

    public static function notifications()
    {
        (new ViewServiceProvider(app()))->notification(ContributorVideoSubmission::class,
            [
                'contributor' => function ($query) {
                    $query->select('id', 'name');
                }
            ], "file", 'items.file', "file.file", "فيديو", "admin.videos.contributors.submissions.review");
        (new ViewServiceProvider(app()))->notification(ContributorImageSubmission::class,
            [
                'contributor' => function ($query) {
                    $query->select('id', 'name');
                }
            ], "file", 'items.image', "images.file", "صورة", "admin.contributors.submissions.review");
        (new ViewServiceProvider(app()))->notification(ContributorVectorSubmission::class,
            [
                'contributor' => function ($query) {
                    $query->select('id', 'name');
                }
            ], "file", 'items.file', "file.file", "فيكتور", "admin.vectors.contributors.submissions.review");
    }
}
