<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoCategory extends Model
{
    use SoftDeletes;

    protected $table = 'video_categories';
    protected $guarded = [];
    protected $appends = ['name', 'post_link', 'post_link_video', 'post_link_vector'];
    protected $fillable = ['sort', 'in_home', 'show_in_trending_list', 'cities_and_landmarks', 'is_uploaded', 'cover', 'people'];

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'category_video', 'category_id', 'video_id')->where('videos.status', 'active');
    }

    public function getNameAttribute()
    {
        $lang = app()->getLocale();
        $name = 'name_' . $lang;
        if ($this->$name != null) {
            return $this->$name;

        } else {
            return $this->name_ar;

        }
    }

    public function getThumbnailAttribute($value)
    {
        if ($value != null) {
            return cdn('uploads/img-category/' . $value);
        } else {
            return asset('img-category/default2.jpg');
        }
    }

    public function getCoverAttribute($value)
    {
        if ($value != null) {
            return cdn('uploads/img-category/' . $value);
        } else {
            return asset('img-category/default2.jpg');
        }
    }

    public function getPostLinkAttribute()
    {
        $id = $this->slug;
        return url('ar/photos/category/' . $id);
    }

    public function getPostLinkVideoAttribute()
    {
        $id = $this->slug;
        return url('ar/videos/category/' . $id);
    }

    public function getPostLinkVectorAttribute()
    {
        $id = $this->slug;
        return url('ar/vectors/category/' . $id);
    }

}
