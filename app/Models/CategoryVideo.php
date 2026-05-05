<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryVideo extends Model
{
    protected $table = 'category_video';
    protected $guarded = array();
    public $timestamps = false;
    protected $fillable = [
        'video_id',
        'category_id',

    ];

    public function video()
    {
        return $this->belongsTo('App\Models\Video');
    }

    public function category()
    {
        return $this->belongsTo(VideoCategory::class);
    }
}
