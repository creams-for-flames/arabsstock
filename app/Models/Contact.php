<?php

namespace App\Models;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded = [];
    // protected $fillable = [ 'title_en','title_ar', 'content_en','content_ar','image' ];
    protected $appends = [ 'age'];

    public function getTitleAttribute()
    {
        return $this->{'name_' . app()->getLocale()};
    }


    public function getAgeAttribute()
    {
        list($year, $month, $day) = explode("-", date("Y-m-d"));
        if ($this->attributes['birth_date'])
            return $age = $year - Carbon::parse($this->attributes['birth_date'])->format('Y');
        return '-';
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city');
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'nationality');
    }

    public function skills()
    {
        return $this->hasMany(Skills::class, 'skills_contacts');
    }

    public function images()
    {
        return $this->morphMany(Attatchments::class, 'attatchmentable');
    }

    public function nationality_casting()
    {
        return $this->belongsTo(Countries::class, 'nationality_one', 'id');
    }


}
