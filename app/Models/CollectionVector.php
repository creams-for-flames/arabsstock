<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionVector extends Model
{
    protected $guarded = array();
    public $timestamps = false;

    protected $table = 'collection_vector';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vectors()
    {
        return $this->belongsTo(Vector::class)->where('status', 'active')->orderBy('id', 'desc')->first();
    }

    public function collection()
    {
        return $this->belongsTo(VectorCollection::class);
    }

    public function belongsCollection()
    {
        return $this->belongsTo(VectorCollection::class, 'collection_id')->orderBy('id', 'desc');
    }

    public function collections()
    {
        return $this->hasMany(VectorCollection::class)->orderBy('id', 'desc');
    }

    public function likes()
    {
        return $this->hasMany(VectorLike::class, 'vector_id', 'vector_id')->where('status', '1');
    }

    public function downloads()
    {
        return $this->hasMany(VectorDownload::class, 'vector_id', 'vector_id');
    }

}
