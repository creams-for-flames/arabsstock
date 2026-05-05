<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryContributorVector extends Model
{
    protected $table = "category_contributor_vector";
    protected $guarded = array();
    public $timestamps = false;
    protected $fillable = [
        'vector_id',
        'category_id',
    ];
}
