<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorVectorSubmissionItem extends Model
{
    protected $table = 'contributor_vector_submission_items';
    protected $fillable = ['contributor_submission_id', 'vector_id'];
    public $timestamps = false;

    public function file()
    {
        return $this->belongsTo(ContributorVector::class, 'vector_id', 'id');
    }

    public function submmission()
    {
        return $this->belongsTo(ContributorVectorSubmission::class, 'contributor_submission_id', 'id');
    }
}
