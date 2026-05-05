<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemsVideo extends Model {

    protected $table    = 'order_items_videos';
	protected $fillable = ['order_id','video_id','user_id','amount','token'];

	public function user() {
        return $this->belongsTo(User::class);
    }

	public function order() {
        return $this->belongsTo(OrderVideo::class);
    }

	public function video() {
        return $this->belongsTo(Video::class);
    }

    public function country() {
        return $this->belongsTo(Countries::class);
    }

    public function city() {
        return $this->belongsTo(Cities::class);
    }



}
