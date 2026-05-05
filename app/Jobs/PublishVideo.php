<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CategoryVideo;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublishVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $contributor_video_id;
    private $original_name;
    private $original_type;
    private $extension;
    private $hash;
    private $preview;
    private $title_en;
    private $title_ar;
    private $user_id;
    private $user_type;
    private $duration;
    private $tags_en;
    private $tags_ar;
    private $category_ids;
    private $contributor;
    private $reviewer_id;
    private $reviewed_at;
    private $publisher_id;
    private $published_at;
    private $how_use_image;

    public function __construct($params)
    {
        $this->onQueue('video');
        $this->contributor_video_id = $params['contributor_video_id'];
        $this->original_name = $params['original_name'];
        $this->original_type = $params['original_type'];
        $this->extension = $params['extension'];
        $this->hash = $params['hash'];
        $this->preview = $params['preview'];
        $this->title_en = $params['title_en'];
        $this->title_ar = $params['title_ar'];
        $this->user_id = $params['user_id'];
        $this->user_type = $params['user_type'];
        $this->duration = $params['duration'];
        $this->tags_en = $params['tags_en'];
        $this->tags_ar = $params['tags_ar'];
        $this->category_ids = $params['category_ids'];
        $this->contributor = $params['contributor'];
        $this->reviewer_id = $params['reviewer_id'];
        $this->reviewed_at = $params['reviewed_at'];
        $this->publisher_id = $params['publisher_id'];
        $this->published_at = $params['published_at'];
        $this->how_use_image = $params['how_use_image'];

    }

    public function handle()
    {
        $data_file = Video::where('contributor_video_id', $this->contributor_video_id)->first();
        if ($data_file) {
            \Log::channel('info')->info('Video exist contributor_video_id: ' . $data_file->contributor_video_id . ' video_id: ' . $data_file->id);
            return;
        }
        $orginal_path = $this->preview;
        $temp_large = \basename($this->preview);
        if (!file_exists(public_path('temp')))
            mkdir(public_path('temp'));
        file_put_contents(public_path('temp/' . $temp_large), Storage::disk('s3')->get($orginal_path));

        $extension = $this->extension;
        $name = Str::random(20) . '.' . $extension;

        $video = new Video();

        $video->title_ar = $this->title_ar;
        $video->title_en = $this->title_en;
        $video->description_ar = '';
        $video->description_en = '';
        $video->user_id = $this->user_id;
        $video->user_type = $this->user_type;
        $video->status = 'active';
        $video->token_id = Str::random(200);
        $video->extension = strtolower($extension);
        $video->how_use_image = $this->how_use_image ?? 'free';
        $video->attribution_required = 'no';
        $video->original_name = $this->original_name;
        $video->hash = $this->hash;

        $video->reviewer_id = $this->reviewer_id;
        $video->reviewed_at = $this->reviewed_at;
        $video->publisher_id = $this->publisher_id;
        $video->published_at = $this->published_at;

        $video->preview = $name;
        $video->duration = $this->duration;
        $video->contributor_video_id = $this->contributor_video_id;

        $video->save();

        $video->slug = 'clip-' . $video->id . '-' . slugify_v2($video->title_en);
        $video->save();

        $new_folder = public_path(DS . 'uploads' . DS . 'videos' . DS . $video->id);
        \Log::channel('info')->info($new_folder);
        if (!file_exists($new_folder)) {
            mkdir($new_folder, 0755, true);
        }

        $temp_video_path = public_path('temp/' . $temp_large);
        $new_video_path = $new_folder . DS . $name;
        // remove audio from file
        $this->remove_audio($temp_video_path, $new_video_path);
        //delete temp file
        if (file_exists($temp_video_path)) {
            unlink($temp_video_path);
        }

        if (count($this->tags_en)) {
            \sync_tags($video, $this->tags_en, 'en');
        }
        if (count($this->tags_ar)) {
            \sync_tags($video, $this->tags_ar, 'ar');
        }
        if (count($this->category_ids)) {
            $cateogries = $this->category_ids;

            for ($i = 0; $i < count($cateogries); $i++) {
                CategoryVideo::create([
                    'video_id' => $video->id,
                    'category_id' => $cateogries[$i],
                ]);
            }
        }
        dispatch(new ConvertVideoForStreaming($video, $this->contributor));

    }

    public function remove_audio($video_path, $new_path)
    {
        $ffmpeg = env('FFMPEG_PATH');
        $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$video_path} -c copy -an {$new_path}";
        \Log::channel('info')->info('remove_audio: ' . $cmd);
        ob_start();
        passthru($cmd);
        ob_end_clean();
    }
}
