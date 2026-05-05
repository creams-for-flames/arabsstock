<?php

namespace App\Jobs;

use App\Models\Vector;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FixVectorsIob implements ShouldQueue
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
        \Log::channel('info')->info('start Vector create image from vector '.now());

        Vector::chunk(200, function ($items)  {
            $mimeType = "jpg";
            foreach ($items as $key => $item) {
                try {
                    $file_path = 'uploads' . DS . 'vectors' . DS . $item->id. DS . $item->vector;
                    $name_image = Str::random(20) . '.' . $mimeType;
                    $image_path = 'uploads' . DS . 'vectors' . DS . $item->id. DS .'large'.DS.$name_image;

                    $temp_dir=public_path($file_path);
                    $temp_dir_img=public_path($image_path);

                   if (Storage::disk('s3')->exists($file_path)) {
                        if (!file_exists(dirname($temp_dir))) {
                        mkdir(dirname($temp_dir), 0777, true);
                        chmod(dirname($temp_dir), 0777);
                        }

                        if (!file_exists(dirname($temp_dir_img))) {
                            mkdir(dirname($temp_dir_img), 0777, true);
                            chmod(dirname($temp_dir_img), 0777);
                            }

                        file_put_contents($temp_dir, Storage::disk('s3')->get($file_path));
                        $image = new \Imagick($temp_dir);
                        $image->setResolution(300,300);
                        $image->readimage($temp_dir);
                        $dimension = $image->getImageGeometry();
                        $vector_width = $dimension['width']??0;
                        $vector_height = $dimension['height']??0;
                        $image->scaleImage($vector_width, $vector_height);
                        $image->setImageFormat($mimeType);
                        $image->writeImage($temp_dir_img);
                        $pathS3 = Storage::disk('s3')->put($image_path, file_get_contents($temp_dir_img));// orginal
                        $item->large = $name_image;
                        $item->save();
                        \Log::channel('info')->info('Done vector : '.$temp_dir_img);
                        if (file_exists(dirname($temp_dir))) {
                             \File::deleteDirectory(dirname($temp_dir));
                        }
                   }
                } catch (\Throwable $th) {
                    \Log::error("Fix Vector ". $th->getMessage().' line: '. $th->getLine());
                }

            }
        });

        \Log::channel('info')->info('end Vector create image from vector '.now());


    }
}
