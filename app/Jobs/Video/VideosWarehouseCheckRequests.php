<?php

namespace App\Jobs\Video;

use App\Models\Video;
use App\Models\WarehouseCheckRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VideosWarehouseCheckRequests implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
        $this->onQueue('fast');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        WarehouseCheckRequest::findOrFail($id);
        $data = Video::with('child')->whereNull('parent_id')->orderBy('id','desc')->get();
        foreach ($data as $key => $item) {
            dispatch(new \App\Jobs\VideosWarehouseChecks($item,$id));

        }

    }
}
