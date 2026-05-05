<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;


class Credit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'operation',
        'entity_id',
        'entity_type',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(FlexPlan::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class)->withTrashed();
    }

    public function RemainingDays()
    {
        $ends_at = Carbon::parse($this->ends_at);
        $interval = now()->diffInDays($ends_at, false);
        return ($ends_at >= now() && $interval > 0) ? $interval : 0;
    }

    public function RemainingDownloads()
    {
        return $this->RemainingDays() ? $this->download_remaining : 0;
    }

    public function isActive()
    {
        return $this->RemainingDays() && $this->RemainingDownloads() && in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_CANCEL]);
    }


    public function scopeActive($q)
    {
        if (!auth()->check())
            return $q;
        return $q->where([
            ['image_subscriptions.download_remaining', '>', 0],
            ['image_subscriptions.user_id', '=', auth()->id()],
            ['image_subscriptions.ends_at', '>=', date('Y-m-d H:i:s')],
            ['image_subscriptions.plan_type', '=', 'monthly'],
            ['image_subscriptions.status', '=', ImageSubscription::STATUS_ACTIVE],
        ]);
    }
}
