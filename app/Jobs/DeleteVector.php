<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\{Log,Storage};
use App\Models\Vector;

class DeleteVector implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $contributor_vector_id;
    public function __construct($contributor_vector_id)
    {
        $this->contributor_vector_id = $contributor_vector_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = Vector::where('contributor_vector_id',$this->contributor_vector_id)->first();
        $id = $file->id;
        Log::channel('info')->info("start delete file vector arabstock {$id}");

        // Collection Vector
        $collectionsImages = \App\Models\CollectionVector::where('vector_id','=',$id)->delete();

        
        // Delete preview
        if (isset($file->preview)) {
            $preview_image = $file->preview;
            if (Storage::disk('s3')->exists($preview_image)) {
                Storage::disk('s3')->delete($preview_image);
            }
        }
        
        // Delete thumbnail
        if (isset($file->thumbnail)) {
            $thumbnail =  $file->thumbnail;
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
