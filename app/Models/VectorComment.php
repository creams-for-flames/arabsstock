<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VectorComment extends Model {

	protected $guarded = array();
	public $timestamps = false;
		protected $table='comment_vectors';
		protected $fillable=['vector_id','user_id','reply','date','status'];
	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function videos(){
		return $this->belongsTo('App\Models\Vector')->first();
	}

	public function total_likes(){
		return $this->hasMany('App\Models\VectorCommentLike','comment_id')->where('status','1');
	}

}
