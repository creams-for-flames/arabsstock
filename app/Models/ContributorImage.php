<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Contributor;
use App\ImageTagContributor;
use App\Models\CategoryContributor;
use App\Models\ContributorImagesLegalRelease;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorImage extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $fillable = [
        'title_en',
        'title_ar',
        'thumbnail',
        'contributor_id',
        'original_name',
        'contributor_stage',
        'file_hash'
    ];
    const STATUS_NEW = 0;
    const STATUS_DATA_ENTRY = 1;
    const STATUS_REVIEW = 2;
    const STATUS_REJECT = 3;
    const STATUS_HARD_REJECT = 4;
    const STATUS_PROCESSING = 5;
    const STATUS_REVIEW2 = 6;
    const STATUS_PUBLISH = 8;

    public function user()
    {
        return $this->belongsTo(Contributor::class, 'contributor_id', 'id');
    }

    public function tags_img()
    {
        return $this->hasMany(ImageTagContributor::class, 'image_id');
    }

    public function category()
    {
        return $this->belongsToMany(CategoryContributor::class, 'category_contributor_image', 'image_id', 'category_id');
    }

    public function release_image()
    {
        return $this->hasMany(ContributorImagesLegalRelease::class, 'image_id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class, 'contributor_id', 'id');
    }

    public function file()
    {
        return $this->hasOne(Image::class, 'contributor_image_id', 'id');
    }

    public function submmission_item()
    {
        return $this->hasOne(ContributorImageSubmissionItem::class, 'image_id', 'id');
    }

    public function getThumbnailAttribute($val)
    {
        return cdn($val);
    }

}
