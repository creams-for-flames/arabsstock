<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentTransfer extends Model
{
    protected $fillable = [
        'from_contributor_id',
        'to_contributor_id',
    ];

    public function to_contributor()
    {
        return $this->belongsTo(Contributor::class, 'to_contributor_id');
    }
}
