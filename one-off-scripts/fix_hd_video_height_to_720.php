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
    $cmd = "$ffprobe -hide_banner -loglevel warning -select_streams v:0 -show_streams -of json $path";
    echo 'get_video_details: ' . $cmd . "\n";
    ob_start();
    passthru($cmd);
    $details = json_decode(trim(ob_get_contents()));
    ob_end_clean();

    $data = $details->streams[0];
    $numbers = explode('/', $data->avg_frame_rate);
    $data->avg_frame_rate = intval(round($numbers[0] / $numbers[1]));
    return $data;
}

$videos = Videos::whereNull('parent_id')
    ->whereIn('id', [
        1,2,3,5,6,8,9,10,11,12,13,14,15,16,17,19,20,86,89,91,92,93,94,96,97,98,100,101,102,104,106,107,108,109,110,111,112,113,115,116,117,123,124,126,127,128,129,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,165,167,168,170,171,176,177,178,179,180,181,183,184,185,186,190,191,192,193,194,195,196,198,199,200,201,202,203,204,205,206,208,209,210,217,219,221,222,223,224,225,227,228,229,230,231,232,234,235,236,237,238,239,240
    ])
    ->get();

foreach ($videos as $video) {
    echo 'process_video: ' . $video->id . "\n";
    $hd_video = Videos::where('parent_id', $video->id)
        ->where('type', 'HD')
        ->first();

    $parts = explode('/', $video->preview);
    $original_path = "./" . end($parts);
    $video_name_output_hd = "./" . strtolower(time() . str_random(5) . '.' . $hd_video->extension);
    file_put_contents($original_path, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $video->preview));
    $params = [
        'crf' => 15,
        'width' => -2,
        'height' => '720',
        'output' => $video_name_output_hd,
    ];

    $ffmpeg = env('FFMPEG_PATH');
    $cmd = "$ffmpeg -hide_banner -loglevel warning -y -i {$original_path} -c:v libx264 -preset medium -crf {$params['crf']} -vf scale='{$params['width']}:{$params['height']}' -c:a copy  {$params['output']}" . "\n";
    echo 'create_video: ' . $cmd . "\n";
    ob_start();
    passthru($cmd);
    ob_end_clean();

    $filesize_in_bytes = filesize($params['output']);

    $details = get_video_details($params['output']);

    $hd_video->width = $details->width;
    $hd_video->height = $details->height;
    $hd_video->aspect_ratio = $video->aspect_ratio;
    $hd_video->fps = $details->avg_frame_rate;
    $hd_video->size = $filesize_in_bytes;
    $hd_video->save();

    Storage::disk('s3')->put($hd_video->preview, file_get_contents($video_name_output_hd));

    if ($original_path) {
        unlink($original_path);
    }
    if ($video_name_output_hd) {
        unlink($video_name_output_hd);
    }
    echo "Done\n";
}
