<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionRenewal extends Model
{
    protected $fillable = [
        'expired_credits',
        'downloads_count',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
