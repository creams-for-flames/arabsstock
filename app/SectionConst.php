<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SectionConst extends Model
{
    protected $table = 'section_const';
    protected $primaryKey = 'section_id';
    protected $fillable = [
        'standard_credits',
        'enhanced_credits',
        'section_id',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('last', function (Builder $builder) {
            $builder->where('status', 1)->orderBy('id', 'desc')->take(1);
        });
    }
}
