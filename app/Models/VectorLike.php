<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VectorLike extends Model
{

    protected $guarded = array();
    public $timestamps = false;
    protected $table = 'vector_likes';

    public function user()
    {
        return $this->belongsTo(User::class)->first();
    }

    public function vectors()
    {
        return $this->hasMany(Vector::class);
    }
}
