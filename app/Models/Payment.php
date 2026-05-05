<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $fillable = ['payment_method_id', 'subscription_type', 'subscription_id', 'transaction_id', 'currency_code', 'payment_status'];

}
