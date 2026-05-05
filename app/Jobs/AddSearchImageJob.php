<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AddSearchImageJob implements ShouldQueue
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
        \Log::info('start create image search image '.now());
        $images = Image::whereNull('serarch')->take(5)->get();
        try {
            foreach ($images as $image){
                addMaskedImages($image);
                \Log::info('Done Image : '.$image->id);
            }
        }
        catch (\Throwable $th) {
            \Log::error("Add Search Image ". $th->getMessage().' line: '. $th->getLine());
        }
        \Log::info('end create image search image '.now());
    }
}
