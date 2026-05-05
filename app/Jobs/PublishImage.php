<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper;
use App\Models\CategoryImage;
use App\Models\Stock;
use App\Models\Image;
use Illuminate\Support\Str;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use League\ColorExtractor\Color;
use Illuminate\Support\Facades\Storage;

class PublishImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $contributor_image_id;
    private $contributor_submission_id;
    private $original_name;
    private $extension;
    private $hash;
    private $large;
    private $title_en;
    private $title_ar;
    private $user_id;
    private $user_type;
    private $tags_en;
    private $tags_ar;
    private $category_ids;
    private $thumbnail_name_video;
    private $contributor;
    private $reviewer_id;
    private $reviewed_at;
    private $publisher_id;
    private $published_at;
    private $how_use_image;

    public function __construct($params)
    {
        $this->onQueue('image');
        $this->contributor_image_id = $params['contributor_image_id'];
        $this->contributor_submission_id = $params['contributor_submission_id'];
        $this->original_name = $params['original_name'];
        $this->extension = $params['extension'];
        $this->hash = $params['hash'];
        $this->large = $params['large'];
        $this->title_en = $params['title_en'];
        $this->title_ar = $params['title_ar'];
        $this->user_id = $params['user_id'];
        $this->user_type = $params['user_type'];
        $this->tags_en = $params['tags_en'];
        $this->tags_ar = $params['tags_ar'];
        $this->category_ids = $params['category_ids'];
        $this->contributor = $params['contributor'];
        $this->contributor = $params['contributor'];
        $this->reviewer_id = $params['reviewer_id'];
        $this->reviewed_at = $params['reviewed_at'];
        $this->publisher_id = $params['publisher_id'];
        $this->published_at = $params['published_at'];
        $this->how_use_image = $params['how_use_image'];

    }

    public function handle()
    {
        $data_file = Image::where('contributor_image_id', $this->contributor_image_id)->first();
        if ($data_file) {
            \Log::channel('info')->info('Image exist contributor_image_id: ' . $data_file->contributor_image_id . ' image_id: ' . $data_file->id);
            return;
        }

        \Log::channel('info')->info('Image Start1');

        $orginal_path = $this->large;
        $temp_large = \basename($this->large);
        $temp = public_path('temp/');
        if (!file_exists($temp))
            mkdir($temp, 0777, true);
        file_put_contents(public_path('temp/' . $temp_large), Storage::disk('s3')->get($orginal_path));
        // PATHS

        $watermarkSource = public_path('img/watermark.png');

        $extension = $this->extension;
        $large = strtolower(time() . Str::random(5) . '.' . $extension);
        $medium = strtolower(time() . Str::random(5) . '.' . $extension);
        $small = strtolower(time() . Str::random(5) . '.' . $extension);
        $preview = strtolower(time() . Str::random(5) . '.' . $extension);
        $thumbnail = strtolower(time() . Str::random(5) . '.' . $extension);

        if (rename($temp . $temp_large, $temp . $large)) {
            $original = $temp . $large;
            $width = Helper::getWidth($original);
            $height = Helper::getHeight($original);
            $dpi = 300;
            $quality = 100;
            if ($width > $height) {
                if ($width > 1280):
                    $_scale = 1280;
                else:
                    $_scale = 900;
                endif;

                // Medium
                $scaleM = $_scale / $width;
                $uploaded = resizeImageWithImagick(
                    $original,
                    $temp . $medium,
                    $width,
                    $height,
                    $dpi,
                    $scaleM,
                    $quality
                );

                // Small
                $scaleS = 640 / $width;
                $uploaded = resizeImageWithImagick(
                    $original,
                    $temp . $small,
                    $width,
                    $height,
                    $dpi,
                    $scaleS,
                    $quality
                );

            } else {
                if ($width > 1280):
                    $_scale = 960;
                else:
                    $_scale = 800;
                endif;

                // Medium
                $scaleM = $_scale / $width;
                $uploaded = resizeImageWithImagick(
                    $original,
                    $temp . $medium,
                    $width,
                    $height,
                    $dpi,
                    $scaleM,
                    $quality
                );

                // Small
                $scaleS = 480 / $width;
                $uploaded = resizeImageWithImagick(
                    $original,
                    $temp . $small,
                    $width,
                    $height,
                    $dpi,
                    $scaleS,
                    $quality
                );
            }

            // PREVIEW
            $_width = 640;
            $_height = 640;
            resizeImageWithImagick($original, $temp . $preview, $_width, $_height);

            // Thumbnail
            $_width = 390;
            $_height = 390;
            resizeImageWithImagick($original, $temp . $thumbnail, $_width, $_height);


            Helper::watermark($temp . $preview, $watermarkSource);
        } else {
            \Log::error('Image moving error for ' . $this->contributor_image_id);
        }

        $description_ar = '';
        $description_en = '';

        $exif_data = @exif_read_data($temp . $large, 0, true);

        if (isset($exif_data['EXIF']['ISOSpeedRatings'][0])) {
            $ISO = 'ISO ' . $exif_data['EXIF']['ISOSpeedRatings'][0];
        }

        if (isset($exif_data['EXIF']['ExposureTime'])) {
            $ExposureTime = $exif_data['EXIF']['ExposureTime'] . 's';
        }

        if (isset($exif_data['EXIF']['FocalLength'])) {
            $FocalLength = round($exif_data['EXIF']['FocalLength'], 1) . 'mm';
        }

        if (isset($exif_data['COMPUTED']['ApertureFNumber'])) {
            $ApertureFNumber = $exif_data['COMPUTED']['ApertureFNumber'];
        }

        if (!isset($FocalLength)) {
            $FocalLength = '';
        }

        if (!isset($ExposureTime)) {
            $ExposureTime = '';
        }

        if (!isset($ISO)) {
            $ISO = '';
        }

        if (!isset($ApertureFNumber)) {
            $ApertureFNumber = '';
        }

        $exif = $FocalLength . ' ' . $ApertureFNumber . ' ' . $ExposureTime . ' ' . $ISO;

        if (isset($exif_data['IFD0']['Model'])) {
            $camera = $exif_data['IFD0']['Model'];
        } else {
            $camera = '';
        }

        //Colors
        $palette = Palette::fromFilename(public_path('temp/' . $preview));

        $extractor = new ColorExtractor($palette);

        // it defines an extract method which return the most “representative” colors
        $colors = $extractor->extract(5);

        // $palette is an iterator on colors sorted by pixel count
        foreach ($colors as $color) {
            $_color[] = trim(Color::fromIntToHex($color), '#');
        }

        $colors_image = implode(',', $_color);

        $token_id = Str::random(200);

        $image = new Image();
        $image->contributor_submission_id = $this->contributor_submission_id;
        $image->contributor_image_id = $this->contributor_image_id;

        $image->title_ar = $this->title_ar;
        $image->title_en = $this->title_en;
        $image->description_ar = trim($description_ar);
        $image->description_en = trim($description_en);
        $image->user_id = $this->user_id;
        $image->user_type = $this->user_type;
        $image->status = 'active';
        $image->token_id = $token_id;
        $image->extension = strtolower($extension);
        $image->colors = $colors_image;
        $image->exif = trim($exif);
        $image->camera = $camera;
        $image->how_use_image = $this->how_use_image ?? 'free';
        $image->attribution_required = 'no';
        $image->original_name = $this->original_name;
        $image->hash = $this->hash;
        $image->reviewer_id = $this->reviewer_id;
        $image->reviewed_at = $this->reviewed_at;
        $image->publisher_id = $this->publisher_id;
        $image->published_at = $this->published_at;

        $image->save();
        $image->slug = 'image-' . $image->id . '-' . slugify_v2($image->title_en);
        $image->thumbnail = "uploads/images/{$image->id}/" . strtolower(Str::limit($image->slug, 70, '')) . "-thumbnail." . pathinfo($thumbnail, PATHINFO_EXTENSION);
        $image->preview = "uploads/images/{$image->id}/" . strtolower(Str::limit($image->slug, 70, '')) . "-preview." . pathinfo($preview, PATHINFO_EXTENSION);
        $image->small = "uploads/images/{$image->id}/small/{$small}";
        $image->medium = "uploads/images/{$image->id}/medium/{$medium}";
        $image->large = "uploads/images/{$image->id}/large/{$large}";

        $thumbnail_local_path = public_path($image->thumbnail);
        $preview_local_path = public_path($image->preview);
        $small_local_path = public_path($image->small);
        $medium_local_path = public_path($image->medium);
        $large_local_path = public_path($image->large);
        $image->save();
        if (count($this->tags_en)) {
            \sync_tags($image, $this->tags_en, 'en');
        }
        if (count($this->tags_ar)) {
            \sync_tags($image, $this->tags_ar, 'ar');
        }
        if (count($this->category_ids)) {
            $cateogries = $this->category_ids;

            for ($i = 0; $i < count($cateogries); $i++) {
                CategoryImage::create([
                    'image_id' => $image->id,
                    'category_id' => $cateogries[$i],
                ]);
            }
        }
        $lResolution = list($w, $h) = getimagesize($temp . $large);
        $lSize = Helper::formatBytes(filesize($temp . $large), 1);
        $mResolution = list($_w, $_h) = getimagesize($temp . $medium);
        $mSize = Helper::formatBytes(filesize($temp . $medium), 1);
        $smallResolution = list($__w, $__h) = getimagesize($temp . $small);
        $smallSize = Helper::formatBytes(filesize($temp . $small), 1);
        $stockImages = [
            [
                'name' => $large,
                'type' => 'large',
                'resolution' => $w . 'x' . $h,
                'size' => $lSize,
            ],
            [
                'name' => $medium,
                'type' => 'medium',
                'resolution' => $_w . 'x' . $_h,
                'size' => $mSize,
            ],
            [
                'name' => $small,
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
            $stock->token = $token_id;
            $stock->save();
        }

        if (!file_exists(dirname($preview_local_path))) {
            mkdir(dirname($preview_local_path), 0777, true);
        }

        \File::copy($temp . $preview, $preview_local_path);
        \File::delete($temp . $preview);

        if (!file_exists(dirname($thumbnail_local_path))) {
            mkdir(dirname($thumbnail_local_path), 0777, true);
        }
        \File::copy($temp . $thumbnail, $thumbnail_local_path);
        \File::delete($temp . $thumbnail);

        if (!file_exists(dirname($small_local_path))) {
            mkdir(dirname($small_local_path), 0777, true);
        }
        \File::copy($temp . $small, $small_local_path);
        \File::delete($temp . $small);

        if (!file_exists(dirname($medium_local_path))) {
            mkdir(dirname($medium_local_path), 0777, true);
        }
        \File::copy($temp . $medium, $medium_local_path);
        \File::delete($temp . $medium);

        if (!file_exists(dirname($large_local_path))) {
            mkdir(dirname($large_local_path), 0777, true);
        }
        \File::copy($temp . $large, $large_local_path);
        \File::delete($temp . $large);

        $img = Image::with('stock')->findOrFail($image->id);
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
        foreach ($img->stock as $imgItem) {
            dispatch(new StoreImgInS3($imgItem->id));
        }

        // generate tags (must start after StoreImgInS3)
        dispatch(
            new SetTagsByComputerVision([
                'image_id' => $image->id,
            ])
        );
        // Setting the search image
        // /* last action send mail to  the contributor */
        // $details = [
        //     'contributor' => $this->contributor,
        //     'type' => 'images',
        // ];
        // SendEmailAlertContributorFilePublish::dispatch($details);
    }

    public function getDimension($path, $type)
    {
        if ($type == 'width') {
            $width = Helper::getWidth($path);
            return $width;
        } else {
            $height = Helper::getHeight($path);
            return $height;
        }
    }

}
