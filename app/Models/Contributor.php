<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contributor extends Model
{
    protected $table = "contributors";

    protected $fillable = [
        'username', 'name', 'bio', 'country_id', 'city_id', 'email', 'mobile', 'email_verified_at', 'password', 'avatar', 'twitter', 'website', 'status', 'paypal_account', 'profit_ratio', 'total_amount', 'api_token', 'show_land_images', 'show_land_vectors', 'show_land_videos'
    ];

    /**
     * Get all of the comments for the Contributor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(ContributorImage::class, 'contributor_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(ContributorVideo::class, 'contributor_id', 'id');
    }

    public function vectors()
    {
        return $this->hasMany(ContributorVector::class, 'contributor_id', 'id');
    }


    public function account_ledgers()
    {
        return $this->hasMany(AccountLedger::class);
    }

    public function bounced_emails()
    {
        return $this->hasMany(BouncedEmail::class, 'email', 'email');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdraw::class);
    }


    public function created_images()
    {
        return $this->morphMany(Image::class, 'user');
    }

    public function created_videos()
    {
        return $this->morphMany(Video::class, 'user');
    }

    public function created_vectors()
    {
        return $this->morphMany(Vector::class, 'user');
    }

    public function getAvatarAttribute($val)
    {
        return asset('avatar/default.jpg');
    }
}
