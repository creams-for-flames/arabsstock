<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockVideo extends Model {

	protected $table = 'stock_videos';
	protected $guarded = array();
	public $timestamps = false;

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function video() {
        return $this->belongsTo('App\Models\Video');
    }

}
