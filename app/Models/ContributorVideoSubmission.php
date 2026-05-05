<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorVideoSubmission extends Model
{
    use SoftDeletes;

    protected $table = "contributor_video_submissions";
    protected $fillable = ['contributor_id', 'type', 'status'];

    public function items()
    {
        return $this->hasMany(ContributorVideoSubmissionItem::class, 'contributor_submission_id', 'id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }
}
