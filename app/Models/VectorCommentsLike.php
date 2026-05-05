<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VectorCommentsLike extends Model
{

    protected $guarded = array();
    public $timestamps = false;
    protected $table = 'comments_vector_likes';
    protected $fillable = ['user_id', 'comment_id', 'status'];

    public function user()
    {
        return $this->belongsTo('App\Models\User')->first();
    }
}
