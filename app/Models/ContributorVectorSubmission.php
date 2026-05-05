<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorVectorSubmission extends Model
{
    use SoftDeletes;

    protected $table = "contributor_vector_submissions";
    protected $fillable = ['contributor_id', 'type', 'status'];

    public function items()
    {
        return $this->hasMany(ContributorVectorSubmissionItem::class,'contributor_submission_id','id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }
}
