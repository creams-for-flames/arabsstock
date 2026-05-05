<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class)->withPivot('value', 'complete');
    }
}
