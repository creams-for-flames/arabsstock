<?php

namespace App;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

class DownloadSubscription extends Model
{
    protected $table = 'download_subscription';

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
