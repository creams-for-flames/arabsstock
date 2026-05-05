<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockVector extends Model {

	protected $table = 'stock_vectors';
	protected $guarded = array();
	public $timestamps = false;

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function vector() {
        return $this->belongsTo('App\Models\Vector');
    }

}
