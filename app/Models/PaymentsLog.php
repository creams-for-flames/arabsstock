<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentsLog extends Model {

    protected $table      = 'payments_logs';
	protected $fillable   = ['user_id','payment_method_id','webhook_id','event_type','resource_type','category','plan_id','subscription_id','order_id','data','type'];
	protected $casts   	  = ['data' => 'array'];

	public function user() {
        return $this->belongsTo(User::class);
    }
}
