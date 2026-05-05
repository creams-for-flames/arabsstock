<?php

namespace App\Contexts;

use App\Models\Image as ImagesModel;
use App\Models\CollectionImage;
use App\Models\ImagesReported;
use App\Models\CategoryImage;
use App\Models\ComputerVisionTag;
use App\Models\VisitImage;
use App\Models\Stock;

class Image
{
    public static function delete($ids)
    {
        foreach ($ids as $id) {
            $image = ImagesModel::find($id);

            // @note check Delete Notification

            CollectionImage::where('image_id', '=', $id)->delete();
            ImagesReported::where('image_id', '=', $id)->delete();
            CategoryImage::where('image_id', $id)->delete();
            ComputerVisionTag::where('image_id', $id)->delete();
            $image->tags()->detach();
            VisitImage::where('image_id', $id)->delete();

            // ALL RESOLUTIONS IMAGES
            $stocks = Stock::where('image_id', '=', $id)->get();

            foreach ($stocks as $stock) {
                // Delete Stock
                \Storage::disk('s3')->delete('uploads/' . $stock->type . '/' . $stock->name);
            }
            Stock::where('image_id', '=', $id)->delete();

            // Delete preview
            if ($image->preview) {
                \Storage::disk('s3')->delete('uploads/preview/' . $image->preview);
            }

            // Delete thumbnail
            if ($image->thumbnail) {
                \Storage::disk('s3')->delete('uploads/thumbnail/' . $image->thumbnail);
            }

            $image->delete();

            /* \Artisan::call('stock:resort'); @note why this code was used here */
        }

        return true;
    }
}
