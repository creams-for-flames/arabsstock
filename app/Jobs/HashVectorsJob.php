<?php

namespace App\Jobs;

use App\Models\Vector;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HashVectorsJob implements ShouldQueue
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
        \Log::channel('hashfile')->info('start hash Vector'.now());
        // \DB::table('vectors')->update(['hash'=>NULL]);
        Vector::where('large','!=','')->where('is_uploaded',1)->where('hash','')->orderBy('id','desc')->chunk(200, function ($items)  {
            foreach ($items as $key => $item) {
                $file_path = 'uploads' . DS . 'vectors' . DS . $item->id. DS . $item->large;

                $temp_dir=public_path($file_path);

               if (Storage::disk('s3')->exists($file_path)) {
                    if (!file_exists(dirname($temp_dir))) {
                    mkdir(dirname($temp_dir), 0777, true);
                    chmod(dirname($temp_dir), 0777);
                    }
                    file_put_contents($temp_dir, Storage::disk('s3')->get($file_path));
                    $hash = hash_file('sha256', $temp_dir);
                    \Log::channel('hashfile')->info('hash Vector : '.$temp_dir);
                    if($hash && $hash != ''){
                        $item->hash = $hash;
                        $item->save();
                    }
                    if (file_exists(dirname($temp_dir))) {
                         \File::deleteDirectory(dirname($temp_dir));
                    }
               }
           }
        });
        \Log::channel('hashfile')->info('end hash Vector '.now());

    }
}
