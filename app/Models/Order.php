<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'city_id',
        'country_id',
        'resource_type',
        'resource_id',
        'payment_method_id',
    ];

    public function resource()
    {
        return $this->morphTo('resource');
    }
}
