<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageTagContributor extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'image_tags_contributor';
    protected $appends = ['post_link_ar','post_link_en','post_link'];
    protected $fillable = [
        'image_id',
        'tag',
        'local',
        'confidence',
        'slug'
    ];


    public function images()
    {
        return $this->belongsTo(ContributorImage::class, 'image_id', 'id');
    }


    public function getPostLinkAttribute()
    {
        $id = $this->slug;
        return url(app()->getLocale().'/photos/tags', preg_replace('/[[:space:]]+/', '-', ($id)));
    }

    public function getPostLinkArAttribute()
    {
        $id = $this->slug;
        return url('ar/photos/tags', preg_replace('/[[:space:]]+/', '-', ($id)));
    }


    public function getPostLinkEnAttribute()
    {
        $id = $this->slug;
        return url('en/photos/tags', preg_replace('/[[:space:]]+/', '-', ($id)));
    }
}
