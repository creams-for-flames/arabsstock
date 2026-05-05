<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stripe\Plan;

class Purchase extends Model
{
    protected $fillable = [
        'purchaseable_id',
        'purchaseable_type',
        'user_id',
        'contributor_id',
        'download_id',
        'plan_id',
        'plan_price',
        'unit_price',
        'profit_ratio',
        'profit_value',
    ];
    protected $table = "purchases";

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function download()
    {
        return $this->belongsTo(Download::class);
    }

    public function purchaseable()
    {
        return $this->morphTo('purchaseable');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'purchase_id');
    }

    public function vector()
    {
        return $this->belongsTo(Vector::class, 'purchase_id');
    }

    public function video()
    {
        return $this->belongsTo(Video::class, 'purchase_id');
    }

    public function image_plan()
    {
        return $this->belongsTo(ImagePlan::class, 'plan_id');
    }

    public function video_plan()
    {
        return $this->belongsTo(VideoPlan::class, 'plan_id');
    }

    public function vector_plan()
    {
        return $this->belongsTo(VectorPlan::class, 'plan_id');
    }

    public function contributor()
    {
        return $this->belongsTo(Contributor::class);
    }

    public function account_ledger()
    {
        return $this->morphOne(AccountLedger::class, 'accountable');
    }

    public function withdrawals()
    {
        return $this->belongsToMany(Withdraw::class)->withPivot('value', 'complete');
    }
}
