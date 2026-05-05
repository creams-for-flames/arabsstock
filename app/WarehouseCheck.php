<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseCheck extends Model
{
    use SoftDeletes;
    public function warehouseable()
    {
        return $this->morphTo();
    }
}
