<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class AdminCollectionImage extends Model {



    protected $fillable=['image_id','admin_collection_id'];
    protected $table='admin_collection_images';
	




    public function collection()
    {
        return $this->belongsTo(AdminCollection::class,'admin_collection_id');
    }

    public function image()
    {
        return $this->belongsTo(Images::class,'image_id');
    }
    /**
     * The "booting" method of the model.
     *
     * @return void
     */

	


}