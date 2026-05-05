<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchImage extends Model
{
    public function getRouteKey()
    {
        return 'hash';
    }

    protected $fillable = [
        'user_id',
        'path',
        'file_name',
        'size',
        'hash',
        'ip',
    ];
}
