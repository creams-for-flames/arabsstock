<?php

namespace App\Jobs\XML;

use App\ConvertVideo;
use App\ConvertVideoToFullHD;
use App\Models\AdminImageSettings;
use App\Models\ImageCategory;
use App\Models\VideoCategory;
use Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class XmlCategoryCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('heavy');
    }

    public function handle()
    {

        Storage::disk('public')->makeDirectory('feeds');

        $counter = 0;
        $sitemapCounter = 0;
        $sitemap = App::make('sitemap');
        $setting = image_settings();
        foreach (glob(public_path('feeds/sitemap-categories-*')) as $r)
            unlink($r);
        foreach (ImageCategory::where('mode','on')->get() as $categoryItem) {
            if ($counter == 20000) {
                $sitemap->store('xml', 'feeds/sitemap-categories-' . $sitemapCounter);
                $sitemap->model->resetItems();
                $counter = 0;
                $sitemapCounter++;
            }
            $translations = [
                ['language' => 'en', 'url' => url('en/photos/category/' . $categoryItem->slug)]
            ];
            $sitemap->add(url('ar/photos/category/' . $categoryItem->slug), $categoryItem->updated_at, $priority = null, $freq = null,
                $images = null, $title = $categoryItem->name . ' - ' . $setting->description, $translations, $videos = null, $googlenews = null,
                $alternates = null);
            $counter++;
        }

        foreach (VideoCategory::where('mode','on')->get() as $categoryItem) {
            if ($counter == 10000) {
                $sitemap->store('xml', 'feeds/sitemap-categories-' . $sitemapCounter);
                $sitemap->model->resetItems();
                $counter = 0;
                $sitemapCounter++;
            }

            $translations = [
                ['language' => 'en', 'url' => url('en/videos/category/' . $categoryItem->slug)]
            ];
            $sitemap->add(url('ar/videos/category/' . $categoryItem->slug), $categoryItem->updated_at, $priority = null, $freq = null,
                $images = null, $title = $categoryItem->name . ' - ' . $setting->description, $translations, $videos = null, $googlenews = null,
                $alternates = null);
            $counter++;
        }
        $sitemap->store('xml', 'feeds/sitemap-categories-' . $sitemapCounter);
    }
}
