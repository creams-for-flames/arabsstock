<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VectorSearchKey extends Model
{
    use SoftDeletes;

    protected $table = 'vector_search_keys';

    protected $fillable = ['key_word', 'count', 'lang'];


}
