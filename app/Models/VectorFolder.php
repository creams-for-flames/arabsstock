<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VectorFolder extends Model
{
    protected $fillable = ['folder'];

    public function vectors()
    {
        return $this->hasMany(Vector::class, 'folder_id');
    }

}
