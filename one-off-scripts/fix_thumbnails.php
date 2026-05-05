<?php

use App\Models\Images;
use App\Helper;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$images = Images::whereNull('deleted_at')
    ->get();
foreach ($images as $image) {
    echo $image->id . "\n";

    $medium = "./" . $image->medium;
    file_put_contents($medium, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com/uploads/medium/' . $image->medium));

    $array = explode('.', $medium);
    $extension = end($array);

    $width = intval(Helper::getWidth($medium));
    $height = intval(Helper::getHeight($medium));

    $thumbnail = "./" . strtolower(time() . str_random(5) . '.' . $image->extension);
    $preview = "./" . strtolower(time() . str_random(5) . '.' . $image->extension);

    // PREVIEW
    $_width = $width > $height ? 640 : 0;
    $_height = $width > $height ? 0  : 640;
    Helper::resize_image_without_scale($medium, $_width, $_height, $preview);
    $watermarkSource = public_path('img/watermark.png');
    Helper::watermark($preview, $watermarkSource);

    // Thumbnail
    $_width = $width > $height ? 390 : 0;
    $_height = $width > $height ? 0  : 390;
    Helper::resize_image_without_scale($medium, $_width, $_height, $thumbnail);

    Storage::disk('s3')->put('uploads/thumbnail/' . $image->thumbnail, file_get_contents($thumbnail));
    Storage::disk('s3')->put('uploads/preview/' . $image->preview, file_get_contents($preview));

    $image->width_thumbnail = intval(Helper::getWidth($thumbnail));
    $image->height_thumbnail = intval(Helper::getHeight($thumbnail));
    $image->width_preview = intval(Helper::getWidth($preview));
    $image->height_preview = intval(Helper::getHeight($preview));
    $image->save();

    if ($preview) {
        unlink($preview);
    }
    if ($thumbnail) {
        unlink($thumbnail);
    }
    if ($medium) {
        unlink($medium);
    }
    echo "Done\n";
}
