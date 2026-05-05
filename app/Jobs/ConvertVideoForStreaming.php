<?php

namespace App\Jobs;

use App\Models\ContributorVideo;
use App\Models\Video;
use App\Models\ContributorVideos;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class ConvertVideoForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CONTRIBUTOR_STAGE_PUBLISH = 8;
    private $video;
    private $contributor = null;
    private $details = null;
    private $path_prefix = "";
    private $path_prefix_relative = "";
    private $original_path = "";
    private $extension = "";
    private $video_name_240p = "";
    private $video_name_sd = "";
    private $video_name_hd = "";
    private $video_name_fhd = "";
    private $video_name_4k = "";
    private $thumbnail_name_image = "";
    private $thumbnail_name_gif = "";
    private $thumbnail_name_video = "";

    public function __construct(Video $video, $contributor)
    {
        $this->video = $video;
        $this->contributor = $contributor ?? NULL;
    }

    public function handle()
    {
        $this->convertVideo($this->video);

    }

    public function convertVideo($video)
    {

        \Log::channel('info')->info("video_id: {$video->id} --- step1");
        $orginalVideo = Video::find($video->id);
        // TODO add validation in uploader
        // must at least bigger than sd height (480)
        $admin_video_setting = new \App\Models\AdminVideoSettings();

        $video_settings = $admin_video_setting->first();


        $source_video_dir = base_path('public/tempVideo');
        $extension = pathinfo($video->preview, PATHINFO_EXTENSION);
        $this->extension = strtolower($extension);
        $nameFile = explode('.', $video->preview);
        $orignalType = '';

        $ffmpeg = env('FFMPEG_PATH');

        $this->path_prefix_relative = DS . 'uploads' . DS . 'videos' . DS . $video->id . DS;

        $this->path_prefix = public_path($this->path_prefix_relative);

        $this->video_name_sd = 'sd_' . Str::random(20) . '.' . $this->extension;
        $this->video_name_hd = 'hd_' . Str::random(20) . '.' . $this->extension;
        $this->video_name_fhd = 'fhd_' . Str::random(20) . '.' . $this->extension;
        $this->video_name_4k = '4k_' . Str::random(20) . '.' . $this->extension;
        $this->thumbnail_name_image = 'thumbnail_' . Str::random(20) . '.png';
        $this->thumbnail_name_gif = 'thumbnail_' . Str::random(20) . '.gif';
        $this->thumbnail_name_video = 'thumbnail_' . Str::random(20) . '.mp4';
        $this->video_name_240p = '240p_' . Str::random(20) . '.mp4';
        $this->video_name_240p_watermarked = '240pwatermarked_' . Str::random(20) . '.mp4';


        $this->original_path = $this->path_prefix . $video->preview;
        $video_name_output_sd = $this->path_prefix . $this->video_name_sd;
        $video_name_output_hd = $this->path_prefix . $this->video_name_hd;
        $video_name_output_fhd = $this->path_prefix . $this->video_name_fhd;
        $video_name_output_4k = $this->path_prefix . $this->video_name_4k;

        $video_name_output_240p = $this->path_prefix . $this->video_name_240p;
        $video_name_output_240p_watermarked = $this->path_prefix . $this->video_name_240p_watermarked;
        $thumbnail_name_output = $this->path_prefix . $this->thumbnail_name_image;
        $thumbnail_name_gif_output = $this->path_prefix . $this->thumbnail_name_gif;
        $thumbnail_name_video_output = $this->path_prefix . $this->thumbnail_name_video;

        ini_set('memory_limit', -1);

        $this->details = $this->get_video_details($this->original_path);
        $aspect_ratio = $this->details->width / $this->details->height;
        $filesize_in_bytes = filesize($this->original_path);

        $this->generate_video_thumbnail_image($this->original_path, $thumbnail_name_output, $aspect_ratio);

        if (!file_exists($thumbnail_name_output)) {
            \Log::error('no thumbnail for video : ' . $video->id);
            $orginalVideo = Video::findOrFail($video->id);
            $orginalVideo->type = $orignalType;
            $orginalVideo->width = $this->details->width;
            $orginalVideo->height = $this->details->height;
            $orginalVideo->size = $filesize_in_bytes;
            $orginalVideo->video_fail = 1;
            $orginalVideo->save();
            return;
        }

        $this->generate_video_gif($this->original_path, $thumbnail_name_gif_output);

        // SD  # X 480
        // HD  # X 720
        // FHD # X 1080
        // 4K  # X 2160
        if ($this->details->height >= 480 && $this->details->height < 720) {
            $orignalType = 'SD';
        }
        if ($this->details->height >= 720 && $this->details->height < 1080) {
            $orignalType = 'HD';
        }
        if ($this->details->height >= 1080 && $this->details->height < 2160) {
            $orignalType = 'FHD';
        }
        if ($this->details->height >= 2160) {
            $orignalType = '4K';
        }

        if (in_array($orignalType, ['SD', 'HD', 'FHD', '4K'])) {
            $this->create_240p($video, $aspect_ratio);
            $this->watermark_video($video_name_output_240p, $video_name_output_240p_watermarked, 240);
            $this->create_sd($video, $aspect_ratio, $video_settings);
            $this->watermark_video($video_name_output_sd, $thumbnail_name_video_output, 480);
        }
        if (in_array($orignalType, ['HD', 'FHD', '4K'])) {
            $this->create_hd($video, $aspect_ratio, $video_settings);
        }
        if (in_array($orignalType, ['FHD', '4K'])) {
            $this->create_fhd($video, $aspect_ratio, $video_settings);
        }
        if (in_array($orignalType, ['4K'])) {
            $this->create_4k($video, $aspect_ratio, $video_settings);
        }
        \Log::channel('info')->info("video_id: {$video->id} --- step2");
        $hash = hash_file('sha256', $this->original_path);
        \Log::channel('info')->info("video_id: {$video->id} --- step3");

        $orginalVideo->type = $orignalType;
        $orginalVideo->width = $this->details->width;
        $orginalVideo->height = $this->details->height;
        $orginalVideo->aspect_ratio = $this->details->display_aspect_ratio ?? '';
        $orginalVideo->fps = $this->details->avg_frame_rate;
        $orginalVideo->size = $filesize_in_bytes;
        $orginalVideo->thumbnail = $this->path_prefix_relative . $this->thumbnail_name_image;
        $orginalVideo->thumbnail_width = getWidth(public_path($this->path_prefix_relative . $this->thumbnail_name_image));
        $orginalVideo->thumbnail_height = getHeight(public_path($this->path_prefix_relative . $this->thumbnail_name_image));
        $orginalVideo->cut_video = $this->path_prefix_relative . $this->thumbnail_name_video;
        $orginalVideo->gif_video = $this->path_prefix_relative . $this->thumbnail_name_gif;
        $orginalVideo->gif_video_width = 300;
        $orginalVideo->gif_video_height = intval(300 * $this->details->height / $this->details->width);
        $orginalVideo->size_240p = $this->path_prefix_relative . $this->video_name_240p_watermarked;
        $orginalVideo->size_240p_width = 426;
        $orginalVideo->hash = $hash;
        $orginalVideo->size_240p_height = 240;
        \Log::channel('info')->info("video_id: {$video->id} --- step4");
        $orginalVideo->save();
        \Log::channel('info')->info("video_id: {$video->id} --- step5");

        // TODO use in process flag 'video_under_processing'

        $orginal_video_new_path = $this->path_prefix_relative . $orginalVideo->preview;
        $data = [
            'orginal_video_new_path' => $orginal_video_new_path,
            'original_path' => $this->original_path,
            'thumbnail' => $orginalVideo->thumbnail,
            'cut_video' => $orginalVideo->cut_video,
            'gif_video' => $orginalVideo->gif_video,
            'size_240p' => $orginalVideo->size_240p,
            'orignalType' => $orignalType,
            'path_prefix_relative' => $this->path_prefix_relative,
            'path_prefix' => $this->path_prefix,
            'video_name_sd' => $this->video_name_sd,
            'video_name_hd' => $this->video_name_hd,
            'video_name_fhd' => $this->video_name_fhd,
            'video_name_4k' => $this->video_name_4k,
            'video_name_output_sd' => $video_name_output_sd,
            'video_name_output_hd' => $video_name_output_hd,
            'video_name_output_fhd' => $video_name_output_fhd,
            'video_name_output_4k' => $video_name_output_4k,
            'id' => $video->id,
        ];
        \Log::channel('info')->info("video_id: {$video->id} --- step6");
        dispatch(
            new UploadVideoS3($data)
        )->onQueue(env('VIDEO_PROCESSING_QUEUE'));


        // $this->upload_to_s3($orginal_video_new_path, file_get_contents($this->original_path));
        // $this->upload_to_s3($orginalVideo->thumbnail, file_get_contents(public_path($orginalVideo->thumbnail)));
        // $this->upload_to_s3($orginalVideo->cut_video, file_get_contents(public_path($orginalVideo->cut_video)));
        // $this->upload_to_s3($orginalVideo->gif_video, file_get_contents(public_path($orginalVideo->gif_video)));
        // $this->upload_to_s3($orginalVideo->size_240p, file_get_contents(public_path($orginalVideo->size_240p)));

        // if (in_array($orignalType, ['SD', 'HD', 'FHD', '4K'])) {
        //     $this->upload_to_s3($this->path_prefix_relative . $this->video_name_sd, file_get_contents($video_name_output_sd));
        // }
        // if (in_array($orignalType, ['HD', 'FHD', '4K'])) {
        //     $this->upload_to_s3($this->path_prefix_relative . $this->video_name_hd, file_get_contents($video_name_output_hd));
        // }
        // if (in_array($orignalType, ['FHD', '4K'])) {
        //     $this->upload_to_s3($this->path_prefix_relative . $this->video_name_fhd, file_get_contents($video_name_output_fhd));
        // }
        // if (in_array($orignalType, ['4K'])) {
        //     $this->upload_to_s3($this->path_prefix_relative . $this->video_name_4k, file_get_contents($video_name_output_4k));
        // }

        // $this->delete_local_files();

        // Video::where('id', $video->id)->orWhere('parent_id', $video->id)->update(['is_uploaded' => 1]);

        $orginalVideo->preview = $this->path_prefix_relative . $orginalVideo->preview;
        $orginalVideo->save();

        // generate tags (must start after s3 store)
        dispatch(
            new SetTagsByComputerVision([
                'video_id' => $video->id,
            ])
        )->onQueue(env('VIDEO_PROCESSING_QUEUE'));
        if ($this->contributor) {
            if ($video->contributor_video_id !== 0) {
                ContributorVideo::where('id', $video->contributor_video_id)->update(
                    ['contributor_stage' => self::CONTRIBUTOR_STAGE_PUBLISH]
                );
            }
            $details = [
                'contributor' => $this->contributor,
                'type' => 'videos',
            ];

            SendEmailAlertContributorFilePublish::dispatch($details)->onConnection('arabsstock_redis');
        }
    }

    public function create_video($video, $params)
    {

        // @note maybe should generate all format on one step which share resources and it will be faster
        // ffmpeg -y -i input.mkv \
        // -filter_complex "
        //    [0:v]format=yuv420p,yadif,split=3[in1][in2][in3];
        //      [in1]scale=1920:1080[hd];
        //      [in2]scale=720:576,hflip[sd];
        //      [in3]fps=1/5,scale=320:180[thumbnails];
        //    [0:a]aresample=48000,asplit=2[a1][a2]" \
        // -map [hd] -map [a1] hd.mov \
        // -map [sd] -map [a2] sd-flipped.mp4 \
        // -map [thumbnails] thumbnail-%03d.jpg


        $new_video = new Video();
        $new_video->title_ar = trim($video->title_ar);
        $new_video->title_en = trim($video->title_en);
        $new_video->description_ar = trim($video->description_ar);
        $new_video->description_en = trim($video->description_en);
        $new_video->user_id = $video->user_id;
        $new_video->status = $video->status;
        $new_video->token_id = Str::random(200);
        $new_video->parent_id = $video->id;
        $new_video->extension = $this->extension;
        $new_video->how_use_image = $video->how_use_image;
        $new_video->attribution_required = $video->attribution_required;
        $new_video->original_name = $video->original_name;
        $new_video->save();

        // crf only appliable fro h264
        $ffmpeg_crf = $params['codec_name'] === 'h264' ? "-crf {$params['crf']}" : "";

        // to fix height-not-divisible-by-2 we prepare width and height
        // another solution is to add 1px padding https://stackoverflow.com/a/53024964/2570425

        $ffmpeg = env('FFMPEG_PATH');
        // use -2 for width so ffmpeg will calculate width
        // use 0 for crf so ffmpeg will do lossless converting
        if ($params['crop']) {
            $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$this->original_path} -c:v {$params['codec_name']} $ffmpeg_crf -vf 'scale=w={$params['width']}:h={$params['height']}:force_original_aspect_ratio=2,crop={$params['width']}:{$params['height']}' -an {$params['output']}";
        } else {
            $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$this->original_path} -c:v {$params['codec_name']} $ffmpeg_crf -vf scale='{$params['width']}:{$params['height']}' -an {$params['output']}";
        }

        \Log::channel('info')->info('create_video: ' . $cmd);
        ob_start();
        passthru($cmd);
        ob_end_clean();

        $filesize_in_bytes = filesize($params['output']);

        $details = $this->get_video_details($params['output']);
        $hash = hash_file('sha256', \public_path('') . $this->path_prefix_relative . $params['name']);
        \Log::channel('info')->info('new video : ' . \public_path('') . $this->path_prefix_relative . $params['name']);

        $new_video->thumbnail = $this->path_prefix_relative . $this->thumbnail_name_image;
        $new_video->preview = $this->path_prefix_relative . $params['name'];
        $new_video->type = $params['type'];
        $new_video->duration = $video->getAttributes()['duration'];
        $new_video->width = $details->width;
        $new_video->height = $details->height;
        $new_video->aspect_ratio = $details->display_aspect_ratio ?? "";
        $new_video->fps = $details->avg_frame_rate;
        $new_video->size = $filesize_in_bytes;
        $new_video->hash = $hash;
        $new_video->price = $params['price'];
        $new_video->save();


    }

    public function create_sd($video, $aspect_ratio, $settings)
    {
        $video_name_output_sd = $this->path_prefix . $this->video_name_sd;

        $this->create_video($video, [
            'codec_name' => $this->details->codec_name,
            'type' => 'SD',
            'width' => -2,
            'height' => '480',
            'name' => $this->video_name_sd,
            'output' => $video_name_output_sd,
            'price' => $settings->sd_price,
            'crf' => 15,
            'crop' => false,
        ]);
    }

    public function create_hd($video, $aspect_ratio, $settings)
    {
        $video_name_output_hd = $this->path_prefix . $this->video_name_hd;

        $this->create_video($video, [
            'codec_name' => $this->details->codec_name,
            'type' => 'HD',
            'width' => -2,
            'height' => '720',
            'name' => $this->video_name_hd,
            'output' => $video_name_output_hd,
            'price' => $settings->hd_price,
            'crf' => 15,
            'crop' => false,
        ]);
    }

    public function create_fhd($video, $aspect_ratio, $settings)
    {
        $video_name_output_fhd = $this->path_prefix . $this->video_name_fhd;

        $this->create_video($video, [
            'codec_name' => $this->details->codec_name,
            'type' => 'FHD',
            'width' => -2,
            'height' => '1080',
            'name' => $this->video_name_fhd,
            'output' => $video_name_output_fhd,
            'price' => $settings->fhd_price,
            'crf' => 15,
            'crop' => false,
        ]);
    }

    public function create_4k($video, $aspect_ratio, $settings)
    {
        $video_name_output_4k = $this->path_prefix . $this->video_name_4k;
        $this->create_video($video, [
            'codec_name' => $this->details->codec_name,
            'type' => '4K',
            'width' => -2,
            'height' => '2160',
            'name' => $this->video_name_4k,
            'output' => $video_name_output_4k,
            'price' => $settings->four_k_price,
            'crf' => 15,
            'crop' => false,
        ]);


    }

    public function get_video_details($path)
    {
        // {
        //     "streams": [
        //         {
        //             "index": 0,
        //             "codec_name": "h264",
        //             "codec_long_name": "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10",
        //             "profile": "High",
        //             "codec_type": "video",
        //             "codec_time_base": "1001/60000",
        //             "codec_tag_string": "avc1",
        //             "codec_tag": "0x31637661",
        //             "width": 1920,
        //             "height": 1080,
        //             "coded_width": 1920,
        //             "coded_height": 1088,
        //             "has_b_frames": 2,
        //             "sample_aspect_ratio": "1:1",
        //             "display_aspect_ratio": "16:9",
        //             "pix_fmt": "yuv420p",
        //             "level": 40,
        //             "chroma_location": "left",
        //             "refs": 1,
        //             "is_avc": "true",
        //             "nal_length_size": "4",
        //             "r_frame_rate": "30000/1001",
        //             "avg_frame_rate": "30000/1001",
        //             "time_base": "1/30000",
        //             "start_pts": 0,
        //             "start_time": "0.000000",
        //             "duration_ts": 109109,
        //             "duration": "3.636967",
        //             "bit_rate": "40069125",
        //             "bits_per_raw_sample": "8",
        //             "nb_frames": "109",
        //         }
        //     ]
        // }

        // $details->streams->width
        $ffprobe = env('FFPROBE_PATH');
        $cmd = "$ffprobe -hide_banner -loglevel error -select_streams v:0 -show_streams -of json $path";
        \Log::channel('info')->info('get_video_details: ' . $cmd);
        ob_start();
        passthru($cmd);
        $details = json_decode(trim(ob_get_contents()));
        ob_end_clean();

        $data = $details->streams[0];
        $numbers = explode('/', $data->avg_frame_rate);
        $data->avg_frame_rate = intval(round($numbers[0] / $numbers[1]));
        return $data;
    }

    public function generate_video_thumbnail_image($input, $output, $aspect_ratio)
    {
        $width = 720;
        $ffmpeg = env('FFMPEG_PATH');
        $cmd = "$ffmpeg -hide_banner -loglevel warning -itsoffset -1 -i $input -vframes 1 -filter:v scale='$width:-2' $output";
        \Log::channel('info')->info('generate_video_thumbnail_image: ' . $cmd);

        ob_start();
        passthru($cmd);
        $output = trim(ob_get_contents());
        ob_end_clean();
    }

    public function watermark_video($input, $output, $height)
    {
        $ffmpeg = env('FFMPEG_PATH');
        // TODO resize water mark to same width as the video
        $watermarkSource = base_path('public/img/watermark.png');

        $cmd = "$ffmpeg -hide_banner -loglevel warning -i $input -i $watermarkSource -pix_fmt yuv420p -filter_complex '[1:v] scale=-2:$height [logo1]; [0:v][logo1] overlay=(main_w-overlay_w)/2:(main_h-overlay_h)/2' -an $output";
        \Log::channel('info')->info('watermark_video: ' . $cmd);
        ob_start();
        passthru($cmd);
        $output = trim(ob_get_contents());
        ob_end_clean();
    }

    public function generate_video_gif($input, $output)
    {
        $ffmpeg = env('FFMPEG_PATH');
        $cmd = "$ffmpeg -hide_banner -loglevel warning -ss 1 -t 2 -i $input -vf scale=300:-2 -gifflags -transdiff -y $output";
        \Log::channel('info')->info('generate_video_gif: ' . $cmd);
        ob_start();
        passthru($cmd);
        $output = trim(ob_get_contents());
        ob_end_clean();
    }

    public function create_240p($video, $aspect_ratio)
    {
        $video_name_output_240p = $this->path_prefix . $this->video_name_240p;

        $params = [
            'type' => '240p',
            'width' => 426,
            'height' => 240,
            'output' => $video_name_output_240p,
            'crf' => 18,
        ];

        $ffmpeg = env('FFMPEG_PATH');
        $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$this->original_path} -pix_fmt yuv420p -c:v libx264 -preset medium -crf {$params['crf']} -vf 'scale=w={$params['width']}:h={$params['height']}:force_original_aspect_ratio=2,crop={$params['width']}:{$params['height']}' -an {$params['output']}";
        \log::info('create_video: ' . $cmd);
        ob_start();
        passthru($cmd);
        ob_end_clean();
    }

    public function upload_to_s3($path, $path_on_disk)
    {
        \Log::channel('info')->info('upload_to_s3 : ' . $path);
        $status = Storage::disk('s3')->put($path, $path_on_disk);
        if ($status === false) {
            \Log::error('could not upload file ' . $path_on_disk);
        }
    }

    public function delete_local_files()
    {
        \Log::channel('info')->info('delete_local_files : ' . $this->path_prefix);
        File::deleteDirectory($this->path_prefix);
    }

}
