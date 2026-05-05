<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PublishAdminVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $video;

    public function __construct($video)
    {
        $this->video = $video;
    }

    public function handle()
    {
        $path = DS . 'uploads' . DS . 'videos' . DS . $this->video->id . DS . $this->video->preview;
        if (!file_exists(pathinfo(public_path($path), PATHINFO_DIRNAME)))
            mkdir(pathinfo(public_path($path), PATHINFO_DIRNAME), 0777, true);
        file_put_contents(public_path($path), Storage::disk('s3')->get($path));
        dispatch(new ConvertVideoForStreaming($this->video, null))->onQueue(env('VIDEO_PROCESSING_QUEUE'));
    }
}
