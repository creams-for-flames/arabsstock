<?php

namespace App\Jobs\XML;

use App\ConvertVideo;
use App\ConvertVideoToFullHD;
use Illuminate\Support\Facades\App;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class XmlVideosCreator implements ShouldQueue
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
        $videos = Video::whereHas('category')->where('parent_id', null)->where('videos.status', 'active')
            ->where('videos.video_fail', 0)
            ->select('videos.*')
            ->groupBy('videos.id')
            ->where('videos.is_uploaded', 1)// TODO always show when is_uploaded = 1 in frontend
            ->where('videos.parent_id', null)->get();
        foreach (glob(public_path('feeds/sitemap-videos*')) as $r)
            unlink($r);

        // counters
        $counter = 0;
        $sitemapCounter = 0;
        /**@var $sitemap \Laravelium\Sitemap\Sitemap */
        foreach ($videos as $video) {
            if ($counter == 20000) {
                $sitemap->store('xml', 'feeds/sitemap-videos-' . $sitemapCounter);
                $sitemap->model->resetItems();
                $counter = 0;
                $sitemapCounter++;
            }
            sscanf($video->duration, "%d:%d", $minutes, $seconds);
            $duration = $minutes * 60 + $seconds;
            $videoArray = [
                [
                    'title' => $video->title ,
                    'description' => $video->title,
                    'content_loc' => cdn($video->size_240p),
                    'thumbnail_loc' => cdn($video->thumbnail),
                    'duration' => $duration,
                    'view_count' => $video->count_view,
                    'family_friendly' => 'yes',
                    'live' => 'no',
                    'price' => [
                        'price' => 65,
                        'currency' => 'USD'
                    ],
                    'publication_date' => date('Y-m-d\TH:i:sP', strtotime($video->date)),
                ],
            ];
            $translations = [
                ['language' => 'en', 'url' => url('en' . route('video.show', $video->slug, false))]
            ];
            $sitemap->add(url('ar' . route('video.show', $video->slug, false)), $video->date, $priority = null, $freq = null, $images = null,
                $title = null,
                $translations, $videos = $videoArray, $googlenews = null, $alternates = null);
            $counter++;
        }
        $sitemap->store('xml', 'feeds/sitemap-videos-' . $sitemapCounter);
    }
}
