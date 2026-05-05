<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminVideoSettings extends Model
{

    protected $table = 'admin_video_settings';
    protected $guarded = array();
    protected $appends = ['title'];
    public $timestamps = false;


    public function getTitleAttribute()
    {

        return $this->{'title_' . app()->getLocale()};

    }

    public function getTitleImageAttribute()
    {

        return $this->{'title_image_' . app()->getLocale()};

    }

    public function getDescriptionAttribute()
    {

        return $this->{'description_' . app()->getLocale()};

    }


    public function getKeywordsAttribute()
    {

        return $this->{'keywords_' . app()->getLocale()};

    }

}
