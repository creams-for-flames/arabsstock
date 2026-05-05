<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photographer extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function sessions()
    {
        return $this->belongsToMany(ImageFolder::class)
            ->withPivot('contract')
            ->withTimestamps();
    }
}
