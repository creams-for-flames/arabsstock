<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawVideo extends Model
{
    protected $guarded = [];
    public function raw()
    {
        return $this->belongsTo(Video::class, 'video_id', 'id');
    }

}
