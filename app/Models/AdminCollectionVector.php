<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class AdminCollectionVector extends Model
{
    protected $fillable = ['vector_id', 'admin_collection_id'];
    protected $table = 'admin_collection_vectors';

    public function collection()
    {
        return $this->belongsTo(AdminCollection::class, 'admin_collection_id');
    }
 
    public function vectors()
    {
        return $this->belongsTo(Vectors::class, 'vector_id');
    }
}
