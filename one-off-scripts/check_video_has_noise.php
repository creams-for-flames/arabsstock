<?php

use App\Models\ImageTag;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
//\App\Models\Videos::where('parent_id','=',null)->count();


\App\Models\Videos::where('type', '=', 'SD')->orderBy('id')->chunk(100, function ($videos) {


    //  dd($videos->first());
    $ffprobe = env('FFPROBE_PATH');

    $raw_query = "";
    $counter = 0;

    foreach ($videos as $videoValue) {


        if($videoValue->parent_id != null)
        {
            $parts = explode('/', $videoValue->preview);
            $video_file = "./" . end($parts);
            file_put_contents($video_file, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com' . $videoValue->preview));



            // dd(url(''));
      //     $cmd = "$ffprobe   -v fatal  -of default=nw=1:nk=1 -show_streams  -select_streams a  -show_entries stream=codec_type $video_file";

           // ffprobe -i INPUT -show_streams -select_streams a -loglevel error

         //   $cmd = "$ffprobe   -v fatal  -of default=nw=1:nk=1 -show_streams  -select_streams a  -show_entries stream=codec_type $video_file";

            //  ffprobe -i INPUT -show_streams -select_streams a -loglevel error


            echo 'count:' . $counter++ . ' - ID: ' . $video_file . "\n";

        }

    }

    echo '----execute Query-----' . "\n";

    Config::set('database.default', 'mysqlVideo');

    \DB::unprepared($raw_query);
});
