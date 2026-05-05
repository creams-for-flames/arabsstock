<?php

namespace App\Jobs;

use App\Models\ContributorImage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HashContributorImagesJob implements ShouldQueue
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

        \Log::channel('info')->info('start hash ContributorImage '.now());
        \DB::table('contributor_images')->update(['hash'=>NULL]);
        ContributorImage::orderBy('id','desc')->chunk(200, function ($items)  {

            foreach ($items as $key => $item) {
                $item_path= 'uploads' . DS . 'contributor_images' . DS . $item->id;
                $orginal_path= 'uploads' . DS . 'contributor_images' . DS . $item->id .DS. 'orginal' .DS. $item->large;
                $temp_dir=public_path($orginal_path);
                if (Storage::disk('s3')->exists($orginal_path)) {
                \Log::channel('info')->info('ContributorImage'.$temp_dir);
                    if (!file_exists(public_path($item_path))) {
                        mkdir(public_path($item_path), 0777, true);
                        chmod(public_path($item_path), 0777);
                    }

                    if (!file_exists(dirname($temp_dir))) {
                        mkdir(dirname($temp_dir), 0755, true);
                        chmod(dirname($temp_dir), 0777);
                        }

                    file_put_contents($temp_dir, Storage::disk('s3')->get($orginal_path));
                    /* ImageHash start */
                    $hash = hash_file('sha256', $temp_dir);
                    $item->hash = $hash;
                    $item->save();

                    /* ImageHash end */
                     \File::deleteDirectory(\public_path($item_path));


                }



            }
        });

        \Log::channel('info')->info('end hash ContributorImage '.now());

    }
}
