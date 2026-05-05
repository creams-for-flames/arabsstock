<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContributorRawVideoMatch extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Get the user that owns the ContributorRawVideoMatch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contributor_raw_video(): BelongsTo
    {
        return $this->belongsTo(ContributorRawVideo::class, 'contributor_raw_video_id', 'id');
    }
    public function contributor_video(): BelongsTo
    {
        return $this->belongsTo(ContributorVideo::class, 'contributor_video_id', 'id');
    }
}
