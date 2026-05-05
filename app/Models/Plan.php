<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * Class ImagePlan
 *
 * @package App
 * @property string $title
 * @property decimal $price
 * @property integer $credits_count
 * @property string $type
 */
class Plan extends Model
{
    use SoftDeletes;

    // public $timestamps = false;

    protected $fillable = [
        'paypal_plan',
        'stripe_plan',
        'title_en',
        'title_ar',
        'slug',
        'uuid',
        'price',
        'credit_price',
        'credits_count',
        'type',
        'on_demand',
        'in_show_page',
        'status',
        'free',
        'for_teams',
        'license',
        'can_cancel',
        'is_default',
        'trial_days',
        'hidden',
    ];
    protected $hidden = [];
    protected $appends = ['title', 'frequency', 'description'];


    /**
     * Set attribute to money format
     * @param $input
     */
    public function setPriceAttribute($input)
    {
        $this->attributes['price'] = $input ? $input : null;
    }


    public function getPriceAttribute($val)
    {
        if ($this->on_demand && session()->has('on_demand_subscription') && @session()->get('on_demand_subscription')['plan_id'] == $this->id)
            return session()->get('on_demand_subscription')['credits_count'] * $this->credit_price;
        return $val;
    }

    public function getCreditsCountAttribute($val)
    {
        if ($this->on_demand && session()->has('on_demand_subscription') && @session()->get('on_demand_subscription')['plan_id'] == $this->id)
            return session()->get('on_demand_subscription')['credits_count'];
        return $val;
    }

    public function getTitleAttribute()
    {
        if ($this->on_demand && session()->has('on_demand_subscription') && @session()->get('on_demand_subscription')['plan_id'] == $this->id) {
            return session()->get('on_demand_subscription')['credits_count'] . ' ' . __('credit');
        }
        $title = $this->{'title_' . app()->getLocale()};
        if (!empty($title))
            return $title;
        return $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        if ($this->frequency == 'month')
            return __(":type Subscription, :credits Credits /month", ['credits' => $this->credits_count, 'type' => __($this->type)]);
        if ($this->frequency == 'year')
            return __(":type Subscription, :credits Credits /year", ['credits' => $this->credits_count * 12, 'type' => __($this->type)]);
        return __("One-time Subscription, :credits Credits", ['credits' => $this->credits_count]);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function getFrequencyAttribute()
    {
        return @['package' => false, 'monthly' => 'month', 'annual' => 'year'][$this->type];
    }

    public static function onDemandCreditsPrice($credits_count)
    {
        $prices = \App\Models\Plan::where('type', 'package')->where('status', 1)->where('hidden', 0)->where('credits_count', '>', 1)->select('credit_price', 'credits_count')->orderBy('credits_count')->get()->pluck('credit_price', 'credits_count')->toArray();
        $prices[0] = 12;
        $prices = Arr::sort($prices, function ($val, $index) {
            return $index;
        });
        foreach ($prices as $credits => $price) {
            if ($credits > $credits_count) {
                break;
            }
            $credit_price = $price;
        }
        return $credit_price;
    }
}
