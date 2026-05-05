<?php

namespace App\Jobs;

use App\Models\ContributorRawVideo;
use App\Models\RawVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CopyRawVideosFromContributorToArabsstock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $contributor_raw_id;
    protected $raw_id;
    public function __construct($contributor_raw_id,$raw_id)
    {
        $this->contributor_raw_id = $contributor_raw_id;
        $this->raw_id = $raw_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('info')->info("Start CopyRawVideosFromContributorToArabsstock contributor_raw_id:  $this->contributor_raw_id : raw_video : $this->raw_id ");

        $raw =  ContributorRawVideo::findOrFail($this->contributor_raw_id);
        $raw_copy =  RawVideo::findOrFail($this->raw_id);
        if (Storage::disk('s3')->exists($raw->original)) {
             Storage::disk('s3')->copy($raw->original, $raw_copy->original);
             $file_size = Storage::disk('s3')->size($raw_copy->original);
             $raw_copy->is_uploaded_original = true;
             $raw_copy->size = $file_size;
             $raw_copy->save();
            Log::channel('info')->info(" CopyRawVideosFromContributorToArabsstock copy raw original from:  $raw->original:  to raw_video original : $raw_copy->original ");

            # code...
        }
        
        if (Storage::disk('s3')->exists($raw->preview)) {
            Storage::disk('s3')->copy($raw->preview, $raw_copy->preview);
            $raw_copy->is_uploaded_preview = true;
            $raw_copy->save();
            Log::channel('info')->info(" CopyRawVideosFromContributorToArabsstock copy raw preview from:  $raw->preview:  to raw_video preview : $raw_copy->preview ");
           # code...
       }
       if ($raw_copy->is_uploaded_preview  && $raw_copy->is_uploaded_original ) {
            $raw->contributor_stage = 8;
            $raw->save();
       }
       Log::channel('info')->info("Start CopyRawVideosFromContributorToArabsstock contributor_raw_id:  $this->contributor_raw_id : raw_video : $this->raw_id ");

    }
}
