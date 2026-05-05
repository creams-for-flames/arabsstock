<?php

use App\Models\Videos;
use App\Helper;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

function watermark_video($input, $output, $height)
{
    $ffmpeg = env('FFMPEG_PATH');
    // TODO resize water mark to same width as the video
    $watermarkSource = base_path('public/img/watermark.png');

    $cmd = "$ffmpeg -hide_banner -loglevel warning -i $input -i $watermarkSource -pix_fmt yuv420p -filter_complex '[1:v] scale=-2:$height [logo1]; [0:v][logo1] overlay=(main_w-overlay_w)/2:(main_h-overlay_h)/2' -an $output";
    echo 'watermark_video: ' . $cmd;
    ob_start();
    passthru($cmd);
    $output = trim(ob_get_contents());
    ob_end_clean();
}

$videos = Videos::whereNull('parent_id')->whereNull('deleted_at')
    ->get();

foreach ($videos as $video) {
    echo 'process_video: ' . $video->id . "\n";

    $parts = explode('/', $video->size_240p);
    $size_240p = "./" . end($parts);
    $thumbnail_name_video_output = "./" . strtolower(time() . str_random(5) . '.mp4');
    file_put_contents($size_240p, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $video->size_240p));

    watermark_video($size_240p, $thumbnail_name_video_output, 240);

    $path_prefix_relative = DS . 'uploads' . DS . 'videos' . DS . $video->id . DS;

    if ($video->size_240p) {
        Storage::disk('s3')->delete($video->size_240p);
    }

    $old_size_240p = $video->size_240p;
    $video->size_240p = $path_prefix_relative . '240pwatermarked_' . str_random(20) . '.mp4';
    $video->save();

    Storage::disk('s3')->put($video->size_240p, file_get_contents($thumbnail_name_video_output));
    Storage::disk('s3')->delete($old_size_240p);

    if ($size_240p) {
        unlink($size_240p);
    }
    if ($thumbnail_name_video_output) {
        unlink($thumbnail_name_video_output);
    }
    echo "Done\n";
}
