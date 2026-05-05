<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyLetterClientFile extends Model
{
    //
	protected $guarded = array();
    protected $appends = ['type'];

    public function fileable()
    {
        return $this->morphTo();
    }
    public function getTypeAttribute()
    {
        $fileable_type = $this->fileable_type;
        switch ($fileable_type) {
            case Image::class:
                $className = 'image';
                break;
            case Video::class:
                $className = 'video';
                break; 
            case Vector::class:
                $className = 'vector';
                break;                

        }
        return $className;
    }
}
