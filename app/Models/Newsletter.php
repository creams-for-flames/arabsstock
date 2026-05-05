<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'newsletter';
    protected $fillable = [
        'subject',
        'from_name',
        'from_email',
        'html',
        'status',
        'sent',
        'receivers',
        'specific_users',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'newsletter_user');
    }
}
