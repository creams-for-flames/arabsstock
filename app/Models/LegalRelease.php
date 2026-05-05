<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalRelease extends Model
{
    protected $fillable = ['contributor_id', 'name', 'type', 'file', 'ethnicity', 'age', 'gender'];
    protected $table = 'legal_releases';
}
