<?php

require './vendor/autoload.php';
require 'Fastimage.php';

$images_chunks = collect([
[3103, '-158703906920pbb.jpg'],
])->chunk(100);

foreach ($images_chunks as $images_chunk) {

    $raw_query = "";
    foreach ($images_chunk as $image) {
        // loading image into constructor
        $result = new FastImage('https://arabsstock.fra1.digitaloceanspaces.com/uploads/preview/'. $image[1]);
        list($width, $height) = $result->getSize();
        echo $image[0] .": ". $image[1] . " ". $width . "x" . $height . "\n";
        $raw_query .= "UPDATE `images` SET `preview_width` = '$width', `preview_height` = '$height' WHERE `images`.`id` = {$image[0]};";
    }

    sleep(3);
    \DB::unprepared($raw_query);
}
