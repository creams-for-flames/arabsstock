<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionVideo extends Model
{


    protected $guarded = array();
    public $timestamps = false;
    protected $appends = ['user'];

    protected $table = 'collection_video';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserAttribute()
    {
        $user = User::find($this->user_id);
        if ($user) {
            return $user;
        } else {
            return null;
        }
    }

    public function videos()
    {
        return $this->belongsTo(Video::class)->where('status', 'active')->orderBy('id', 'desc')->first();
    }

    public function collection()
    {
        return $this->belongsTo(VideoCollection::class)->first();
    }

    public function belongsCollection()
    {
        return $this->belongsTo(VideoCollection::class, 'collection_id')->orderBy('id', 'desc');
    }

    public function collections()
    {
        return $this->hasMany(VideoCollection::class)->orderBy('id', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(VideoLike::class, 'video_id', 'video_id')->where('status', '1');
    }

    public function downloads()
    {
        return $this->hasMany(DownloadsVideo::class, 'video_id', 'video_id');
    }

}
