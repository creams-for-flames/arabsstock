<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'leader_id',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'user');
    }

    public function member_subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'team_user_subscription')->withPivot('credits', 'remaining_credits');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }
}
