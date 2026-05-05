<?php

namespace App\Jobs;

use App\Models\ContributorVector;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HashContributorVectorsJob implements ShouldQueue
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

        \Log::channel('hashfile')->info('start hash ContributorVector '.now());
        \DB::table('contributor_vectors')->update(['hash'=>NULL]);
        ContributorVector::orderBy('id','desc')->chunk(200, function ($items)  {

            foreach ($items as $key => $item) {
                $item_path= 'uploads' . DS . 'contributor_vectors' . DS . $item->id;
                $orginal_path= 'uploads' . DS . 'contributor_vectors' . DS . $item->id .DS. 'orginal' .DS. $item->large;
                $temp_dir=public_path($orginal_path);
                if (Storage::disk('s3')->exists($orginal_path)) {
                \Log::channel('hashfile')->info('ContributorVector : '.$temp_dir);

                    if (!file_exists(public_path($item_path))) {
                        mkdir(public_path($item_path), 0777, true);
                        chmod(public_path($item_path), 0777);
                    }
                        if (!file_exists(dirname($temp_dir))) {
                        mkdir(dirname($temp_dir), 0777, true);
                        chmod(dirname($temp_dir), 0777);
                        }

                    file_put_contents($temp_dir, Storage::disk('s3')->get($orginal_path));
                    /* ContributorVector start */
                    $hash = hash_file('sha256', $temp_dir);
                    $item->hash = $hash;
                    $item->save();

                    /* ContributorVector end */
                    if (file_exists(\public_path($item_path))) {
                        \File::deleteDirectory(\public_path($item_path));
                    }


                }



            }
        });

        \Log::channel('hashfile')->info('end hash ContributorVector '.now());

    }
}
