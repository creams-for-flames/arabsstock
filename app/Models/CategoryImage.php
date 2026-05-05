<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryImage extends Model
{
    protected $table = 'category_image';
    protected $guarded = array();
    public $timestamps = false;
    protected $fillable = [
        'image_id',
        'category_id',

    ];

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function category()
    {
        return $this->belongsTo(ImageCategory::class);
    }
}
