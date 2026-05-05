<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoDownload extends Model
{

    protected $fillable = array('user_id', 'video_id', 'order_id', 'child_id', 'type', 'plan_id', 'subscription_id');
    protected $guarded = array();
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class)->first();
    }

    public function images()
    {
        return $this->belongsTo(Video::class)->first();
    }


    public function video()
    {
        return $this->belongsTo(Video::class)->withoutGlobalScope('reserved');
    }


    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function image()
    {
        return $this->belongsTo(Video::class, 'video_id')->withTrashed()->withoutGlobalScopes();
    }

    public function plan()
    {
        return $this->belongsTo(VideoPlan::class);
    }

    public function subscription()
    {
        return $this->belongsTo(VideoSubscription::class);
    }


}
