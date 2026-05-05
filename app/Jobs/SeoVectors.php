<?php

namespace App\Jobs;

use App\Models\Vector;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\{Log,Storage};
use Illuminate\Support\Str;

class SeoVectors implements ShouldQueue
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
        Log::channel('info')->info("Start SeoVectors Move  id: {$this->id}");
        try {
            $file = Vector::find($this->id);
            if ($file) {
                /* 
                preview: uploads/vectors/1/preview/YM9zDr1ztx39JYQ1630302061.jpg
                thumbnail: uploads/vectors/1/xj80XHtRGc1630302061.jpg
                search: uploads/vectors/1/search/YM9zDr1ztx39JYQ1630302061.jpg
                uploads/vectors/5973/search_large-illustration-5973-cartoon-design-horse-sunset-jeddah-saudi-arabia.jpg
    
                */
                $preview = "uploads/vectors/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-preview." . pathinfo($file->preview, PATHINFO_EXTENSION);
                $thumbnail = "uploads/vectors/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-thumbnail." . pathinfo($file->thumbnail, PATHINFO_EXTENSION);
                $search = "uploads/vectors/{$this->id}/" . strtolower(Str::limit($file->slug, 70, '')) . "-search." . pathinfo($file->search, PATHINFO_EXTENSION);
                $search_large = "uploads/vectors/{$this->id}/search_large-" . strtolower(Str::limit($file->slug, 70, ''))  .".". pathinfo($file->large, PATHINFO_EXTENSION);
    
                if (Storage::disk('s3')->exists($file->preview) && ($preview !== $file->preview)) {
                    Storage::disk('s3')->move($file->preview, $preview);
                    Log::channel('info')->info("Move {$file->preview} to {$preview}");
                    $file->preview = $preview;
                }
                if (Storage::disk('s3')->exists($file->thumbnail) && ($thumbnail !== $file->thumbnail)) {
                    Storage::disk('s3')->move($file->thumbnail, $thumbnail);
                    Log::channel('info')->info("Move {$file->thumbnail} to {$thumbnail}");
                    $file->thumbnail = $thumbnail;
                }
    
                if (Storage::disk('s3')->exists($file->search) && ($search !== $file->search)) {
                    Storage::disk('s3')->move($file->search, $search);
                    Log::channel('info')->info("Move {$file->search} to {$search}");
                    $file->search = $search;
                }

                if (Storage::disk('s3')->exists($file->search_large) && ($search_large !== $file->search_large)) {
                    Storage::disk('s3')->move($file->search_large, $search_large);
                    Log::channel('info')->info("Move {$file->search_large} to {$search_large}");
                    $file->search_large = $search_large;
                }

                $file->save();

                cache()->delete("video_show_{$this->id}_ar");
                cache()->delete("video_show_{$this->id}_en");
            }
        } catch (\Throwable $th) {
            throw $th;
            // Log::channel('info')->error("SeoVectors msg: {$th->getMessage()} line  {$th->getLine()}");
        }
        Log::channel('info')->info("End SeoVectors Move  id: {$this->id}");
    }
}

