<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageSearchKey extends Model
{
    use SoftDeletes;

    protected $table = 'image_search_keys';

    protected $fillable = ['key_word', 'count', 'lang'];


}
