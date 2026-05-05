<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};

class PayoutItem extends Model
{
    use SoftDeletes;

    public function withdraw() {
        return $this->belongsTo(Withdraw::class,'withdraw_id');
    }
}
