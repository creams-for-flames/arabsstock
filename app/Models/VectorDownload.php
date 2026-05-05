<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VectorDownload extends Model
{
    use SoftDeletes;

    protected $fillable = array('user_id', 'vector_id', 'order_id');
    protected $guarded = array();
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class)->first();
    }

    public function vectors()
    {
        return $this->belongsTo(Vector::class)->first();
    }

    public function vector()
    {
        return $this->belongsTo(Vector::class)->withoutGlobalScope('reserved');
    }


    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function image()
    {
        return $this->belongsTo(Vector::class, 'vector_id')->withTrashed()->withoutGlobalScopes();
    }

    public function plan()
    {
        return $this->belongsTo(VectorPlan::class);
    }

    public function subscription()
    {
        return $this->belongsTo(VectorSubscription::class);
    }



    public function downloads()
    {
        return $this->hasMany(VectorDownload::class, 'subscription_id');
    }
}
