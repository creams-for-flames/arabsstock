<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorVideoSubmissionItem extends Model
{
    protected $table = 'contributor_video_submission_items';
    protected $fillable = ['contributor_submission_id', 'video_id'];
    public $timestamps = false;

    public function file()
    {
        return $this->belongsTo(ContributorVideo::class, 'video_id', 'id');
    }

    public function submmission()
    {
        return $this->belongsTo(ContributorVideoSubmission::class, 'contributor_submission_id', 'id');
    }
}
