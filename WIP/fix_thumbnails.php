<?php

use App\Models\Images;
use App\Helper;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$images = Images::get();
foreach($images as $image) {
    echo $image->id . "\n";

    $medium = "./" . $image->medium;
    file_put_contents($medium, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com/uploads/medium/' . $image->medium));

    $array = explode('.', $medium);
    $extension = end($array);

    $width = intval(Helper::getWidth($medium));
    $height = intval(Helper::getHeight($medium));

    $thumbnail = strtolower(
        time() .
        str_random(5) .
        '.' .
        $extension
    );

    // Thumbnail
    $scaleT = 400 / $width;
    $uploaded = Helper::resizeImage(
        $medium,
        $width,
        $height,
        $scaleT,
        $thumbnail
    );

    $scaleT = 480/400;
    $uploaded = Helper::resizeImage(
        $thumbnail,
        intval(Helper::getWidth($thumbnail)),
        intval(Helper::getHeight($thumbnail)),
        $scaleT,
        $thumbnail
    );

    Storage::disk('s3')->put('uploads/thumbnail/' . $image->thumbnail, file_get_contents($thumbnail));

    $image->width_thumbnail = intval(Helper::getWidth($thumbnail));
    $image->height_thumbnail = intval(Helper::getHeight($thumbnail));
    $image->save();

    if ($thumbnail) {
        unlink($thumbnail);
    }
    if ($medium) {
        unlink($medium);
    }
    echo "Done\n";
}
