<?php

namespace App\Jobs\XML;

use App\Models\Vector;
use Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class XmlVectorsCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    private $sitemapCounter;
    private $from;
    private $to;

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
        $vectors = Vector::whereHas('category')->where('status', 'active')->get();
        foreach (glob(public_path('feeds/sitemap-vectors*')) as $r)
            unlink($r);
        foreach ($vectors as $vector) {
            if ($counter == 20000) {
                $sitemap->store('xml', 'feeds/sitemap-vectors-' . $sitemapCounter);
                $sitemap->model->resetItems();
                $counter = 0;
                $sitemapCounter++;
            }
            $imagesArray = [
                [
                    'url' => cdn($vector->preview),
                    'title' => $vector->title,
                    'license' => url('en/page/license-agreement'),
                ]
            ];
            $translations = [
                ['language' => 'en', 'url' => url('en' . route('vector.show', $vector->slug, false))]
            ];
            $sitemap->add(url('ar' . route('vector.show', $vector->slug, false)), null, $priority = null, $freq = null, $images = $imagesArray, $title = null, $translations, $videos = null, $googlenews = null, $alternates = null);
            $counter++;
        }
        $sitemap->store('xml', 'feeds/sitemap-vectors-' . $sitemapCounter);
    }
}
