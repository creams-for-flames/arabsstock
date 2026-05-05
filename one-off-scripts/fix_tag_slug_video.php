<?php

use App\Models\ImageTag;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

Config::set('database.default', 'mysqlVideo');
DB::table('video_tags')->orderBy('created_at')->chunk(100, function($tags)
{
    $raw_query = "";
    $counter=0;

    foreach ($tags->toArray() as $tagsValue) {
        $column_string = [];
        //   foreach ($params as $column => $value) {
        $value = get_soundex($tagsValue->tag);
        $column_string = "'$value'";
        // }
        $raw_query .= "UPDATE `video_tags` SET slug=" . $column_string . " WHERE `video_tags`.`id` = $tagsValue->id;";
        echo 'count:' . $counter++  . "\n". $tagsValue->id;
        echo '----execute Query-----' . "\n";
    }


    \DB::unprepared($raw_query);
});





