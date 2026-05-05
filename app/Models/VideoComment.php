<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model {

	protected $guarded = array();
	public $timestamps = false;
		protected $table='comment_videos';
		protected $fillable=['video_id','user_id','reply','date','status'];
	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function videos(){
		return $this->belongsTo('App\Models\Video')->first();
	}

	public function total_likes(){
		return $this->hasMany('App\Models\VideoCommentLike','comment_id')->where('status','1');
	}

}
