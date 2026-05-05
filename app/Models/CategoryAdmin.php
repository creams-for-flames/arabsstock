<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryAdmin extends Model
{
    use SoftDeletes;
    protected $guarded = [];


    protected $fillable = ['name', 'slug'];

    /*	public function images() {
            return $this->hasMany(Image::class,'id','image_id')->where('status','active');
        }*/
    public function images()
    {
        return $this->hasMany(Image::class, 'category_admin_id');
    }

}
