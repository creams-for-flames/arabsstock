<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectionReason extends Model
{
    protected $guarded = [];
    protected $appends = ['type_lang','status_lang'];

    public function getTypeLangAttribute()
    {
       $type = __('admin.'.$this->type);
       return $type;
    }

    public function getStatusLangAttribute()
    {
       $type = __('views.'.ucfirst($this->status));
       return $type;
    }
}
