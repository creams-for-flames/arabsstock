<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionImage extends Model
{

    protected $guarded = array();
    public $timestamps = false;
    protected $table = 'collection_image';

    public function user()
    {
        return $this->belongsTo(User::class)->first();
    }

    public function images()
    {
        return $this->belongsTo(Image::class)->where('status', 'active')->orderBy('id', 'desc')->first();
    }

    public function collection()
    {
        return $this->belongsTo(ImageCollection::class)->first();
    }

    public function belongsCollection()
    {
        return $this->belongsTo(ImageCollection::class, 'collection_id')->orderBy('id', 'desc');
    }

    public function collections()
    {
        return $this->hasMany(ImageCollection::class)->orderBy('id', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(ImageLike::class, 'image_id', 'image_id')->where('status', '1');
    }

    public function downloads()
    {
        return $this->hasMany(ImageDownload::class, 'image_id', 'image_id');
    }

}
