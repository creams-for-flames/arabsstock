<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }
}
