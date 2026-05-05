<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionInvoice extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function invoiceable()
    {
        return $this->morphTo();
    }
}
