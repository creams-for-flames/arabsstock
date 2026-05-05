<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'message',
        'type',
        'entity',
        'seen_at',
        'ip',
    ];

}
