<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeoImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* 
        search : uploads/images/2361/image-2361-a-saudi-gulf-businessman-on-an-office-talking-on-a-mobile-p-search.jpg
        search_large : /uploads/search_large-image-2361-a-saudi-gulf-businessman-on-an-office-talking-on-a-mobile-p.jpg
        */
        Log::channel('info')->info("Start SeoImages Move  id: {$this->id}");

        $image = Image::find($this->id);
        if ($image) {
            $new_preview = "uploads/images/{$this->id}/" . strtolower(Str::limit($image->slug, 70, '')) . "-preview." . pathinfo($image->preview, PATHINFO_EXTENSION);
            $new_thumbnail = "uploads/images/{$this->id}/" . strtolower(Str::limit($image->slug, 70, '')) . "-thumbnail." . pathinfo($image->thumbnail, PATHINFO_EXTENSION);
            $search = "uploads/images/{$this->id}/" . strtolower(Str::limit($image->slug, 70, '')) . "-search." . pathinfo($image->search, PATHINFO_EXTENSION);
            $search_large = "uploads/images/{$this->id}/" . strtolower(Str::limit($image->slug, 70, '')) . "-search_large." . pathinfo($image->search_large, PATHINFO_EXTENSION);
            if (Storage::disk('s3')->exists($image->preview) && ($new_preview !== $image->preview)) {
                Storage::disk('s3')->move($image->preview, $new_preview);
                Log::channel('info')->info("Move {$image->preview} to {$new_preview}");
                $image->preview = $new_preview;
            }
            if (Storage::disk('s3')->exists($image->thumbnail) && ($new_thumbnail !== $image->thumbnail)) {
                Storage::disk('s3')->move($image->thumbnail, $new_thumbnail);
                Log::channel('info')->info("Move {$image->thumbnail} to {$new_thumbnail}");
                $image->thumbnail = $new_thumbnail;
            }

            if (Storage::disk('s3')->exists($image->search) && ($search !== $image->search)) {
                Storage::disk('s3')->move($image->search, $search);
                Log::channel('info')->info("Move {$image->search} to {$search}");
                $image->search = $search;
            }

            if (Storage::disk('s3')->exists($image->search_large) && ($search_large !== $image->search_large)) {
                Storage::disk('s3')->move($image->search_large, $search_large);
                Log::channel('info')->info("Move {$image->search_large} to {$search_large}");
                $image->search_large = $search_large;
            }

            $image->save();
            cache()->delete("image_show_{$this->id}_ar");
            cache()->delete("image_show_{$this->id}_en");
        }
        Log::channel('info')->info("End SeoImages Move  id: {$this->id}");

    }
}

