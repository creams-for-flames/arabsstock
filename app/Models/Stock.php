<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model {

	protected $table = 'stock';
	protected $guarded = array();
	public $timestamps = false;
	protected $fillable=['is_uploaded'];

	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function image() {
        return $this->belongsTo('App\Models\Image','image_id','id');
    }

}
