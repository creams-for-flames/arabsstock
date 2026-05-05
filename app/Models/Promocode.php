<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Promocode extends Model
{
    use SoftDeletes;

    protected $dates = ['expired_at'];
    protected $appends = ['title'];
    protected $fillable = [
        'title_ar',
        'title_en',
        'code',
        'expired_at',
        'type',
        'value',
        'max_usage',
        'max_users',
        'status',
        'created_by_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'promocode_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('expired_at', '>', now());
    }

    public function calculate_price($price)
    {
        return round($price - $this->calculate_discount($price));
    }

    public function calculate_discount($price)
    {
        if ($this->type == 'amount')
            return $this->value;
        elseif ($this->type == 'percent')
            return round($this->value * 0.01 * $price);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class)->withPivot('paypal_plan');
    }


    public function getTitleAttribute()
    {
        $lang = app()->getLocale();
        $name = 'title_' . $lang;
        if (@$this->$name) {
            return $this->$name;

        } else {
            return $this->title_ar;
        }
    }
}
