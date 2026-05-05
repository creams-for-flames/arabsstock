<?php

namespace App\Jobs;

use App\Models\CategoryVideo;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;


class StoreCategoryAndTags implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $video;

    public function __construct(Video $video)
    {
        $this->video = $video;

    }

    public function handle()
    {
        $category = CategoryVideo::where('video_id', $this->video->parent_id)->get();
        if ($category) {
            foreach ($category as $categoryItem) {
                $categoryImages = CategoryVideo::create([
                    'video_id' => $this->video->id,
                    'category_id' => $categoryItem->category_id
                ]);
            }
        }

        $tags = $this->video->parent->tags();
        if ($tags->count()) {
            sync_tags($this->video, $tags->where('local', 'ar'), 'ar');
            sync_tags($this->video, $tags->where('local', 'en'), 'en');
        }

    }
}
