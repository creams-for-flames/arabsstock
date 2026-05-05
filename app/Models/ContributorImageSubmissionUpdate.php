<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorImageSubmissionUpdate extends Model
{
    protected $fillable = [
        'file',
        'contributor_id',
        'status',
    ];

    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }
}
