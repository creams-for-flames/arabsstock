<?php

use App\Models\Videos;
use App\Helper;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

function get_video_details($path)
{
    $ffprobe = env('FFPROBE_PATH');
    $cmd = "$ffprobe -hide_banner -loglevel error -select_streams v:0 -show_streams -of json $path";
    /* echo 'get_video_details: ' . $cmd; */
    ob_start();
    passthru($cmd);
    $details = json_decode(trim(ob_get_contents()));
    ob_end_clean();

    $data = $details->streams[0];
    $numbers = explode('/', $data->avg_frame_rate);
    $data->avg_frame_rate = intval(round($numbers[0] / $numbers[1]));
    return $data;
}

function create_video($params)
{
    // crf only appliable fro h264
    $ffmpeg_crf = $params['codec_name'] === 'h264' ? "-crf {$params['crf']}" : "";

    if (!$params['skip_processing']) {
        // to fix height-not-divisible-by-2 we prepare width and height
        // another solution is to add 1px padding https://stackoverflow.com/a/53024964/2570425

        $ffmpeg = env('FFMPEG_PATH');
        // use -2 for width so ffmpeg will calculate width
        // use 0 for crf so ffmpeg will do lossless converting
        if ($params['crop']) {
            $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$params['input']} -c:v {$params['codec_name']} $ffmpeg_crf -vf 'scale=w={$params['width']}:h={$params['height']}:force_original_aspect_ratio=2,crop={$params['width']}:{$params['height']}' -c:a copy  {$params['output']}";
        } else {
            $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$params['input']} -c:v {$params['codec_name']} $ffmpeg_crf -vf scale='{$params['width']}:{$params['height']}' -c:a copy  {$params['output']}";
        }

        echo 'create_video: ' . $cmd;
        ob_start();
        passthru($cmd);
        ob_end_clean();
    }
}

function create_sd($video_id, $codec_name, $input, $output)
{
    create_video([
        'codec_name' => $codec_name,
        'type' => 'SD',
        'width' => -2,
        'height' => '480',
        'input' => $input,
        'output' => $output,
        'crf' => 15,
        'crop' => false,
        'skip_processing' => false,
    ]);

    $video = Videos::where('parent_id', $video_id)
        ->where('type', 'SD')
        ->first();

    $filesize_in_bytes = filesize($output);
    $video->size = $filesize_in_bytes;
    $video->save();

    Storage::disk('s3')->put($video->preview, file_get_contents($output));
    if ($output) {
        unlink($output);
    }
}

function create_hd($video_id, $codec_name, $input, $output)
{
    create_video([
        'codec_name' => $codec_name,
        'type' => 'HD',
        'width' => -2,
        'height' => '720',
        'input' => $input,
        'output' => $output,
        'crf' => 15,
        'crop' => false,
        'skip_processing' => false,
    ]);

    $video = Videos::where('parent_id', $video_id)
        ->where('type', 'HD')
        ->first();

    $filesize_in_bytes = filesize($output);
    $video->size = $filesize_in_bytes;
    $video->save();

    Storage::disk('s3')->put($video->preview, file_get_contents($output));
    if ($output) {
        unlink($output);
    }
}

function create_fhd($video_id, $codec_name, $input, $output)
{
    create_video([
        'codec_name' => $codec_name,
        'type' => 'FHD',
        'width' => -2,
        'height' => '1080',
        'input' => $input,
        'output' => $output,
        'crf' => 15,
        'crop' => false,
        'skip_processing' => false,
    ]);

    $video = Videos::where('parent_id', $video_id)
        ->where('type', 'FHD')
        ->first();

    $filesize_in_bytes = filesize($output);
    $video->size = $filesize_in_bytes;
    $video->save();

    Storage::disk('s3')->put($video->preview, file_get_contents($output));
    if ($output) {
        unlink($output);
    }
}

function create_4k($video_id, $codec_name, $input, $output)
{
    $video_is_4k = false;
    $details = get_video_details($input);
    if ($details->height === 2160) {
        $video_is_4k = true;
    }

    if ($video_is_4k) {
        // if use uploaded 4k then cropy it without conversion
        File::copy($input, $output);
    }

    create_video([
        'codec_name' => $codec_name,
        'type' => '4K',
        'width' => -2,
        'height' => '2160',
        'input' => $input,
        'output' => $output,
        'crf' => 15,
        'crop' => false,
        'skip_processing' => $video_is_4k,
    ]);

    $video = Videos::where('parent_id', $video_id)
        ->where('type', '4K')
        ->first();

    $filesize_in_bytes = filesize($output);
    $video->size = $filesize_in_bytes;
    $video->save();

    Storage::disk('s3')->put($video->preview, file_get_contents($output));
    if ($output) {
        unlink($output);
    }
}

$videos = Videos::whereNull('parent_id')
    ->where('extension', 'mov')
    ->get();

foreach ($videos as $video) {
    echo 'process_video: ' . $video->id . "\n";

    $parts = explode('/', $video->preview);
    $input = "./" . end($parts);
    file_put_contents($input, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $video->preview));

    $details = get_video_details($input);
    $codec_name = $details->codec_name;
    if (in_array($video->type, ['SD', 'HD', 'FHD', '4K'])) {
        $output = "./" . strtolower(time() . str_random(5) . '.' . $video->extension);
        create_sd($video->id, $codec_name, $input, $output);
    }
    if (in_array($video->type, ['HD', 'FHD', '4K'])) {
        $output = "./" . strtolower(time() . str_random(5) . '.' . $video->extension);
        create_hd($video->id, $codec_name, $input, $output);
    }
    if (in_array($video->type, ['FHD', '4K'])) {
        $output = "./" . strtolower(time() . str_random(5) . '.' . $video->extension);
        create_fhd($video->id, $codec_name, $input, $output);
    }
    if (in_array($video->type, ['4K'])) {
        $output = "./" . strtolower(time() . str_random(5) . '.' . $video->extension);
        create_4k($video->id, $codec_name, $input, $output);
    }

    if ($input) {
        unlink($input);
    }
    echo "Done\n";
}
