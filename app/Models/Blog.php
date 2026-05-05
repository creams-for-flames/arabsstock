<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [ 'title_en','title_ar', 'content_en','content_ar','image' ];
    protected $append = ['title','content'];

    public function getTitleAttribute(){
        return $this->{'title_'.app()->getLocale()};
    }

    public function getContentAttribute(){
        return $this->{'content_'.app()->getLocale()};
    }

}
