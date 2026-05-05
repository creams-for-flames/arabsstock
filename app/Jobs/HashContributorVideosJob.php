<?php

namespace App\Jobs;

use App\Models\ContributorVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HashContributorVideosJob implements ShouldQueue
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

        \Log::channel('hashfile')->info('start hash ContributorVideo '.now());
        \DB::table('contributor_videos')->update(['hash'=>NULL]);
        ContributorVideo::orderBy('id','desc')->chunk(200, function ($items)  {

            foreach ($items as $key => $item) {
                $item_path= 'uploads' . DS . 'contributor_videos' . DS . $item->id;
                $orginal_path= 'uploads' . DS . 'contributor_videos' . DS . $item->id .DS. 'orginal' .DS. $item->preview;
                $temp_dir=public_path($orginal_path);
                if (Storage::disk('s3')->exists($orginal_path)) {
                \Log::channel('hashfile')->info('ContributorVideo : '.$temp_dir);

                    if (!file_exists(public_path($item_path))) {
                        mkdir(public_path($item_path), 0777, true);
                        chmod(public_path($item_path), 0777);
                    }
                        if (!file_exists(dirname($temp_dir))) {
                        mkdir(dirname($temp_dir), 0777, true);
                        chmod(dirname($temp_dir), 0777);
                        }

                    file_put_contents($temp_dir, Storage::disk('s3')->get($orginal_path));
                    /* ContributorVideo start */
                    $hash = hash_file('sha256', $temp_dir);
                    $item->hash = $hash;
                    $item->save();

                    /* ContributorVideo end */
                      \File::deleteDirectory(\public_path($item_path));


                }



            }
        });

        \Log::channel('hashfile')->info('end hash ContributorVideo '.now());

    }
}
