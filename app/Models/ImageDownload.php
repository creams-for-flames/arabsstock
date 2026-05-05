<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageDownload extends Model {

	protected $guarded = array();

	protected $fillable = ['image_id','user_id','ip','plan_id','subscription_id',];
	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function client() {
        return $this->belongsTo('App\Models\User','user_id');
    }

	public function images(){
		return $this->belongsTo('App\Models\Image')->first();
	}

	public function image(){
		return $this->belongsTo('App\Models\Image','image_id')->withoutGlobalScope('reserved');
	}

	public function plan(){
		return $this->belongsTo(ImagePlan::class,'plan_id');
	}

	public function subscription(){
		return $this->belongsTo(ImageSubscription::class);
	}


}
