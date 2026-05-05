<?php

namespace App\Jobs\XML;

use App\ConvertVideo;
use App\ConvertVideoToFullHD;
use App\Models\Tag;
use Illuminate\Support\Facades\App;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class XmlTagImagesCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;
    private $sitemapCounter;
    private $from;
    private $to;

    public function __construct($sitemapCounter, $from, $to)
    {
        $this->onQueue('heavy');
        $this->sitemapCounter = $sitemapCounter;
        $this->from = $from;
        $this->from = $from;
        $this->to = $to;
    }

    public function handle()
    {
        $sitemap = App::make('sitemap');
        $imagesTags = Tag::whereHas('images')->offset($this->from)->groupBy('slug')->take($this->to)->get();
        foreach ($imagesTags as $imagesTagsItem) {
            $sitemap->add(url(urldecode($imagesTagsItem->local . route('tags.show', $imagesTagsItem->slug, false))), \Carbon\Carbon::now());
        }
        $sitemap->store('xml', 'feeds/sitemap-image_tags-' . $this->sitemapCounter);
    }
}
