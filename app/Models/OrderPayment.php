<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['payment_method_id', 'user_id', 'order_id', 'payment_id', 'txn_id', 'total', 'currency', 'status', 'data'];

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REFUND = 2;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(OrderVideo::class);
    }
}
