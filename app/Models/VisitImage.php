<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitImage extends Model
{

    protected $guarded = array();
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

}
