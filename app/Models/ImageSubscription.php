<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;


class ImageSubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'city_id',
        'country_id',
        'plan_id',
        'payment_method_id',
        'subscription_id',
        'quantity',
        'created_by_hook',
        'download_remaining',
        'plan_type',
        'currency',
        'amount',
        'payment_gateway_fee',
        'payment_id',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'status',
        'renewal',
        'invoice_file',
        'data',
    ];
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CANCEL = 2;
    const STATUS_REFUND = 3;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    public function plan()
    {
        return $this->belongsTo(ImagePlan::class)->withTrashed();
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

    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCEL;
    }

    public function downloads()
    {
        return $this->hasMany(ImageDownload::class, 'subscription_id');
    }

    public function scopeActive($q)
    {
        if (!auth()->check())
            return $q;
        return $q->where([
            ['image_subscriptions.download_remaining', '>', 0],
            ['image_subscriptions.user_id', '=', auth()->id()],
            ['image_subscriptions.ends_at', '>=', date('Y-m-d H:i:s')],
        ])->where(function ($q) {
            $q->where(function ($q) {
                $q->whereIn('image_subscriptions.status', [self::STATUS_ACTIVE, self::STATUS_CANCEL])->whereIn('image_subscriptions.plan_type', ['monthly', 'annual']);
            })->orWhere(function ($q) {
                $q->whereIn('image_subscriptions.status', [self::STATUS_ACTIVE])->where('image_subscriptions.plan_type', 'package');
            });
        });
    }
}
