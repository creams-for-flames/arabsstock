<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoLike extends Model {

	protected $guarded = array();
	public $timestamps = false;
	protected $table='video_likes';

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function images() {
        return $this->hasMany('App\Models\Video');
    }

}
