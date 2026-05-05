<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorVectorDownload extends Model {

	protected $guarded = array();

	protected $fillable = ['image_id','contributor_id','user_id','plan_id','subscription_id','plan_price','image_price','profit_ratio','profit_value','type_download','vector_download_id'];

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function image(){
		return $this->belongsTo('App\Models\Vector','image_id');
	}

	public function plan(){
		return $this->belongsTo(VectorPlan::class);
	}

	public function subscription(){
		return $this->belongsTo(VectorSubscription::class);
	}
	public function contributor(){
		return $this->belongsTo(Contributor::class);
	}
	public function vector_download(){
		return $this->belongsTo(VectorDownload::class,'vector_download_id');
	}

}
