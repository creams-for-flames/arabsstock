<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeDownload extends Model
{

    protected $fillable = [
        'entity_id',
        'entity_type',
        'credits',
        'license_type',
        'user_id',
        'ip',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->morphTo('entity');
    }

}
