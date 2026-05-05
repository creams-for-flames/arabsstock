<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class AdminCollectionVideo extends Model
{
    protected $fillable = ['video_id', 'admin_collection_id'];
    protected $table = 'admin_collection_videos';

    public function collection()
    {
        return $this->belongsTo(AdminCollection::class, 'admin_collection_id');
    }

    public function video()
    {
        return $this->belongsTo(Images::class, 'video_id');
    }
}
