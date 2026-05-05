<?php

namespace App;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

class ScheduledUnsubscription extends Model
{
    protected $fillable = [
        'subscription_id',
        'date',
        'done',
    ];
    protected $dates = ['date'];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
