<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\{Log,Storage};

class DeleteImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $contributor_image_id;
    public function __construct($contributor_image_id)
    {
        $this->contributor_image_id = $contributor_image_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = \App\Models\Image::where('contributor_image_id',$this->contributor_image_id)->first();
        $id = $file->id;
        Log::info("start delete file image arabstock {$id}");
                // Delete Notification
                $notifications = \App\Models\Notifications::where('destination', $id)
                ->whereIn('type', [2,3,6])
                ->delete();
                // Collection Image
                // $collectionsImages = \App\Models\CollectionImage::where('image_id','=',$id)->delete();
                // Image Reported
                $imagesReporteds = \App\Models\ImagesReported::where('image_id', '=', $id)->delete();
                // ALL RESOLUTIONS IMAGES
                // $stocks = \App\Models\Stock::where('image_id', '=', $id)->delete();
                // \App\Models\ImageTag::where('image_id',$id)->delete();
                if (isset($file->medium) && $file->medium != '') {
                    $medium = $file->medium;
                    // Delete medium
                    if (Storage::disk('s3')->exists($medium)) {
                        Storage::disk('s3')->delete($medium);
                    }
                }
                if (isset($file->small) && $file->small != '') {
                    $small = $file->small;
                    // Delete small
                    if (Storage::disk('s3')->exists($small)) {
                        Storage::disk('s3')->delete($small);
                    }
                }
        
                if (isset($file->preview)) {
                    $preview_image = $file->preview;
                    // Delete preview
                    if (Storage::disk('s3')->exists($preview_image)) {
                        Storage::disk('s3')->delete($preview_image);
                    }
                }
        
                if (isset($file->thumbnail)) {
                    $thumbnail = $file->thumbnail;
                    // Delete thumbnail
                    if (Storage::disk('s3')->exists($thumbnail)) {
                        Storage::disk('s3')->delete($thumbnail);
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
