<?php

namespace App\Jobs\XML;

use App\ConvertVideo;
use App\ConvertVideoToFullHD;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class XmlMasterCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('heavy');
    }

    public function handle()
    {
        Storage::disk('public')->makeDirectory('feeds');

        /**@var $sitemap \Laravelium\Sitemap\Sitemap */
        $sitemap = App::make('sitemap');
        foreach (Storage::disk('public')->allFiles('feeds') as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'xml')
                $sitemap->addSitemap(URL::to($file), Carbon::createFromTimestamp(filemtime(public_path($file))));
        }
        $sitemap->store('sitemapindex', 'sitemap', public_path());
    }
}
