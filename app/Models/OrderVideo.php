<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderVideo extends Model {

    protected $table 	= 'orders_videos';
	protected $fillable = ['user_id','country_id','city_id','status'];

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE  = 1;
    const STATUS_REFUND  = 2;

	public function user() {
        return $this->belongsTo(User::class);
    }

	public function payment() {
        return $this->hasOne(OrderPayment::class,'order_id');
    }

	public function country() {
        return $this->belongsTo(Countries::class);
    }

	public function city() {
        return $this->belongsTo(Cities::class);
    }

	public function items(){
		return $this->hasMany(OrderItemsVideo::class,'order_id');
	}

}
