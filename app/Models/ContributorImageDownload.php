<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorImageDownload extends Model {

	protected $guarded = array();
    protected $table = 'contributor_image_download';
	protected $fillable = ['image_id','contributor_id','user_id','plan_id','subscription_id','plan_price','image_price','profit_ratio','profit_value','type_download','image_download_id'];

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function image(){
		return $this->belongsTo('App\Models\Image','image_id');
	}

	public function plan(){
		return $this->belongsTo(ImagePlan::class);
	}

	public function subscription(){
		return $this->belongsTo(ImageSubscription::class);
	}
	public function contributor(){
		return $this->belongsTo(Contributor::class);
	}
	public function image_download(){
		return $this->belongsTo(ImageDownload::class,'image_download_id');
	}

}
