<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitVideo extends Model
{

    protected $guarded = array();
    public $timestamps = false;
    protected $table = 'visit_videos';
    protected $fillable = ['video_id', 'user_id', 'ip', 'date'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function videos()
    {
        return $this->belongsTo('App\Models\Video', 'video_id');
    }

}
