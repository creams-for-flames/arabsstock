<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HashVideosJob implements ShouldQueue
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
        \Log::channel('hashfile')->info('start hash Video'.now());
        Video::where('preview','!=','')->where('video_fail',0)->where('is_uploaded',1)->orderBy('id','desc')->chunk(200, function ($items)  {
            foreach ($items as $key => $item) {
               $video_path_temp=public_path($item->preview);
               $video_dir = dirname($video_path_temp);
               if (Storage::disk('s3')->exists($item->preview)) {
                    if (!file_exists($video_dir)) {
                    mkdir($video_dir, 0777, true);
                    chmod($video_dir, 0777);
                    }
                    file_put_contents($video_path_temp, Storage::disk('s3')->get($item->preview));
                    $hash = hash_file('sha256', $video_path_temp);
                    \Log::channel('hashfile')->info('hash Video : '.$video_path_temp);
                    if($hash && $hash != ''){
                        $item->hash = $hash;
                        $item->save();
                    }
                    if (file_exists($video_dir)) {
                         \File::deleteDirectory($video_dir);
                    }
               }
           }
        });
        \Log::channel('hashfile')->info('end hash Video '.now());

    }
}
