<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VisibleScope;

class UserVideoCollection extends Model {




	public    $timestamps = false;


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
   /* protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new VisibleScope);
    }
	*/
	public function user() {
        return $this->belongsTo('App\Models\User')->first();
    }

	public function collection_Video() {
        return $this->hasMany('App\Models\CollectionsVideo')->orderBy('id','desc');
    }

    public function getFirstImageAttribute() {
        return $this->hasMany('App\Models\CollectionsVideo')->first();
    }

    public function collection_videos() {
        return $this->hasMany(CollectionVideo::class,'collection_id','id')->orderBy('id','desc');
    }
    public function getFirstVideoAttribute() {
        return $this->hasMany('App\Models\CollectionVideo')->first();
    }

}
