<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoSearchKey extends Model
{
    use SoftDeletes;

    protected $table = 'video_search_keys';

    protected $fillable = ['key_word', 'count', 'lang'];


}
