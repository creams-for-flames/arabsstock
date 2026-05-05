<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSubscribe extends Model
{

    protected $fillable = ['user_id', 'email'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
