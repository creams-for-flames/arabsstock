<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContributorVectorLegalRelease extends Model
{
    protected $fillable = ['release_id', 'vector_id'];
    protected $with = ['release'];
    protected $table = 'contributor_vector_legal_release';

    public function release()
    {
        return $this->belongsTo(LegalRelease::class, 'release_id');
    }
}
