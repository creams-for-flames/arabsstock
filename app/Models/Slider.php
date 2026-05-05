<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use SoftDeletes;
    protected $fillable = ['image', 'youtube_url', 'order_by', 'createdBy'];
    protected $hidden = ['translations', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['title'];

    public function getImageAttribute($value)
    {
        return url($value);
    }

    public function scopePublic($query, $isActive = 'active', $orderBy = 'asc')
    {
        return $query->where(['status' => $isActive])->orderBy('order_by', $orderBy);
    }



    public function getStatusAttribute($value)
    {
        if ($value == 'not_active')
            return "Not Active";
        return "Active";
    }



}
