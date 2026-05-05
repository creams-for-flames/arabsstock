<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class ImageCollection extends Model
{

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collection_images()
    {
        return $this->hasMany(CollectionImage::class)->orderBy('id', 'desc');
    }

    public function collection_videos()
    {
        return $this->hasMany(CollectionVideo::class, 'collection_id', 'id')->orderBy('id', 'desc');
    }

    public function collection_vectors()
    {
        return $this->hasMany(CollectionVector::class, 'collection_id', 'id')->orderBy('id', 'desc');
    }

}
