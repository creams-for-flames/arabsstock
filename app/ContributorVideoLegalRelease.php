<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContributorVideoLegalRelease extends Model
{
    protected $fillable = ['release_id', 'video_id'];
    protected $with = ['release'];
    protected $table = 'contributor_video_legal_release';

    public function release()
    {
        return $this->belongsTo(LegalRelease::class, 'release_id');
    }
}
