<?php

namespace App\Models;

use App\Models\Video;
use App\Models\Contributor;
use App\VideoTagContributor;
use App\Models\CategoryContributor;
use App\Models\ContributorVideoLegalRelease;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ContributorVideo extends Model
{
    use SoftDeletes;
    public function user()
    {
        return $this->belongsTo(Contributor::class, 'contributor_id', 'id');
    }
    public function tags()
    {
        return $this->hasMany(VideoTagContributor::class, 'video_id');
    }

    public function category()
    {
        return $this->belongsToMany(CategoryContributor::class, 'category_contributor_video', 'video_id', 'category_id');
    }

    public function release_video()
    {
        return $this->hasMany(ContributorVideoLegalRelease::class, 'video_id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class, 'contributor_id', 'id');
    }

    public function file()
    {
        return $this->hasOne(Video::class, 'contributor_video_id', 'id');
    }

    public function submmission_item()
    {
        return $this->hasOne(ContributorVideoSubmissionItem::class, 'video_id', 'id');
    }

    public function getThumbnailAttribute($val)
    {
        return cdn($val);
    }

    public function raw()
    {
        return $this->hasOne(ContributorRawVideo::class, 'contributor_video_id', 'id');
    }

    public function match()
    {
        return $this->hasOne(ContributorRawVideoMatch::class, 'contributor_video_id', 'id');

    }
}
