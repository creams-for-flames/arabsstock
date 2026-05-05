<?php

namespace App\Jobs\XML;

use App\ConvertVideo;
use App\ConvertVideoToFullHD;
use App\Models\Image;
use Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class XmlImagesCreator implements ShouldQueue
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
        $counter = 0;
        $sitemapCounter = 0;
        /**@var $sitemap \Laravelium\Sitemap\Sitemap */
        $sitemap = App::make('sitemap');
        $images = Image::whereHas('category')->where('status', 'active')->get();
        foreach (glob(public_path('feeds/sitemap-images*')) as $r)
            unlink($r);
        foreach ($images as $image) {
            if ($counter == 20000) {
                $sitemap->store('xml', 'feeds/sitemap-images-' . $sitemapCounter);
                $sitemap->model->resetItems();
                $counter = 0;
                $sitemapCounter++;
            }
            $imageArray = [
                [
                    'url' => cdn($image->preview),
                    'title' => $image->title,
                    'license' => url('en/page/license-agreement'),
                ],
                [
                    'url' => cdn($image->thumbnail),
                    'title' => $image->title,
                    'license' => url('en/page/license-agreement'),
                ],
            ];
            $translations = [
                ['language' => 'en', 'url' => url("en" . route('photo.show', $image->slug, false))]
            ];
            $sitemap->add(url("ar" . route('photo.show', $image->slug, false)), null, $priority = null, $freq = null, $images = $imageArray, $title = null, $translations, $videos = null, $googlenews = null, $alternates = null);
            $counter++;
        }
        $sitemap->store('xml', 'feeds/sitemap-images-' . $sitemapCounter);
    }
}
