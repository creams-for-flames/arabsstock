<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionLocation extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function scopeLatestLocation($query)
    {
        return $query->orderBy('location_image_folder.created_at', 'desc');
    }
}
