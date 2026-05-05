<?php

use App\Models\Images;
use App\Helper;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$images = Images::whereNull('deleted_at')
    ->whereRaw('height_thumbnail > width_thumbnail')
    ->get();

foreach ($images as $image) {
    echo 'process_image: ' . $image->id . "\n";

    $small = "./" . strtolower(time() . str_random(5) . '.' . $image->extension);
    $preview = "./" . strtolower(time() . str_random(5) . '.' . $image->extension);
    file_put_contents($small, file_get_contents('https://arabsstock.fra1.digitaloceanspaces.com/uploads/small/' . $image->small));
    \File::copy($small, $preview);

    $watermarkSource = public_path('img/watermark.png');
    Helper::watermark($preview, $watermarkSource);

    Storage::disk('s3')->put('uploads/preview/' . $image->preview, file_get_contents($preview));

    if ($small) {
        unlink($small);
    }
    if ($preview) {
        unlink($preview);
    }
    echo "Done\n";
}
