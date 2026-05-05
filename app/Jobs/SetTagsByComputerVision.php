<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetTagsByComputerVision implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $params;

    public function __construct($params)
    {
        // params = ['image_id', 'image_url', 'video_id', 'video_url']
        $this->params = $params;
    }

    public function handle()
    {
        if (isset($this->params['image_id'])) {
            $this->handle_image($this->params['image_id']);
        }
        if (isset($this->params['video_id'])) {
            $this->handle_video($this->params['video_id']);
        }
        if (isset($this->params['vector_id'])) {
            $this->handle_vector($this->params['vector_id']);
        }
    }

    private function handle_image($image_id)
    {
        $image = Image::findOrFail($image_id);
        list($response, $httpcode) = $this->get_tags_from_imagga(env('DO_SPACES_URL') . '/' . $image->small);;
        if ($httpcode === 200) {

            $json_response = json_decode($response);
            $data = [];
            foreach ($json_response->result->tags as $tag) {
                $data[] = [
                    'image_id' => $image_id,
                    'confidence' => $tag->confidence,
                    'tag_ar' => $tag->tag->ar,
                    'tag_en' => $tag->tag->en,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            \DB::table('computer_vision_image_tags')->insert($data);
        } else {
            \Log::error('computer_vision_tags not set for image ' . $image_id);
        }
    }

    private function handle_video($video_id)
    {
        $image_url = \DB::table('videos')->where('id', $video_id)->value('thumbnail');
        // generate thumbnail
        list($response, $httpcode) = $this->get_tags_from_imagga(env('DO_SPACES_URL') . '/' . $image_url);;

        if ($httpcode === 200) {

            $json_response = json_decode($response);
            $data = [];
            foreach ($json_response->result->tags as $tag) {
                $data[] = [
                    'video_id' => $video_id,
                    'image' => $image_url,
                    'confidence' => $tag->confidence,
                    'tag_ar' => $tag->tag->ar,
                    'tag_en' => $tag->tag->en,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            \DB::table('computer_vision_video_tags')->insert($data);
        } else {
            \Log::error('computer_vision_tags not set for video ' . $video_id);
        }
    }

    private function handle_vector($vector_id)
    {
        $image_url = \DB::table('vectors')->where('id', $vector_id)->value('thumbnail');
        // generate thumbnail
        list($response, $httpcode) = $this->get_tags_from_imagga(env('DO_SPACES_URL') . '/' . $image_url);;

        if ($httpcode === 200) {

            $json_response = json_decode($response);
            $data = [];
            foreach ($json_response->result->tags as $tag) {
                $data[] = [
                    'vector_id' => $vector_id,
                    'confidence' => $tag->confidence,
                    'tag_ar' => $tag->tag->ar,
                    'tag_en' => $tag->tag->en,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }

            \DB::table('computer_vision_vector_tags')->insert($data);
        } else {
            \Log::error('computer_vision_tags not set for vector ' . $vector_id);
        }
    }

    private function get_tags_from_imagga($image_url)
    {
        # curl example
        # curl -u "user:password" "https://api.imagga.com/v2/tags?language=ar,en&image_url=https://imagga.com/static/images/tagging/wind-farm-538576_640.jpg"
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.imagga.com/v2/tags?language=ar,en&image_url=' . urlencode($image_url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_USERPWD, env('IMAGGA_USER') . ':' . env('IMAGGA_PASSWORD'));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$response, $httpcode];
    }
}
