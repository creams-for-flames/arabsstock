<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HashImagesJob implements ShouldQueue
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
        \Log::channel('hashfile')->info('start hash images'.now());
        Image::with('stock')->orderBy('id','desc')->chunk(200, function ($items)  {

            foreach ($items as $key => $item) {
                $temp_dir=public_path(DS . 'uploads' . DS . 'images' . DS . $item->id );

                foreach ($item->stock as $key => $child) {
                    $nameFile = 'uploads' . DS . $child->type . DS . $child->name;
                    $tempNameFile = 'uploads' . DS . $child->type . DS . $child->name;

                    if (Storage::disk('s3')->exists($nameFile)) {
                        if (!file_exists($temp_dir)) {
                            mkdir($temp_dir, 0755, true);
                            chmod($temp_dir, 0777);
                            }
                        $full_path_file = $temp_dir. DS . $child->type.DS.$child->name;
                        if (!file_exists(dirname($full_path_file))) {
                            mkdir(dirname($full_path_file), 0755, true);
                            chmod(dirname($full_path_file), 0777);
                            }
                        file_put_contents($full_path_file, Storage::disk('s3')->get($nameFile));
                        /* ImageHash start */
                        $hash = hash_file('sha256', $full_path_file);
                        $child->hash = $hash;
                        $child->save();

                        /* ImageHash end */

                        \File::deleteDirectory($temp_dir);


                    }
                }


            }
        });

        \Log::channel('hashfile')->info('end hash images '.now());

    }
}
