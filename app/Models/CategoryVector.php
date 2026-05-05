<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryVector extends Model
{

    protected $table = 'category_vector';
    protected $guarded = array();
    public $timestamps = false;
    protected $fillable = [
        'vector_id',
        'category_id',

    ];

    public function vector()
    {
        return $this->belongsTo('App\Models\Vector');
    }

    public function category()
    {
        return $this->belongsTo(VectorCategory::class);
    }

}
