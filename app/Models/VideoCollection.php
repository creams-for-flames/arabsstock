<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class VideoCollection extends Model
{

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collection_videos()
    {
        return $this->hasMany(CollectionVideo::class, 'collection_id', 'id')->orderBy('id', 'desc');
    }

}
