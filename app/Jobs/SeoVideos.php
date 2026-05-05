<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoVideos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('info')->info("Start SeoVideos Move  id: {$this->id}");
        try {
            $file = Video::with('child')->find($this->id);
            if ($file) {
                /* 
                cut_video: /uploads/videos/1/thumbnail_mJ3YFMxRzgXSKMsYkAHT.mp4
                size_240p:/uploads/videos/1/240pwatermarked_bT1xoD3XIoEfJOfxYduO.mp4
                thumbnail: /uploads/videos/1/thumbnail_1q5DKZBiJw23s1dMWU24.png
                thumbnail_sm: /uploads/videos/1/thumbnail_sm_1642513646PlUlWsd5aL.png
                gif_video: /uploads/videos/1/thumbnail_43nNVwR10XucgpFls3af.gif
                search: /uploads/videos/1/search_1q5DKZBiJw23s1dMWU24.png
    
                */
                $cut_video = "uploads/videos/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-preview." . pathinfo($file->cut_video, PATHINFO_EXTENSION);
                $size_240p = "uploads/videos/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-preview_sm." . pathinfo($file->size_240p, PATHINFO_EXTENSION);
                $gif_video = "uploads/videos/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-thumbnail_gif." . pathinfo($file->gif_video, PATHINFO_EXTENSION);
                $thumbnail = "uploads/videos/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-thumbnail." . pathinfo($file->thumbnail, PATHINFO_EXTENSION);
                $thumbnail_sm = "uploads/videos/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-thumbnail_sm." . pathinfo($file->thumbnail_sm, PATHINFO_EXTENSION);
                $search = "uploads/videos/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-search." . pathinfo($file->search, PATHINFO_EXTENSION);
                if (Storage::disk('s3')->exists($file->cut_video) && ($cut_video !== $file->cut_video)) {
                    Storage::disk('s3')->move($file->cut_video, $cut_video);
                    Log::channel('info')->info("Move {$file->cut_video} to {$cut_video}");
                    $file->cut_video = $cut_video;
                }
                if (Storage::disk('s3')->exists($file->size_240p) && ($size_240p !== $file->size_240p)) {
                    Storage::disk('s3')->move($file->size_240p, $size_240p);
                    Log::channel('info')->info("Move {$file->size_240p} to {$size_240p}");
                    $file->size_240p = $size_240p;
                }

                if (Storage::disk('s3')->exists($file->gif_video) && ($gif_video !== $file->gif_video)) {
                    Storage::disk('s3')->move($file->gif_video, $gif_video);
                    Log::channel('info')->info("Move {$file->gif_video} to {$gif_video}");
                    $file->gif_video = $gif_video;
                }
    
                if (Storage::disk('s3')->exists($file->thumbnail) && ($thumbnail !== $file->thumbnail)) {
                    Storage::disk('s3')->move($file->thumbnail, $thumbnail);
                    Log::channel('info')->info("Move {$file->thumbnail} to {$thumbnail}");
                    $file->thumbnail = $thumbnail;
                }
                if (Storage::disk('s3')->exists($file->thumbnail_sm) && ($thumbnail_sm !== $file->thumbnail_sm)) {
                    Storage::disk('s3')->move($file->thumbnail_sm, $thumbnail_sm);
                    Log::channel('info')->info("Move {$file->thumbnail_sm} to {$thumbnail_sm}");
                    $file->thumbnail_sm = $thumbnail_sm;
                }
    
    
                if (Storage::disk('s3')->exists($file->search) && ($search !== $file->search)) {
                    Storage::disk('s3')->move($file->search, $search);
                    Log::channel('info')->info("Move {$file->search} to {$search}");
                    $file->search = $search;
                }
                $file->save();
                $file->child()->update([
                    'thumbnail'=> $thumbnail,
                    'thumbnail_sm'=> $thumbnail_sm,
                    'search'=> $search,
                ]);
                cache()->delete("video_show_{$this->id}_ar");
                cache()->delete("video_show_{$this->id}_en");
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::channel('info')->error("SeoVideos msg: {$th->getMessage()} line  {$th->getLine()}");
        }
        Log::channel('info')->info("End SeoVideos Move  id: {$this->id}");

    }
}

