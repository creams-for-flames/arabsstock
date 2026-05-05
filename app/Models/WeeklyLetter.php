<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyLetter extends Model
{
    protected $table = 'weekly_letters';
    protected $fillable = [
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'image_5',
        'image_6',
        'cat_1_name',
        'cat_1_image',
        'cat_1_url',
        'cat_2_name',
        'cat_2_image',
        'cat_2_url',
        'cat_3_name',
        'cat_3_image',
        'cat_3_url',
        'cat_4_name',
        'cat_4_image',
        'cat_4_url',
        'cat_5_name',
        'cat_5_image',
        'cat_5_url',
        'cat_6_name',
        'cat_6_image',
        'cat_6_url',
        'sent',
        'image_generated',
        'target',
        'custom_target',
        'image_1_path',
        'image_2_path',
        'image_3_path',
        'image_4_path',
        'image_5_path',
        'image_6_path',
        'cat_1_image_path',
        'cat_2_image_path',
        'cat_3_image_path',
        'cat_4_image_path',
        'cat_5_image_path',
        'cat_6_image_path'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'weekly_letters_users');
    }
    public function first_image(){
        return $this->belongsTo(Image::class,'image_1');
    }
    public function second_image(){
        return $this->belongsTo(Image::class,'image_2');
    }
    public function third_image(){
        return $this->belongsTo(Image::class,'image_3');
    }
    public function fourth_image(){
        return $this->belongsTo(Image::class,'image_4');
    }
    public function fifth_image(){
        return $this->belongsTo(Image::class,'image_5');
    }
    public function sixth_image(){
        return $this->belongsTo(Image::class,'image_6');
    }
    public function first_cat_image(){
        return $this->belongsTo(Image::class,'cat_1_image');
    }
    public function second_cat_image(){
        return $this->belongsTo(Image::class,'cat_2_image');
    }
    public function third_cat_image(){
        return $this->belongsTo(Image::class,'cat_3_image');
    }
    public function fourth_cat_image(){
        return $this->belongsTo(Image::class,'cat_4_image');
    }
    public function fifth_cat_image(){
        return $this->belongsTo(Image::class,'cat_5_image');
    }
    public function sixth_cat_image(){
        return $this->belongsTo(Image::class,'cat_6_image');
    }
}
