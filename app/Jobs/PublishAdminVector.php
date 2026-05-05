<?php

namespace App\Jobs;

use App\Helper;
use App\Models\Vector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublishAdminVector implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $vector_id;

    public function __construct($vector_id)
    {
        $this->vector_id = $vector_id;
    }

    public function handle()
    {

        $vector = Vector::find($this->vector_id);
        if (!$vector) {
            \Log::channel('info')->info("No Image found ({$this->vector_id})");
            return;
        }

        $original = $vector->vector;
        $preview = "uploads/vectors/{$vector->id}/preview/" . strtolower(\Illuminate\Support\Str::slug($vector->title_en, '-') . '-' . time() . Str::random(5) . '.jpg');
        $thumbnail = "uploads/vectors/{$vector->id}/" . strtolower(\Illuminate\Support\Str::slug($vector->title_en, '-') . '-' . time() . Str::random(5) . '.jpg');
        $large = "uploads/vectors/{$vector->id}/large/" . strtolower(\Illuminate\Support\Str::slug($vector->title_en, '-') . '-' . time() . Str::random(5) . '.jpg');

        $original_local_path = public_path($original);
        $thumbnail_local_path = public_path($thumbnail);
        $preview_local_path = public_path($preview);
        $large_local_path = public_path($large);


        if (!file_exists(dirname($preview_local_path)))
            mkdir(dirname($preview_local_path), 0777, true);

        if (!file_exists(dirname($thumbnail_local_path)))
            mkdir(dirname($thumbnail_local_path), 0777, true);

        if (!file_exists(dirname($large_local_path)))
            mkdir(dirname($large_local_path), 0777, true);

        if (!file_exists(dirname($original_local_path)))
            mkdir(dirname($original_local_path), 0777, true);


        $file_content = Storage::disk('s3')->get($vector->vector);
        if (!file_exists(dirname($original_local_path)))
            mkdir(dirname($original_local_path), 0777, true);
        file_put_contents($original_local_path, $file_content);

        $imageType = 'jpg';

        $image = new \Imagick(realpath($original_local_path));
        $image->setResolution(300, 300);
        $image->setImageColorspace(\Imagick::COLORSPACE_RGB);
        $image->setCompression(\Imagick::COMPRESSION_JPEG);
        $image->setCompressionQuality(100);
        $image->setImageFormat('jpeg');
        $image->readimage(realpath($original_local_path));
        $dimension = $image->getImageGeometry();
        $w_thumb_vector = $dimension['width'];
        $h_thumb_vector = $dimension['height'];
        $image->scaleImage($w_thumb_vector, $h_thumb_vector);
        $image->writeImage($large_local_path);
        $image->clear();
        $image->destroy();

        $vector->thumbnail = $thumbnail;
        $vector->preview = $preview;
        $vector->large = $large;
        $vector->save();
        /* e:update fix */

        // PREVIEW
        $_width_preview = $w_thumb_vector > $h_thumb_vector ? 640 : 0;
        $_height_preview = $w_thumb_vector > $h_thumb_vector ? 0 : 640;
        $uploaded = Helper::resize_image_without_scale(
            $large_local_path,
            $_width_preview,
            $_height_preview,
            $preview_local_path
        );

        // Thumbnail
        $_height_thumbnail = 280;
        $_width_thumbnail = 260;
        if ($w_thumb_vector > $h_thumb_vector) {
            (int)$_width_thumbnail = $w_thumb_vector * ($_height_thumbnail / $h_thumb_vector);
        }
        Helper::resize_image_without_scale(
            $large_local_path,
            $_width_thumbnail,
            $_height_thumbnail,
            $thumbnail_local_path
        );
        $watermarkSource = public_path('img/watermark.png');

        Helper::watermark($preview_local_path, $watermarkSource);

        $vector->width_preview = $this->getDimension($preview_local_path, 'width');
        $vector->height_preview = $this->getDimension($preview_local_path, 'height');
        $vector->width_thumbnail = $this->getDimension($thumbnail_local_path, 'width');
        $vector->height_thumbnail = $this->getDimension($thumbnail_local_path, 'height');
        $vector->height_large = $h_thumb_vector;
        $vector->width_large = $w_thumb_vector;
        $vector->save();
        dispatch(new UploadVectorS3($vector->id))->onQueue('media_default');
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
