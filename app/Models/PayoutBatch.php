<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model,SoftDeletes};


class PayoutBatch extends Model
{
    use SoftDeletes;
    protected $table="payout_batch";
    protected $fillable = ['sender_batch_id','email_subject','email_message','payout_batch_id','data'];

    public function payoutItem() {
        return $this->hasMany(PayoutItem::class,'payout_batch_tbl_id');
    }
}
