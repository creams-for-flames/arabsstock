<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryContributorImage extends Model
{
    protected $guarded = array();
    protected $table = "category_contributor_image";
    public $timestamps = false;
    protected $fillable = [
        'image_id',
        'category_id',

    ];

    public function image()
    {
        return $this->belongsTo(ContributorImage::class);
    }

    public function category()
    {
        return $this->belongsTo(ImageCategory::class);
    }
}
