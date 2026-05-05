<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model {

	protected $guarded = array();
	public $timestamps = false;

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function images(){
		return $this->belongsTo('App\Models\Image')->first();
	}

	public function total_likes(){
		return $this->hasMany('App\Models\ImageCommentLike','comment_id')->where('status','1');
	}

}
