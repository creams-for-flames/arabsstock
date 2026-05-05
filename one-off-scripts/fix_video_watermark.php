<?php

use App\Models\Videos;
use App\Helper;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

function watermark_sd($input, $output)
{
    $sd_height = 480;
    $ffmpeg = env('FFMPEG_PATH');
    // TODO resize water mark to same width as the video
    $watermarkSource = base_path('public/img/watermark.png');

    $cmd = "$ffmpeg -hide_banner -loglevel warning -i $input -i $watermarkSource -filter_complex '[1:v] scale=-2:$sd_height [logo1]; [0:v][logo1] overlay=(main_w-overlay_w)/2:(main_h-overlay_h)/2' -codec:a copy $output";
    echo 'watermark_sd: ' . $cmd;
    ob_start();
    passthru($cmd);
    $output = trim(ob_get_contents());
    ob_end_clean();
}

$videos = Videos::whereNull('parent_id')->whereNull('deleted_at')
    ->get();

foreach ($videos as $video) {
    echo 'process_video: ' . $video->id . "\n";
    $sd_video = Videos::where('parent_id', $video->id)
        ->where('type', 'SD')
        ->first();

    $parts = explode('/', $sd_video->preview);
    $sd_path = "./" . end($parts);
    $thumbnail_name_video_output = "./" . strtolower(time() . str_random(5) . '.' . $sd_video->extension);
    file_put_contents($sd_path, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $sd_video->preview));

    watermark_sd($sd_path, $thumbnail_name_video_output);

    Storage::disk('s3')->put($video->cut_video, file_get_contents($thumbnail_name_video_output));

    if ($sd_path) {
        unlink($sd_path);
    }
    if ($thumbnail_name_video_output) {
        unlink($thumbnail_name_video_output);
    }
    echo "Done\n";
}
