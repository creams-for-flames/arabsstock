<?php

namespace App\Jobs;

use App\Helper;
use App\Models\Image;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class PublishAdminImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image_id;

    public function __construct($image_id)
    {
        $this->image_id = $image_id;
    }

    public function handle()
    {
        $image = Image::find($this->image_id);
        if (!$image) {
            \Log::channel('info')->info("No Image found ({$this->image_id})");
            return;
        }
        $watermarkSource = public_path('img/watermark.png');

        $extension = $image->extension;

        $medium = "uploads/images/{$image->id}/medium/" . strtolower(time() . Str::random(5) . '.' . $extension);
        $small = "uploads/images/{$image->id}/small/" . strtolower(time() . Str::random(5) . '.' . $extension);
        $preview = "uploads/images/{$image->id}/preview/" . strtolower(
                Str::slug($image->title_en, '-') .
                '-' .
                time() .
                Str::random(5) .
                '.' .
                $extension
            );
        $thumbnail = "uploads/images/{$image->id}/thumbnail/" . strtolower(
                Str::slug($image->title_en, '-') .
                '-' .
                time() .
                Str::random(5) .
                '.' .
                $extension
            );

        $thumbnail_local_path = public_path($thumbnail);
        $preview_local_path = public_path($preview);
        $small_local_path = public_path($small);
        $medium_local_path = public_path($medium);
        $large_local_path = public_path($image->large);


        if (!file_exists(dirname($preview_local_path)))
            mkdir(dirname($preview_local_path), 0777, true);

        if (!file_exists(dirname($thumbnail_local_path)))
            mkdir(dirname($thumbnail_local_path), 0777, true);

        if (!file_exists(dirname($small_local_path)))
            mkdir(dirname($small_local_path), 0777, true);

        if (!file_exists(dirname($medium_local_path)))
            mkdir(dirname($medium_local_path), 0777, true);

        if (!file_exists(dirname($large_local_path)))
            mkdir(dirname($large_local_path), 0777, true);

        set_time_limit(0);

        $original = $large_local_path;
        $file_content = Storage::disk('s3')->get($image->large);
        if (!file_exists(dirname($original)))
            mkdir(dirname($original), 0777, true);
        file_put_contents($original, $file_content);

        $width = getWidth($original);
        $height = getHeight($original);
        if ($width > $height) {
            if ($width > 1280):
                $_scale = 1280;
            else:
                $_scale = 900;
            endif;

            // Medium
            $scaleM = $_scale / $width;
            $uploaded = Helper::resizeImage(
                $original,
                $width,
                $height,
                $scaleM,
                $medium_local_path,
                300
            );

            // Small
            $scaleS = 640 / $width;
            $uploaded = Helper::resizeImage(
                $original,
                $width,
                $height,
                $scaleS,
                $small_local_path,
                300
            );

        } else {
            if ($width > 1280):
                $_scale = 960;
            else:
                $_scale = 800;
            endif;

            // Medium
            $scaleM = $_scale / $width;
            $uploaded = Helper::resizeImage(
                $original,
                $width,
                $height,
                $scaleM,
                $medium_local_path,
                300
            );

            // Small
            $scaleS = 480 / $width;
            $uploaded = Helper::resizeImage(
                $original,
                $width,
                $height,
                $scaleS,
                $small_local_path,
                300
            );
        }

        // PREVIEW
        $_width = $width > $height ? 640 : 0;
        $_height = $width > $height ? 0 : 640;
        Helper::resize_image_without_scale(
            $original,
            $_width,
            $_height,
            $preview_local_path
        );

        // Thumbnail
        $_width = $width > $height ? 390 : 0;
        $_height = $width > $height ? 0 : 390;
        Helper::resize_image_without_scale(
            $original,
            $_width,
            $_height,
            $thumbnail_local_path
        );

        Helper::watermark($preview_local_path, $watermarkSource);

        //Colors
        $palette = Palette::fromFilename(
            $preview_local_path
        );

        $extractor = new ColorExtractor($palette);

        // it defines an extract method which return the most “representative” colors
        $colors = $extractor->extract(5);

        // $palette is an iterator on colors sorted by pixel count
        foreach ($colors as $color) {
            $_color[] = trim(Color::fromIntToHex($color), '#');
        }

        $colors_image = implode(',', $_color);

        $image->colors = $colors_image;

        $image->thumbnail = $thumbnail;
        $image->preview = $preview;
        $image->small = $small;
        $image->medium = $medium;
        $image->save();


        $lResolution = list($w, $h) = getimagesize($large_local_path);
        $lSize = Helper::formatBytes(filesize($large_local_path), 1);

        $mResolution = list($_w, $_h) = getimagesize($medium_local_path);
        $mSize = Helper::formatBytes(filesize($medium_local_path), 1);

        $smallResolution = list($__w, $__h) = getimagesize(
            $small_local_path
        );
        $smallSize = Helper::formatBytes(filesize($small_local_path), 1);

        $stockImages = [
            [
                'name' => pathinfo($medium, PATHINFO_BASENAME),
                'type' => 'medium',
                'resolution' => $_w . 'x' . $_h,
                'size' => $mSize,
            ],
            [
                'name' => pathinfo($small, PATHINFO_BASENAME),
                'type' => 'small',
                'resolution' => $__w . 'x' . $__h,
                'size' => $smallSize,
            ],
        ];

        foreach ($stockImages as $key) {
            $stock = new Stock();
            $stock->image_id = $image->id;
            $stock->name = $key['name'];
            $stock->type = $key['type'];
            $stock->extension = $extension;
            $stock->resolution = $key['resolution'];
            $stock->size = $key['size'];
            $stock->token = $image->token_id;
            $stock->save();
        }


        $image->width_large = $this->getDimension($large_local_path, 'width');
        $image->height_large = $this->getDimension($large_local_path, 'height');
        $image->width_medium = $this->getDimension($medium_local_path, 'width');
        $image->height_medium = $this->getDimension($medium_local_path, 'height');
        $image->width_small = $this->getDimension($small_local_path, 'width');
        $image->height_small = $this->getDimension($small_local_path, 'height');
        $image->width_preview = $this->getDimension($preview_local_path, 'width');
        $image->height_preview = $this->getDimension($preview_local_path, 'height');
        $image->width_thumbnail = $this->getDimension($thumbnail_local_path, 'width');
        $image->height_thumbnail = $this->getDimension($thumbnail_local_path, 'height');
        $image->save();
        \Log::channel('info')->info('count image' . $image->stock->count());
        foreach ($image->stock as $imgItem) {
            dispatch(new StoreImgInS3($imgItem->id))->onQueue('media_default');
        }
        // generate tags (must start after StoreImgInS3)
        dispatch(
            new SetTagsByComputerVision([
                'image_id' => $image->id,
            ])
        )->onQueue('media_default');

        \Session::flash('success_message', trans('admin.success_add'));

    }

    public function getDimension($path, $type)
    {
        if ($type == 'width') {
            $width = getWidth($path);
            return $width;
        } else {
            $height = getHeight($path);
            return $height;
        }
    }
}
