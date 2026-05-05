<?php

/* $image_ids = collect(\DB::select("SELECT DISTINCT image_id FROM `image_tags` join images on images.id=image_tags.image_id where image_tags.id < 19658 and image_tags.id > 18959 and confidence > 0 "))-> */
$image_ids = collect(\DB::select("SELECT DISTINCT image_id FROM `image_tags` join images on images.id=image_tags.image_id where image_tags.id > 2359"))->
    map(function($item) {
        return $item->image_id;
    });

$data = [];
foreach ($image_ids->chunk(100) as $chunk) {
    $result = \DB::select("SELECT image_id, tag, local, confidence FROM `image_tags` where image_id in (".implode(",", $chunk->toArray()).") and confidence > 0 ");
    foreach ($result as $item) {
        $data[$item->image_id]["id"] = $item->image_id;
        $data[$item->image_id]["tag_" . $item->local] = $item->tag;
        $data[$item->image_id]["confidence"] = $item->confidence;

    }
}

$data = array_values($data);
$data = collect($data);
foreach ($data->chunk(500) as $chunk) {
    $raw_query = "";
    foreach ($data as $tag) {
        $raw_query .= "INSERT INTO `computer_vision_tags`(`image_id`, `tag_en`, `tag_ar`, `confidence`) VALUES ({$tag['id']},'{$tag['tag_en']}','{$tag['tag_ar']}',{$tag['confidence']});";
    }
    \DB::unprepared($raw_query);
}

/* var_dump($data); */



$images = collect(\DB::select("SELECT id, small FROM `images`"))->toArray();

foreach ($images as $image) {
    dispatch(new \App\Jobs\SetTagsByComputerVision(
        $image->id,
        'https://arabsstock.com/uploads/small/'. $image->small
    ))->onConnection('redis');
}

