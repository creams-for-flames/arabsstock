<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageLike extends Model
{

    protected $guarded = array();
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class)->first();
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

}
