<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Download extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'credits',
        'additional_credits',
        'additional_credits_reason',
        'license_type',
        'removebg',
        'raw',
        'user_id',
        'team_id',
        'ip',
        'date',
        'unit_price',
        'section_const_id',
        'hide',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->morphTo('entity');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class)->withPivot('credits', 'created_at');
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
