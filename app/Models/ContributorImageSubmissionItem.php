<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorImageSubmissionItem extends Model
{
    protected $table = 'contributor_image_submission_items';
    protected $fillable = ['contributor_submission_id', 'image_id', 'video_id'];
    public $timestamps = false;

    public function image()
    {
        return $this->belongsTo(ContributorImage::class, 'image_id', 'id');
    }

    public function file()
    {
        return $this->belongsTo(ContributorImage::class, 'image_id', 'id');
    }

    public function submmission()
    {
        return $this->belongsTo(ContributorImageSubmission::class, 'contributor_submission_id', 'id');
    }
}
