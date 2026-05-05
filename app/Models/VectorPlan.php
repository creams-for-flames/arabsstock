<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ImagePlan
 *
 * @package App
 * @property string $title
 * @property decimal $price
 * @property integer $downloads_count
 * @property string $type
 */
class VectorPlan extends Model
{
    use SoftDeletes;

    public $content_type = 'vector';
    protected $fillable = [
        'braintree_plan',
        'paypal_plan',
        'stripe_plan',
        'title_en',
        'title_ar',
        'slug',
        'uuid',
        'price',
        'downloads_count',
        'type',
        'status',
        'free',
        'license',
        'can_cancel',
    ];
    protected $hidden = [];
    protected $appends = ['title', 'frequency'];


    /**
     * Set attribute to money format
     * @param $input
     */
    public function setPriceAttribute($input)
    {
        $this->attributes['price'] = $input ? $input : null;
    }

    /**
     * Set attribute to money format
     * @param $input
     */
    public function setDownloadsCountAttribute($input)
    {
        $this->attributes['downloads_count'] = $input ? $input : null;
    }

    public function getTitleAttribute()
    {
        $title = $this->{'title_' . app()->getLocale()};
        if (!empty($title))
            return $title;
        return $this->title_en;
    }

    public function subscriptions()
    {
        return $this->hasMany(VectorSubscription::class, 'plan_id');
    }

    public function getFrequencyAttribute()
    {
        return @['package' => false, 'monthly' => 'month', 'annual' => 'year'][$this->type];
    }

    public function getDescriptionAttribute()
    {
        if ($this->frequency)
            return __(":type Subscription, with :downloads Downloads /:frequency", ['frequency' => __($this->frequency), 'downloads' => $this->downloads_count, 'type' => __($this->type)]);
        return __("One-time Subscription, with :downloads Downloads", ['downloads' => $this->downloads_count]);
    }

}
