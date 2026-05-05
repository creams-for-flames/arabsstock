<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageCategory extends Model
{
    protected $table = 'image_categories';
    protected $guarded = [];
    protected $appends = ['name', 'post_link', 'post_link_video', 'post_link_vector'];
    protected $fillable = ['sort', 'in_home', 'show_in_trending_list', 'cities_and_landmarks', 'is_uploaded', 'cover', 'people'];

    public function images()
    {
        return $this->belongsToMany(Image::class, 'category_image', 'category_id', 'image_id')->where('images.status', 'active');
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
            return asset('uploads/img-category/' . $value);
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
