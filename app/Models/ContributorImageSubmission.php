<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorImageSubmission extends Model
{
    use SoftDeletes;

    protected $table = "contributor_image_submissions";

    protected $fillable = ['contributor_id', 'type', 'status'];

    public function items()
    {
        return $this->hasMany(ContributorImageSubmissionItem::class, 'contributor_submission_id', 'id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }
}
