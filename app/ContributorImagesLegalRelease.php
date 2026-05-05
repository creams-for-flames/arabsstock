<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContributorImagesLegalRelease extends Model
{
    protected $fillable = ['release_id', 'image_id'];
    protected $with = ['release'];
    protected $table = 'contributor_image_legal_release';

    public function release()
    {
        return $this->belongsTo(LegalRelease::class, 'release_id');
    }
}
