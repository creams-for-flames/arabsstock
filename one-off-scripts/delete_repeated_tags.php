<?php

$data = \DB::select("
    select image_id, tag from ( SELECT image_id, tag, count(*) as c FROM `image_tags` GROUP by image_id, tag HAVING c > 1  ) as t1
");

foreach($data as $item) {
 $first_id = \DB::table('image_tags')->where('image_id', $item->image_id)->where('tag', $item->tag)->first()->id;
 \DB::table('image_tags')->where('id', $first_id)->delete();
}

