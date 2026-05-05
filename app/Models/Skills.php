<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skills extends Model
{
    protected $append = ['name'];

    public function getTitleAttribute(){
        return $this->{'name_'.app()->getLocale()};
    }


}
