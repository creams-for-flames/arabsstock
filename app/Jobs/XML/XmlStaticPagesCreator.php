<?php

namespace App\Jobs\XML;

use App\ConvertVideo;
use App\ConvertVideoToFullHD;
use Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class XmlStaticPagesCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   // private $video;

    public function __construct()
    {
        $this->onQueue('heavy');
    }

    public function handle()
    {

        Storage::disk('public')->makeDirectory('feeds');
        $sitemap = App::make('sitemap');
        $translations    = [
            ['language' => 'en', 'url' => URL::to('/en')]
        ];

        $sitemap->add(URL::to('/ar'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $translations    = [
            ['language' => 'en', 'url' => URL::to('/en/photos')]
        ];
        //  $sitemap->add($categoryItem->post_link, $categoryItem->updated_at);

        $sitemap->add(URL::to('/ar/photos'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $translations    = [
            ['language' => 'en', 'url' => URL::to('/en/videos')]
        ];

        $sitemap->add(URL::to('/ar/videos'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $translations    = [
            ['language' => 'en', 'url' => URL::to('/en/photos/categories')]
        ];

        $sitemap->add(URL::to('/ar/photos/categories'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $translations    = [
            ['language' => 'en', 'url' => URL::to('/en/videos/categories')]
        ];

        $sitemap->add(URL::to('/ar/videos/categories'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);


        $translations    = [
            ['language' => 'en', 'url' => URL::to('en/technical-support')]
        ];
        //  $sitemap->add($categoryItem->post_link, $categoryItem->updated_at);

        $sitemap->add(URL::to('ar/technical-support'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $translations    = [
            ['language' => 'en', 'url' => URL::to('en/page/about')]
        ];
        //  $sitemap->add($categoryItem->post_link, $categoryItem->updated_at);

        $sitemap->add(URL::to('ar/page/about'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);


        $translations    = [
            ['language' => 'en', 'url' => URL::to('en/page/terms-of-service')]
        ];

        $sitemap->add(URL::to('ar/page/terms-of-service'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $translations    = [
            ['language' => 'en', 'url' => URL::to('en/page/privacy')]
        ];
        //  $sitemap->add($categoryItem->post_link, $categoryItem->updated_at);

        $sitemap->add(URL::to('ar/page/privacy'), \Carbon\Carbon::now(), $priority = null, $freq = null,
            $images = null, $title = null, $translations, $videos = null, $googlenews = null,
            $alternates = null);

        $sitemap->store('xml', 'feeds/sitemap-static-pages');
        // add sitemap to sitemaps array
        $sitemap->addSitemap(secure_url('feeds/sitemap-static-pages' . '.xml'));


    }
}
