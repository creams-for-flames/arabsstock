<?php

namespace App\Models;

use App\Models\Vector;
use App\Models\VectorTagContributor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ContributorVector extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $fillable = [
        'title_en',
        'title_ar',
        'thumbnail',
        'contributor_id',
        'original_name',
        'contributor_stage',
        'file_hash',
        'large',
        'review_notes',
    ];
    public function user()
    {
        return $this->belongsTo(Contributor::class, 'contributor_id', 'id');
    }
    public function tags()
    {
        return $this->hasMany(VectorTagContributor::class, 'vector_id');
    }

    public function category()
    {
        return $this->belongsToMany(CategoryContributor::class, 'category_contributor_vector', 'vector_id', 'category_id');
    }

    // public function release_vector()
    // {
    //     return $this->hasMany(ContributorVectorsLegalRelease::class, 'vector_id');
    // }

    public function file()
    {
        return $this->hasOne(Vector::class, 'contributor_vector_id','id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class, 'contributor_id', 'id');
    }

    public function submmission_item()
    {
        return $this->hasOne(ContributorVectorSubmissionItem::class, 'vector_id', 'id');
    }
}
