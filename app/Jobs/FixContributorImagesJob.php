<?php

namespace App\Jobs;

use App\Helper;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\ContributorImage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FixContributorImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::channel('info')->info("start FixContributorImagesJob - ".now());

        try {
            // $images = ContributorImage::whereIn('contributor_stage', [0,1,2])->get();
        ContributorImage::orderBy('id','desc')->chunk(200, function ($images)  {

            foreach ($images as $key => $image) {
                $original_file_path= 'uploads' . DS . 'contributor_images' . DS . $image->id .DS.'orginal'.DS. $image->large;
                $original = public_path($original_file_path);
                $root_dir = public_path('uploads' . DS . 'contributor_images' . DS . $image->id);
                if (Storage::disk('s3')->exists($original_file_path)) {
                    if (!file_exists($root_dir)) {
                        mkdir($root_dir, 0777, true);
                        chmod($root_dir,0777);
                    }
                    if (!file_exists(dirname($original))) {
                        mkdir(dirname($original), 0777, true);
                        chmod(dirname($original),0777);
                    }
                    file_put_contents($original, Storage::disk('s3')->get($original_file_path));
                    $width = getWidth($original);
                    $height = getHeight($original);
                    $_width = $width > $height ? 640 : 0;
                    $_height = $width > $height ? 0  : 640;
                    // PREVIEW
                    $extension  = pathinfo($original, PATHINFO_EXTENSION);
                    $preview = strtolower(\Illuminate\Support\Str::slug(Str::random(15), '-') . '-' . time() . str_random(5) . '.' . $extension);
                    $file_path_preview = 'uploads' . DS . 'contributor_images' . DS . $image->id  .DS. 'preview'.DS. $preview;
                    $path_preview = \public_path($file_path_preview);
                    if (!file_exists(dirname($path_preview))) {
                        mkdir(dirname($path_preview), 0777, true);
                        chmod(dirname($path_preview),0777);

                    }
                    $watermarkSource = public_path('img/watermark.png');
                    $uploaded = Helper::resize_image_without_scale($original,$_width,$_height,$path_preview);
                    Helper::watermark($path_preview, $watermarkSource);
                    //////////
                    Storage::disk('s3')->put($file_path_preview, file_get_contents($path_preview));// preview
                    if (file_exists(dirname($root_dir))) {
                        \File::deleteDirectory($root_dir);
                    }
                    $image->preview = $preview;
                    $image->save();

                    \Log::channel('info')->info("done FixContributorImagesJob - ". now() . ' file: '. $file_path_preview);

                    # code...
                }
            }
        });


        } catch (\Throwable $th) {
            //throw $th;
            $error = $th->getMessage() .' line :  '.$th->getLine();
            \Log::channel('info')->info("error FixContributorImagesJob - ".$error);

        }
        \Log::channel('info')->info("end FixContributorImagesJob - ".now());

    }
}
