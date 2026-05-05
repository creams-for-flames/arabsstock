<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitVector extends Model {

	protected $guarded = array();
	public $timestamps = false;
	protected $table='visit_vectors';
	protected $fillable=['vector_id','user_id','ip','date'];
	public function user() {
        return $this->belongsTo('App\Models\User');
    }

	public function vectors(){
		return $this->belongsTo('App\Models\Video','vector_id');
	}

}
