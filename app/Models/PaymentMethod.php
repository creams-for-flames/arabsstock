<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentMethod
 *
 * @package App
 * @property string $title_en
 * @property text $description_en
 * @property string $title_ar
 * @property text $description_ar
 * @property string $image
 * @property string $link
 * @property string $email
 * @property string $key
 * @property string $status
*/
class PaymentMethod extends Model
{
    use SoftDeletes;
    const PAYPAL = 2;
    const STRIPE = 5;

    const BANK = 6;
    const FREE = 4;
    protected $fillable = ['title_en', 'description_en', 'title_ar', 'description_ar', 'image', 'link', 'email', 'key', 'status'];
    protected $hidden = [];



}
