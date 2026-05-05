<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class AdminCollection extends Model
{
    protected $fillable = ['title', 'description', 'status', 'in_random_home'];
    protected $table = 'admin_collections';

    public function images()
    {
        return $this->belongsToMany(Image::class, 'admin_collection_images', 'admin_collection_id', 'image_id');
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'admin_collection_videos', 'admin_collection_id', 'video_id');
    }

    public function vectors()
    {
        return $this->belongsToMany(Vector::class, 'admin_collection_vectors', 'admin_collection_id', 'vector_id');
    }


}
