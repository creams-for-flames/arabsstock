<?php

use App\Models\ImageTag;
use Illuminate\Support\Facades\DB;


require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());


DB::table('image_tags')->orderBy('created_at')->chunk(100, function($tags)
{
    $raw_query = "";
    $counter=0;

    foreach ($tags->toArray() as $tagsValue) {
        $column_string = [];
        //   foreach ($params as $column => $value) {
        $value = get_soundex($tagsValue->slug);
        $column_string = "'$value'";
        // }
        $raw_query .= "UPDATE `image_tags` SET slug=" . $column_string . " WHERE `image_tags`.`id` = $tagsValue->id;";
        echo 'count:' . $counter++  . "\n". $tagsValue->id;
        echo '----execute Query-----' . "\n";
    }


    \DB::unprepared($raw_query);
});





