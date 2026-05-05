<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalPayer extends Model
{
    protected $fillable = ['payer_id', 'subscription_id', 'user_id', 'plan_id', 'ip', 'status', 'notes','resource'];
}
