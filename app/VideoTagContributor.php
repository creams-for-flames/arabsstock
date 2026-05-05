<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoTagContributor extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'video_tags_contributor';
    protected $fillable = [
        'video_id',
        'tag',
        'local',
        'confidence',
        'slug'
    ];


    public function videos()
    {
        return $this->belongsTo(ContributorVideo::class, 'video_id', 'id');
    }
}
