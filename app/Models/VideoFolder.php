<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VideoFolder extends Model
{
    protected $fillable = ['folder','country_id','city_id','session_date','notes'];

    public function videos()
    {
        return $this->hasMany(Video::class, 'folder_id');
    }
    /**
     * The roles that belong to the VideoFolder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_video_folder', 'folder_id', 'actor_id')
        ->withPivot('contract')
        ->withTimestamps();
    }

    /**
     * The roles that belong to the VideoFolder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photographers(): BelongsToMany
    {
        return $this->belongsToMany(Photographer::class, 'photographer_video_folder', 'folder_id', 'photographer_id')
        ->withPivot('contract')
        ->withTimestamps();
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(SessionLocation::class, 'location_video_folder', 'folder_id', 'session_location_id');
    }

    public function invoices()
    {
        return $this->morphMany(SessionInvoice::class, 'invoiceable');
    }
}
