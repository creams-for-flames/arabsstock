<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'local',
    ];

    public function images()
    {
        return $this->belongsToMany(Image::class, 'tag_image');
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'tag_video');
    }

    public function vectors()
    {
        return $this->belongsToMany(Vector::class);
    }
}
