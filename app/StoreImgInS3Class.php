<?php

namespace App;

use App\Models\Image;
use App\Models\Stock;
use App\Models\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class StoreImgInS3Class
{
    public static function storeS3($id)
    {
        \Log::channel('info')->info('Image Start4');
        $child = Stock::findOrFail($id);
        $parent = Image::find($child->image_id);
        if (!$parent) {
            \Log::channel('info')->info('Image Not Found in StoreImgInS3Class');
            return;
        }

        $nameFile = 'uploads' . DS . 'images' . DS . $parent->id . DS . $child->type . DS . $child->name;

        $nameParentFileThumbnail = $parent->thumbnail;
        $nameParentFilePreview = $parent->preview;
        $full_path_file = public_path() . DS . $nameFile;
        $pathS3 = Storage::disk('s3')->put($nameFile, file_get_contents($full_path_file));
        \Log::channel('info')->info('Image Start5');
        if ($pathS3) {
            \Log::channel('info')->info('Image Start6');
            $child->is_uploaded = 1;
            $child->save();

            if (file_exists(public_path() . DS . $nameFile)) {
                /* ImageHash start */
                $hash = hash_file('sha256', $full_path_file);
                $child->hash = $hash;
                $child->save();

                /* ImageHash end */

                \Log::channel('info')->info([public_path() . DS . $nameFile]);
                unlink(public_path() . DS . "$nameFile");
            }
        }
        $childUpload = Stock::where('image_id', $child->image_id)->where('is_uploaded', 0)->get();
        if (count($childUpload) == 0) {
            Storage::disk('s3')->put($nameParentFileThumbnail, file_get_contents(public_path() . DS . $nameParentFileThumbnail));
            Storage::disk('s3')->put($nameParentFilePreview, file_get_contents(public_path() . DS . $nameParentFilePreview));

            if (file_exists(public_path() . DS . $nameParentFileThumbnail)) {
                unlink(public_path() . DS . "$nameParentFileThumbnail");
            }
            if (file_exists(public_path() . DS . $nameParentFilePreview)) {
                unlink(public_path() . DS . "$nameParentFilePreview");
            }
        }
    }

}





