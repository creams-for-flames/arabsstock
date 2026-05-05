<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorRawVideo extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    public function contributor_file()
    {
        return $this->belongsTo(ContributorVideo::class, 'contributor_video_id', 'id');
    }

    /**
     * Get all of the comments for the ContributorRawVideo
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matches(): HasMany
    {
        return $this->hasMany(ContributorRawVideoMatch::class, 'contributor_raw_video_id', 'id');
    }
}
