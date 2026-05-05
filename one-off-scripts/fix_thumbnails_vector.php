<?php

use App\Helper;
use App\Models\Vectors;
use Illuminate\Support\Facades\Storage;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

// $vector = Vectors::where('is_uploaded',1)->find(872);
\Log::info('****** start process_upload_vector S3 ****** ' . now());

Vectors::where('is_uploaded',1)->chunk(200, function ($vectors)  {
    foreach ($vectors as $key => $vector) {
    //  try {
        \Log::info('start process_upload_vector S3 vector id :  '.$vector->id);
        $dir=public_path(DS . 'uploads' . DS .'temp'.DS. 'vectors' . DS . $vector->id );
        $orginal_folder = $dir;
        $vector_path_temp = $dir.DS.$vector->large;
        $thumbnail_path_temp = $dir.DS.$vector->thumbnail;
        $preview_folder = $dir .DS. 'preview';
        $preview_path_temp = $preview_folder.DS.$vector->preview;
        $temp = public_path("uploads/temp/vectors/");
        $save_path =$temp.time().'vector_id_'.$vector->id.".png";
        $save_path_thumbnail =$temp.time().'thumbnail_vector_id_'.$vector->id.".png";
        if (!file_exists($preview_folder)) {
        mkdir($preview_folder, 0766, true);
        chmod($preview_folder, 0777); 
        chmod($dir, 0777); 
        }
        $vector_path_s3= 'uploads' . DS . 'vectors' . DS . $vector->id  .DS. $vector->large;
        file_put_contents($vector_path_temp, Storage::disk('s3')->get($vector_path_s3));
        $thumbnail_path_s3= 'uploads' . DS . 'vectors' . DS . $vector->id  .DS. $vector->thumbnail;
        $preview_path_s3= 'uploads' . DS . 'vectors' . DS . $vector->id .DS. 'preview' .DS. $vector->preview;
        $image = new \Imagick($vector_path_temp);
        $image->readimage($vector_path_temp);
        $dimension = $image->getImageGeometry();
        $w_thumb_vector = (int)$dimension['width'];
        $h_thumb_vector = (int)$dimension['height'];
        $image->setResolution(100,100);
        $image->scaleImage($w_thumb_vector, $h_thumb_vector);
        $image->setImageFormat("png");
       // header("Content-Type: image/png");
        $image->writeImage($save_path);
        $image->clear();
        $image->destroy();
        // PREVIEW
        $_width = $w_thumb_vector > $h_thumb_vector ? 640 : 0;
        $_height = $w_thumb_vector > $h_thumb_vector ? 0  : 640;
        $uploaded = Helper::resize_image_without_scale(
        $save_path,
        $_width,
        $_height,
        $preview_path_temp
        );

        // Thumbnail
        $_height =  280;
        $_width = 260;
        if($w_thumb_vector > $h_thumb_vector){
            (int)$_width =$w_thumb_vector *($_height / $h_thumb_vector);//min($_height, $w_thumb_vector);

        }
        Helper::resize_image_without_scale(
        $save_path,
        $_width,
        $_height,
        $thumbnail_path_temp
        );

        $watermarkSource = public_path('img/watermark.png');
        
        Helper::watermark($preview_path_temp, $watermarkSource);
        $pathS3 = Storage::disk('s3')->put($thumbnail_path_s3, file_get_contents($thumbnail_path_temp));// thmbmial
        $pathS3 = Storage::disk('s3')->put($preview_path_s3, file_get_contents($preview_path_temp));// preview
        $vector->width_thumbnail = intval(Helper::getWidth($thumbnail_path_temp));
        $vector->height_thumbnail = intval(Helper::getHeight($thumbnail_path_temp));
        $vector->width_preview = intval(Helper::getWidth($preview_path_temp));
        $vector->height_preview = intval(Helper::getHeight($preview_path_temp));
        $vector->height_large = $h_thumb_vector;
        $vector->width_large = $w_thumb_vector;
        $vector->save();
         \File::deleteDirectory($dir);
        \File::delete($save_path);
        // \File::delete($save_path_thumbnail);
        echo "Done   \n";
    
        \Log::info('end process_upload_vector S3 vector id :  '.$vector->id);
        //  exit($image);
        // } catch (\Exception $th) {
        //     echo "error  : process_upload_vector  \n";

        //     //throw $th;
        //      \Log::info('end error process_upload_vector S3 :  '.$th->getMessage());
    
        // }

}
});
\Log::info('****** end process_upload_vector S3 ****** '.now());

// INFO [] 2021-03-14 08:51:28 ****** start process_upload_vector S3 ****** 2021-03-14 08:51:28
// count : 873
// INFO [] 2021-03-14 09:34:12 ****** end process_upload_vector S3 ****** 2021-03-14 09:34:12