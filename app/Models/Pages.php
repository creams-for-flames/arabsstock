<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model {

	protected $guarded = array();
	public $timestamps = false;
	
	protected $fillable = [ 'title_en','title_ar', 'content_en','content_ar', 'slug' ];




}