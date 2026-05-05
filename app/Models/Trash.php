<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App
 * @property string $title
 */
class Trash extends Model
{
    public function content()
    {
        return $this->morphTo('content');
    }

    public function deleter()
    {
        return $this->morphTo('deleter');
    }

}
