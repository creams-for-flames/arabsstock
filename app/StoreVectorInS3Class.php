<?php

namespace App;

use App\Models\Vector;
use App\Models\Stock;
use App\Models\StockVector;
use App\Models\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class StoreVectorInS3Class
{
    public static function storeS3($id)
    {
        \Log::channel('info')->info('Vector Start4');
        $child = StockVector::findOrFail($id);
        $parent = Vector::find($child->vector_id);

        if(!$parent){
         \Log::channel('info')->info('Vector Not Found in StoreVectorInS3Class');
          return;
        }

        $nameFile = 'uploads' . DS . $child->extension . DS . $child->name;


      //  \Log::channel('info')->info([$id]);

        $nameParentFileThumbnail = 'uploads' . DS . 'thumbnail' . DS . $parent->thumbnail;
        $nameParentFilePreview = 'uploads' . DS . 'preview' . DS . $parent->preview;

        $pathS3 = Storage::disk('s3')->put($nameFile, file_get_contents(public_path() . DS . $nameFile));
        \Log::channel('info')->info('Vector Start5');
        if ($pathS3) {
            \Log::channel('info')->info('Vector Start6');
            $child->is_uploaded = 1;
            $child->save();

            if (file_exists(public_path() . DS .$nameFile)) {

                \Log::channel('info')->info([public_path() . DS .$nameFile]);
                unlink(public_path() . DS ."$nameFile");


            }
        }


        $childUpload = Vector::where('vector_id', $child->vector_id)->where('is_uploaded', 0)->get();

        if (count($childUpload) == 0) {


            $storeNameParentFileThumbnail = Storage::disk('s3')->put($nameFile, file_get_contents(public_path() . DS . $nameFile));


            if (file_exists(public_path() . DS .$$nameFile)) {
                unlink(public_path() . DS ."$nameFile");


            }

        }

        // \Log::channel('info')->info($pathS3 . 'path');
        // return $child->id;
    }

}





