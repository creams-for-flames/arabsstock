<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Video;
use Illuminate\Support\Facades\{Log,Storage};

class DeleteVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $contributor_video_id;
    public function __construct($contributor_video_id)
    {
        $this->contributor_video_id = $contributor_video_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = Video::where('contributor_video_id',$this->contributor_video_id)->first();
        $id = $file->id;
        Log::channel('info')->info("start delete file video arabstock {$id}");

        $stocks = Video::where('parent_id',$id)->get();
            foreach ($stocks as $stock) {
                // Delete Stock
                if (isset($stock->preview)) {
                    $stock_path = $stock->preview;
                    if (Storage::disk('s3')->exists($stock_path) ) {
                        Storage::disk('s3')->delete($stock_path);
                    }
                }
                $stock->delete();
            }
            if (isset($file->thumbnail)) {
                if (Storage::disk('s3')->exists($file->thumbnail) ) {
                    Storage::disk('s3')->delete($file->thumbnail);
                }
            }
            if (isset($file->cut_video)) {
                if (Storage::disk('s3')->exists($file->cut_video) ) {
                    Storage::disk('s3')->delete($file->cut_video);
                }
            }
            if (isset($file->gif_video)) {
                if (Storage::disk('s3')->exists($file->gif_video) ) {
                    Storage::disk('s3')->delete($file->gif_video);
                }
            }
            if (isset($file->size_240p)) {
                if (Storage::disk('s3')->exists($file->size_240p) ) {
                    Storage::disk('s3')->delete($file->size_240p);
                }
            }
            if (isset($file->search)) {
                if (Storage::disk('s3')->exists($file->search) ) {
                    Storage::disk('s3')->delete($file->search);
                }
            }
            	
            
        $file->delete();

    }
}
