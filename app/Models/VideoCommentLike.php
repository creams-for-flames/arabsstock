<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCommentLike extends Model
{

    protected $guarded = array();
    public $timestamps = false;
    protected $table = 'comments_video_likes';
    protected $fillable = ['user_id', 'comment_id', 'status'];

    public function user()
    {
        return $this->belongsTo('App\Models\User')->first();
    }
}
