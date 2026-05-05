<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{

    protected $appends = ['name'];
    protected $guarded = [];
    public $timestamps = false;

    public function getNameAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
