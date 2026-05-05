<?php

namespace App\Contexts;

use App\Models\Video as VideosModel;
use App\Models\CollectionVideo;
use App\Models\CategoryVideo;
use App\Models\ComputerVisionTag;
use Illuminate\Support\Facades\File;

class Video
{
    public static function delete($ids)
    {
        // TODO it should be refactored


        foreach ($ids as $id) {
            $video = VideosModel::find($id);

            // @note check Delete Notification

            CollectionVideo::where('video_id', '=', $id)->delete();
            /* ImagesReported::where('video_id', '=', $id)->delete(); */
            CategoryVideo::where('video_id', $id)->delete();
            ComputerVisionTag::where('video_id', $id)->delete();
            $video->tags()->detach();
            $path_prefix_relative = DS . 'uploads' . DS . 'videos' . DS . $id . DS;
            $path_prefix = public_path($path_prefix_relative);

            // ALL RESOLUTIONS VIDEOS
            $child_videos = VideosModel::where('parent_id', '=', $id)->get();

            foreach ($child_videos as $child_video) {
                // Delete child_video
                \Storage::disk('s3')->delete($child_video->preview);
            }
            VideosModel::where('parent_id', '=', $id)->delete();

            if (!$video) {
                continue;
            }

            // Delete thumbnail
            if ($video->thumbnail) {
                \Storage::disk('s3')->delete($video->thumbnail);
            }

            // Delete cut_video
            if ($video->cut_video) {
                \Storage::disk('s3')->delete($video->cut_video);
            }

            // Delete gif_video
            if ($video->gif_video) {
                \Storage::disk('s3')->delete($video->gif_video);
            }

            // Delete size_240p
            if ($video->size_240p) {
                \Storage::disk('s3')->delete($video->size_240p);
            }

            // Delete preview
            if ($video->preview) {
                \Storage::disk('s3')->delete($video->preview);
            }

            // Delete local folder
            if (file_exists($path_prefix)) {
                File::deleteDirectory($path_prefix);
            }

            $video->delete();
        }

        return true;
    }
}
