<?php

namespace App\Jobs;

use App\Helper;
use App\ContributorVideo;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\ContributorImage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FixContributorVideosJob implements ShouldQueue
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
        \Log::channel('info')->info("start FixContributorVideosJob - ".now());

        try {
            $user_id = 2;
            $videos = ContributorVideo::whereIn('contributor_stage', [2,3])
            ->where('user_id','!=',$user_id)
             ->where('preview_admin','!=','')
            ->get();

            foreach ($videos as $key => $file) {
                $original_file_path= 'uploads' . DS . 'contributor_videos' . DS . $file->id .DS.'orginal'.DS. $file->preview;
                $original = public_path($original_file_path);
                $root_dir = public_path('uploads' . DS . 'contributor_videos' . DS . $file->id);

                if (!file_exists($root_dir)) {
                    mkdir($root_dir, 0777, true);
                    chmod($root_dir,0777);
                }
                if (!file_exists(dirname($original))) {
                    mkdir(dirname($original), 0777, true);
                    chmod(dirname($original),0777);
                }
                file_put_contents($original, Storage::disk('s3')->get($original_file_path));
                // PREVIEW
                $extension  = 'mp4';
                $preview = $file->preview_admin; //strtolower(\Illuminate\Support\Str::slug(Str::random(15), '-') . '-' . time() . str_random(5) . '.' . $extension);
                $preview_without_watermark ='preview_without_watermark_'.$file->id.$preview;
                $file_path_preview = 'uploads' . DS . 'contributor_videos' . DS . $file->id  .DS. 'preview'.DS. $preview_without_watermark;
               $dir_preview = 'uploads' . DS . 'contributor_videos' . DS . $file->id  .DS. 'preview'.DS;
                $file_path_preview_with_watermark = $dir_preview. $preview;
                $path_preview = \public_path($file_path_preview);
                $path_preview_watermark = \public_path($file_path_preview_with_watermark);
                if (!file_exists(dirname($path_preview))) {
                    mkdir(dirname($path_preview), 0777, true);
                    chmod(dirname($path_preview),0777);

                }
                ////////
                $this->create_240p($original, $path_preview);
                $this->watermark_video($path_preview, $path_preview_watermark,300);
                if(Storage::disk('s3')->exists($dir_preview))
                {
                    Storage::disk('s3')->deleteDirectory($dir_preview);
                }
                Storage::disk('s3')->put($file_path_preview_with_watermark, file_get_contents($path_preview_watermark));// preview
                if (file_exists(dirname($root_dir))) {
                    \File::deleteDirectory($root_dir);
                }
                // $file->preview_admin = $preview;
                // $file->save();

                \Log::channel('info')->info("done FixContributorVideosJob - ". now() . ' file: '. $file_path_preview);

                # code...
            }

        } catch (\Throwable $th) {
            //throw $th;
            $error = $th->getMessage() .' line :  '.$th->getLine();
            \Log::channel('info')->info("error FixContributorVideosJob - ".$error);

        }
        \Log::channel('info')->info("end FixContributorVideosJob - ".now());

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
    public function create_240p($input, $output)
    {
        $params = [
            'type' => '240p',
            'width' => 720,
            'height' => 480,
            'output' => $output,
            'crf' => 18,
        ];
        $ffmpeg = env('FFMPEG_PATH');
        $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$input} -pix_fmt yuv420p -c:v libx264 -preset medium -crf {$params['crf']} -vf 'scale=w={$params['width']}:h={$params['height']}:force_original_aspect_ratio=2,crop={$params['width']}:{$params['height']}' -an {$params['output']}";
        \log::info('create_video: ' . $cmd);
        ob_start();
        passthru($cmd);
        ob_end_clean();
    }
}
