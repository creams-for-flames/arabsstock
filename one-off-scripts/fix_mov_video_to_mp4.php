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

    $cmd = "$ffmpeg -hide_banner -loglevel warning -i $input -i $watermarkSource -pix_fmt yuv420p -filter_complex '[1:v] scale=-2:$sd_height [logo1]; [0:v][logo1] overlay=(main_w-overlay_w)/2:(main_h-overlay_h)/2' -codec:a copy $output";
    echo 'watermark_sd: ' . $cmd;
    ob_start();
    passthru($cmd);
    $output = trim(ob_get_contents());
    ob_end_clean();
}

function create_240p($input, $output)
{
    $params = [
        'type' => '240p',
        'width' => 426,
        'height' => 240,
        'output' => $output,
        'crf' => 18,
    ];

    $ffmpeg = env('FFMPEG_PATH');
    $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$input} -pix_fmt yuv420p -c:v libx264 -preset medium -crf {$params['crf']} -vf 'scale=w={$params['width']}:h={$params['height']}:force_original_aspect_ratio=2,crop={$params['width']}:{$params['height']}' -c:a copy  {$params['output']}";
    echo 'create_video: ' . $cmd;
    ob_start();
    passthru($cmd);
    ob_end_clean();
}

$videos = Videos::whereNull('parent_id')
    ->where('extension', 'mov')
    ->get();

foreach ($videos as $video) {
    echo 'process_video: ' . $video->id . "\n";
    $sd_video = Videos::where('parent_id', $video->id)
        ->where('type', 'SD')
        ->first();

    $parts = explode('/', $video->preview);
    $original_path = "./" . end($parts);
    $parts = explode('/', $sd_video->preview);
    $sd_path = "./" . end($parts);
    $thumbnail_name_video_output = "./" . strtolower(time() . str_random(5) . '.mp4');
    $p240_output = "./" . strtolower(time() . str_random(5) . '.mp4');
    file_put_contents($original_path, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $video->preview));
    file_put_contents($sd_path, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $sd_video->preview));

    create_240p($original_path, $p240_output);
    watermark_sd($sd_path, $thumbnail_name_video_output);

    $path_prefix_relative = DS . 'uploads' . DS . 'videos' . DS . $video->id . DS;

    if ($video->size_240p) {
        Storage::disk('s3')->delete($video->size_240p);
    }
    if ($video->cut_video) {
        Storage::disk('s3')->delete($video->cut_video);
    }

    $video->size_240p = $path_prefix_relative . '240p_' . str_random(20) . '.mp4';
    $video->cut_video = $path_prefix_relative . 'thumbnail_' . str_random(20) . '.mp4';
    $video->save();

    Storage::disk('s3')->put($video->size_240p, file_get_contents($p240_output));
    Storage::disk('s3')->put($video->cut_video, file_get_contents($thumbnail_name_video_output));

    if ($original_path) {
        unlink($original_path);
    }
    if ($sd_path) {
        unlink($sd_path);
    }
    if ($thumbnail_name_video_output) {
        unlink($thumbnail_name_video_output);
    }
    if ($p240_output) {
        unlink($p240_output);
    }
    echo "Done\n";
}
