<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Relation::morphMap([
//     'product' => 'App\Models\Product',
//     'service' => 'App\Models\Service',
// ]);


class Attatchments extends Model
{

    use SoftDeletes;

    public $table = 'attatchments';
    protected $fillable = [
        'attatchmentable_id',
        'attatchmentable_type',
        'image',
        'type',
    ];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];


    public function getImageAttribute($image)
    {
        if (!is_null($image)) {
            return env('DO_SPACES_URL') . "/{$image}";
        }
        return "";
    }


    public function attatchmentable()
    {
        return $this->morphTo();
    }

}
