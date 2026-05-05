<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VectorTagContributor extends Model
{
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'vectors_tags_contributor';
    protected $fillable = [
        'vector_id',
        'tag',
        'local',
        'confidence',
        'slug'
    ];


    public function vectors()
    {
        return $this->belongsTo(ContributorVector::class, 'vector_id', 'id');
    }
}
