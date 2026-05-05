<?php

namespace App\Jobs;

use App\Helper;
use App\Models\Vector;
use App\Models\VectorFolder;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\CategoryVector;
use App\Models\ContributorVector;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishVector implements ShouldQueue

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CONTRIBUTOR_STAGE_PUBLISH = 8;

    private $contributor_vector_id;
    private $original_name;
    private $extension;
    private $hash;
    private $thumbnail;
    private $vector;
    private $title_en;
    private $title_ar;
    private $user_id;
    private $user_type;
    private $tags_en;
    private $tags_ar;
    private $category_ids;
    private $thumbnail_name_video;
    private $status;
    private $folder;
    private $contributor;
    private $reviewer_id;
    private $reviewed_at;
    private $publisher_id;
    private $published_at;

    public function __construct($params)
    {
        $this->onQueue('vector');
        $this->contributor_vector_id = $params['contributor_vector_id'];
        $this->original_name = $params['original_name'];
        $this->extension = $params['extension'];
        $this->hash = $params['hash'];
        $this->vector = $params['vector'];
        $this->thumbnail = $params['thumbnail'];
        $this->title_en = $params['title_en'];
        $this->title_ar = $params['title_ar'];
        $this->user_id = $params['user_id'];
        $this->user_type = $params['user_type'];
        $this->tags_en = $params['tags_en'] ?? [];
        $this->tags_ar = $params['tags_ar'] ?? [];
        $this->category_ids = $params['category_ids'];
        $this->status = $params['status'] ?? 'active';
        $this->folder = $params['folder'] ?? NULL;
        $this->contributor = $params['contributor'] ?? NULL;
        $this->reviewer_id = $params['reviewer_id'];
        $this->reviewed_at = $params['reviewed_at'];
        $this->publisher_id = $params['publisher_id'];
        $this->published_at = $params['published_at'];

    }

    public function handle()
    {
        $data_file = Vector::where('contributor_vector_id', $this->contributor_vector_id)->first();
        if ($data_file) {
            \Log::channel('info')->info('Vector exist contributor_vector_id: ' . $data_file->contributor_vector_id . ' image_id: ' . $data_file->id);
            exit(1);
        }

//        try {
        \Log::channel('info')->info('Vector Start Publish contributor vector id :  ' . $this->contributor_vector_id ?? 0);
        $mimeType = "jpg";
        $orginal_path = $this->vector;
        $thumbnail_path =$this->thumbnail;

        $watermarkSource = public_path('img/watermark.png');

        $extension = $this->extension;

        $description_ar = '';
        $description_en = '';


        $token_id = Str::random(200);
        $thumbnail_name = Str::random(10) . time() . '.jpg';
        $preview_name = Str::random(15) . time() . '.jpg';
        $large_name = Str::random(20) . time() . '.jpg';

        $vector = new Vector();
        $vector->contributor_vector_id = $this->contributor_vector_id;
        $vector->title_ar = $this->title_ar ?? 'testvector';
        $vector->title_en = $this->title_en ?? 'testvectoren';
        $vector->description_ar = trim($description_ar);
        $vector->description_en = trim($description_en);
        $vector->user_id = $this->user_id;
        $vector->user_type = $this->user_type;
        $vector->status = $this->status;
        $vector->token_id = $token_id;
        $vector->extension = strtolower($extension);
        // $vector->colors = $colors_image;
        $vector->how_use_vector = 'free';
        $vector->attribution_required = 'no';
        $vector->original_name = $this->original_name;
        $vector->hash = $this->hash;
        $vector->reviewer_id = $this->reviewer_id;
        $vector->reviewed_at = $this->reviewed_at;
        $vector->publisher_id = $this->publisher_id;
        $vector->published_at = $this->published_at;
        $vector->save();
        $vector->vector = "uploads/vectors/{$vector->id}/{$this->vector}";
        $vector->preview = "uploads/vectors/{$vector->id}/preview/" . $preview_name;
        $vector->thumbnail = "uploads/vectors/{$vector->id}/" . $thumbnail_name;
        $vector->large = "uploads/vectors/{$vector->id}/large/" . $large_name;
        $keywords = array('vectors', 'victors', 'vector', 'victor', 'illustration', 'illustrator');
        $slug = updateSlug($keywords, slugify_v2($vector->title_en));
        $vector->slug = 'illustration-' . $vector->id . '-' . $slug;
        $vector->save();


        $original_local_path = public_path($vector->vector);
        $thumbnail_local_path = public_path($vector->thumbnail);
        $preview_local_path = public_path($vector->getAttributes()['preview']);
        $large_local_path = public_path($vector->large);

        if (!file_exists(dirname($preview_local_path)))
            mkdir(dirname($preview_local_path), 0777, true);

        if (!file_exists(dirname($thumbnail_local_path)))
            mkdir(dirname($thumbnail_local_path), 0777, true);

        if (!file_exists(dirname($large_local_path)))
            mkdir(dirname($large_local_path), 0777, true);

        if (!file_exists(dirname($original_local_path)))
            mkdir(dirname($original_local_path), 0777, true);

        \Log::channel('info')->info('Vector create vector' . $vector->id ?? NULL);

        Storage::disk('s3')->copy($thumbnail_path, $vector->getAttributes()['thumbnail']);
        Storage::disk('s3')->copy($orginal_path, $vector->getAttributes()['vector']);
        file_put_contents($thumbnail_local_path, Storage::disk('s3')->get($thumbnail_path));
        file_put_contents($original_local_path, Storage::disk('s3')->get($orginal_path));


        $exif_data = @exif_read_data($original_local_path, 0, true);

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


        if (count($this->tags_en)) {
            \sync_tags($vector, $this->tags_en, 'en');
        }
        if (count($this->tags_ar)) {
            \sync_tags($vector, $this->tags_ar, 'ar');
        }

        if (isset($this->category_ids) && count($this->category_ids)) {
            \Log::channel('info')->info('Vector Start create category_ids count ' . count($this->category_ids));
            $cateogries = $this->category_ids;

            for ($i = 0; $i < count($cateogries); $i++) {
                CategoryVector::create([
                    'vector_id' => $vector->id,
                    'category_id' => $cateogries[$i],
                ]);
            }
        }


        \Log::channel('info')->info('Vector Start storge ');

        // $image = new \Imagick(realpath($original_local_path));
        // // $image->setRegistry('temporary-path', '/mnt/mediaprocessing_tmp');
        // $image->setResolution(300, 300);
        // $image->setImageColorspace(\Imagick::COLORSPACE_RGB);
        // $image->setCompression(\Imagick::COMPRESSION_JPEG);
        // $image->setCompressionQuality(100);
        // $image->readimage(realpath($original_local_path));
        // $image->setImageFormat('jpeg');
        // $dimension = $image->getImageGeometry();
        // $vector_width = $dimension['width'] ?? 0;
        // $vector_height = $dimension['height'] ?? 0;
        // $image->writeImage($large_local_path);
        // $image->clear();
        // $image->destroy();

        /*  */
        \Log::channel('info')->info("Start proccess vector ({$this->contributor_vector_id})");

        $path_script = __DIR__ . '/python/convertepstojpgv1.py';
        $cmd = "python3 {$path_script}  {$original_local_path} {$large_local_path}";
        \Log::info("cmd   " . $cmd);
        ob_start();
        passthru($cmd);
        $details = ob_get_contents();// \json_decode(trim(ob_get_contents()));
        ob_end_clean();
        $vowels = array("(", ")", "\n");
        $details = str_replace($vowels, "", $details);
        $details = explode(",", $details);

        /* dimensions vector */
        if (isset($details)) {
            $vector->width_vector = $details[0];
            $vector->height_vector = $details[1];
        }
        /* dimensions vector */
        \Log::channel('info')->info("End proccess vector ({$this->contributor_vector_id})");
        /* ImageHash start */
        if (file_exists($large_local_path)) {
            $hash_image = hash_file('sha256', $large_local_path);
            if ($hash_image)
                $vector->hash_image = $hash_image;
        }
        /* ImageHash end */
        /*  */

        $w_thumb_vector_image = Helper::getWidth($large_local_path);
        $h_thumb_vector_image = Helper::getHeight($large_local_path);
        // PREVIEW
        $_width_preview = $w_thumb_vector_image > $h_thumb_vector_image ? 640 : 0;
        $_height_preview = $w_thumb_vector_image > $h_thumb_vector_image ? 0 : 640;
        Helper::resize_image_without_scale(
            $large_local_path,
            $_width_preview,
            $_height_preview,
            $preview_local_path
        );
        Helper::watermark($preview_local_path, $watermarkSource);
        $this->upload_to_s3($vector->getAttributes()['preview'], file_get_contents($preview_local_path));
        $this->upload_to_s3($vector->large, file_get_contents($large_local_path));
        /* end vector preview */


        $vector->is_uploaded = 1;
        $vector->width_thumbnail = $this->getDimension($thumbnail_local_path, 'width');
        $vector->height_thumbnail = $this->getDimension($thumbnail_local_path, 'height');
        $vector->width_preview = $this->getDimension($preview_local_path, 'width');
        $vector->height_preview = $this->getDimension($preview_local_path, 'height');
        $vector->height_large = $h_thumb_vector_image;
        $vector->width_large = $w_thumb_vector_image;
        $vector->exif = trim($exif);
        $vector->camera = $camera;


        if ($this->folder) {
            \Log::channel('info')->info('Vector Start create foleder ');
            $folder_id = VectorFolder::firstOrCreate(['folder' => $this->folder])->id;
            $vector->folder_id = $folder_id;
        }
        $vector->save();
        // Add Search image
        if ($vector->preview != null) {
            dispatch(new \App\Jobs\AddSearchVectorJob(\App\Models\Base\Vector::find($vector->id)));
        }
        Storage::disk('public')->deleteDirectory("uploads/vectors/{$vector->id}");
        \Log::channel('info')->info('Vector delete directory ' . $vector->id);
        dispatch(
            new SetTagsByComputerVision([
                'vector_id' => $vector->id,
            ])
        );

        if ($this->contributor) {
            ContributorVector::
            where('id', $this->contributor_vector_id)
                ->update(['contributor_stage' => self::CONTRIBUTOR_STAGE_PUBLISH]);

            $details = [
                'contributor' => $this->contributor,
                'type' => 'vectors',
            ];
            SendEmailAlertContributorFilePublish::dispatch($details);
            \Log::channel('info')->info('Vector Alert Email End Publish  contributor vector id : ' . $this->contributor_vector_id ?? 0);

        }
        // $vector->contributor_vector()->update(['contributor_stage'=>$contributor_stage]);
        \Log::channel('info')->info('Vector End Publish  contributor vector id : ' . $this->contributor_vector_id ?? 0);

//        } catch (\Throwable $th) {
//            $contributor_stage = 2;
//            $contributor_vector_id = $this->contributor_vector_id;
//            if (isset($contributor_vector_id)) {
//                ContributorVector::where('id', $contributor_vector_id)->update([
//                    'contributor_stage' => $contributor_stage,
//                ]);
//            }
//            $error = $th->getMessage() . ' line : ' . $th->getLine();
//            \Log::error($error);
//            \Log::channel('info')->info('Vector End Publish Error ' . $this->contributor_vector_id ?? 0 . 'return contributor_stage to : ' . $contributor_stage);
//
//        }


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

    public function upload_to_s3($path, $path_on_disk)
    {
        \Log::channel('info')->info('upload_to_s3 : ' . $path);
        $status = Storage::disk('s3')->put($path, $path_on_disk);
        if ($status === false) {
            \Log::error('could not upload file ' . $path_on_disk);
        }
    }
}
