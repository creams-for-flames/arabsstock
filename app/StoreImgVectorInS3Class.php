<?php

namespace App;

use App\Models\Image;
use App\Models\Vector;
use App\Models\Stock;
use App\Models\StockVector;
use App\Models\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class StoreImgVectorInS3Class
{
    public static function storeS3($id)
    {
        \Log::channel('info')->info('Image Start4');
        $child = StockVector::findOrFail($id);
        $parent = Vector::find($child->vector_id);
        if(!$parent){
         \Log::channel('info')->info('Image Not Found in StoreImgVectorInS3Class');
          return;
        }

        $nameFile = 'uploads' . DS . $child->type . DS . $child->name;


      //  \Log::channel('info')->info([$id]);

        $nameParentFileThumbnail = 'uploads' . DS . 'thumbnail' . DS . $parent->thumbnail;
        $nameParentFilePreview = 'uploads' . DS . 'vector' . DS . $parent->vector;
        $pathS3 = Storage::disk('s3')->put($nameFile, file_get_contents(public_path() . DS . $nameFile));
        \Log::channel('info')->info('Image Start5');
        if ($pathS3) {
            \Log::channel('info')->info('Image Start6');
            $child->is_uploaded = 1;
            $child->save();

            if (file_exists(public_path() . DS .$nameFile)) {

                \Log::channel('info')->info([public_path() . DS .$nameFile]);
                unlink(public_path() . DS ."$nameFile");


            }
        }


        // $childUpload = StockVector::where('vector_id', $child->vector_id)->where('is_uploaded', 0)->get();

        // if (count($childUpload) > 0) {


        //     $storeNameParentFileThumbnail = Storage::disk('s3')->put($nameParentFileThumbnail, file_get_contents(public_path() . DS . $nameParentFileThumbnail));
        //     $storeNameParentFilePreview = Storage::disk('s3')->put($nameParentFilePreview, file_get_contents(public_path() . DS . $nameParentFilePreview));




        //     if (file_exists(public_path() . DS .$nameParentFileThumbnail)) {
        //         unlink(public_path() . DS ."$nameParentFileThumbnail");


        //     }
        //     if (file_exists(public_path() . DS .$nameParentFilePreview)) {
        //         unlink(public_path() . DS ."$nameParentFilePreview");


        //     }
        // }

        // \Log::channel('info')->info($pathS3 . 'path');
        // return $child->id;
    }

}





