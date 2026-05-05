<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryContributorVideo extends Model
{
    protected $table = 'category_contributor_video';
    public $timestamps = false;
    protected $fillable = [
        'video_id',
        'category_id',
    ];
}

