<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ImageFolder extends Model
{
    protected $fillable = ['folder','country_id','city_id','session_date','notes'];

    public function images()
    {
        return $this->hasMany(Image::class,'folder_id');
    }

    /**
     * The roles that belong to the ImageFolder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function actors(): BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_image_folder', 'folder_id', 'actor_id')
        ->withPivot('contract')
        ->withTimestamps();
    }

    /**
     * The roles that belong to the ImageFolder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photographers(): BelongsToMany
    {
        return $this->belongsToMany(Photographer::class, 'photographer_image_folder', 'folder_id', 'photographer_id')
        ->withPivot(['contract','contract_file','id'])
        ->withTimestamps();
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(SessionLocation::class, 'location_image_folder', 'folder_id', 'session_location_id')
        ->withPivot('contract')
        ;
    }

    public function invoices()
    {
        return $this->morphMany(SessionInvoice::class, 'invoiceable');
    }

}
