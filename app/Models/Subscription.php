<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_type',
        'city_id',
        'country_id',
        'plan_id',
        'payment_method_id',
        'subscription_id',
        'quantity',
        'created_by_hook',
        'remaining_credits',
        'credits',
        'credit_price',
        'plan_type',
        'currency',
        'amount',
        'plan_price',
        'payment_gateway_fee',
        'payment_id',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'status',
        'completed',
        'paid',
        'renewal',
        'invoice_file',
        'data',
        'start_period',
        'promocode_id',
        'card_fingerprint',
        'ip',
    ];

    protected $appends = [
        'is_active',
        'title',
    ];

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CANCEL = 2;
    const STATUS_REFUND = 3;

    public function user()
    {
        return $this->morphTo('user');
    }

    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    public function city()
    {
        return $this->belongsTo(Cities::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function RemainingDays()
    {
        $ends_at = Carbon::parse($this->ends_at);
        $interval = now()->diffInDays($ends_at, false);
        return ($ends_at >= now() && $interval > 0) ? $interval : 0;
    }

    public function isActive()
    {
        return (Carbon::parse($this->ends_at)->gt(now())) && $this->remaining_credits && in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_CANCEL]);
    }

    public function getIsActiveAttribute()
    {
        return $this->isActive();
    }

    public function isPending()
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCEL;
    }

    public function scopeActive($q)
    {
        if (!auth()->check())
            return $q;
        return $q->where([
            ['subscriptions.remaining_credits', '>', 0],
            ['subscriptions.user_id', '=', auth()->id()],
            ['subscriptions.ends_at', '>=', date('Y-m-d H:i:s')],
        ])->where(function ($q) {
            $q->where(function ($q) {
                $q->whereIn('subscriptions.status', [self::STATUS_ACTIVE, self::STATUS_CANCEL])->whereIn('subscriptions.plan_type', ['monthly', 'annual']);
            })->orWhere(function ($q) {
                $q->whereIn('subscriptions.status', [self::STATUS_ACTIVE])->where('subscriptions.plan_type', 'package');
            });
        });
    }

    public static function download($record, $license = 'standard', $team = false)
    {
        if ($team && $license == 'standard')
            $license = 'enhanced';
        $user = auth()->user();
        $subscriptions = self::need_to_download($record, $license, $team);
        if (!$subscriptions->count())
            return false;
        $contributor = $record->user_type == Contributor::class ? $record->user : null;
        if ($license == 'exclusive') {
            $record->reserved_to = $user->id;
            $record->reserved_until = now()->addYears(2);
            $record->save();
        }
        $download = new Download();
        $download->entity_type = get_class($record);
        $download->entity_id = $record->id;
        $download->credits = $download->entity_type::{"{$license}_credits"}();
        if (\request('removebg')) {
            $download->additional_credits = 1;
            $download->additional_credits_reason = 'removebg';
            $download->removebg = 1;
        }
        if (\request('raw') && $record->has_raw()) {
            $download->additional_credits = 30;
            $download->additional_credits_reason = 'raw';
            $download->raw = 1;
        }
        $download->license_type = $license;
        $download->user_id = $user->id;
        $download->team_id = $team && $user->team_id ? $user->team_id : null;
        $download->ip = request()->ip();
        $download->section_const_id = $download->entity_type::section_const()->id;
        $download->date = now();
        $download->save();
        $download->subscriptions()->sync($subscriptions->toArray());
        if ($team)
            $user_subscriptions = auth()->user()->active_team_subscriptions()->with('plan')->get()->keyBy('id');
        else
            $user_subscriptions = auth()->user()->active_subscriptions()->with('plan')->get()->keyBy('id');
        $profit_value = 0;
        $unit_price = 0;
        foreach ($subscriptions as $subscription_id => $r) {
            $subscription = $user_subscriptions->get($subscription_id);
            $credit_cost = $subscription->credit_price;
            $unit_price += $credit_cost * $r['credits'];
            $subscription->remaining_credits = $subscription->remaining_credits - $r['credits'];
            $subscription->save();
            if ($team) {
                $user->team_subscriptions()->updateExistingPivot($subscription, [
                    'remaining_credits' => $subscription->pivot->remaining_credits - $r['credits'],
                ]);
            }
        }
        $download->unit_price = $unit_price;
        $download->save();

        if ($contributor && $contributor->profit_ratio) {

            if (in_array($download->additional_credits_reason, ['removebg'])) {
                $credit_price = $unit_price / ($download->credits + $download->additional_credits);
                $original_credits_cost = $credit_price * $download->credits;
                $profit_value = $original_credits_cost * ($contributor->profit_ratio / 100);
            } else
                $profit_value = $unit_price * ($contributor->profit_ratio / 100);
            if ($profit_value) {
                $purchase = new Purchase();
                $purchase->user_id = auth()->id();
                $purchase->contributor_id = $contributor->id;
                $purchase->download_id = $download->id;
                $purchase->unit_price = $unit_price;
                $purchase->profit_ratio = $contributor->profit_ratio;
                $purchase->profit_value = $profit_value;
                $purchase->purchaseable_id = $record->id;
                $purchase->purchaseable_type = get_class($record);
                $purchase->save();

                // save to account ledger
                $account_ledger = new AccountLedger();
                $account_ledger->proccess = "pay";
                $account_ledger->value = $profit_value;
                $account_ledger->contributor_id = $contributor->id;
                $account_ledger->accountable_id = $purchase->id;
                $account_ledger->accountable_type = Purchase::class;
                $account_ledger->save();
            }

        }
        return true;
    }

    public static function need_to_download($record, $license = 'standard', $team = false)
    {
        if (!auth()->check())
            return collect([]);
        $user = auth()->user();
        if ($team) {
            $subscriptions = $user->active_team_subscriptions()->get();
            $class = get_class($record);
            $method = "{$license}_credits";
            $needed_credits = $class::{$method}();
            if (request('removebg'))
                $needed_credits += 1;
            if (request('raw') && $record->has_raw())
                $needed_credits += 30;
            $used_subscriptions = [];
            if ($subscriptions->sum('pivot.remaining_credits') < $needed_credits)
                return collect([]);
            foreach ($subscriptions as $r) {
                if ($r->pivot->remaining_credits >= $needed_credits) {
                    $used_subscriptions[$r->id] = ['credits' => $needed_credits, 'created_at' => now()];
                    break;
                } else {
                    $used_subscriptions[$r->id] = ['credits' => ($r->pivot->remaining_credits), 'created_at' => now()];
                    $needed_credits -= $r->pivot->remaining_credits;
                }
                if ($needed_credits <= 0)
                    break;
            }
            return collect($used_subscriptions);
        } else {
            $subscriptions = $user->active_subscriptions()->get();
            $class = get_class($record);
            $method = "{$license}_credits";
            $needed_credits = $class::{$method}();
            if (request('removebg'))
                $needed_credits += 1;
            if (request('raw') && $record->has_raw())
                $needed_credits += 30;
            $used_subscriptions = [];
            if ($subscriptions->sum('remaining_credits') < $needed_credits)
                return collect([]);
            foreach ($subscriptions as $r) {
                if ($r->remaining_credits >= $needed_credits) {
                    $used_subscriptions[$r->id] = ['credits' => $needed_credits, 'created_at' => now()];
                    break;
                } else {
                    $used_subscriptions[$r->id] = ['credits' => ($r->remaining_credits), 'created_at' => now()];
                    $needed_credits -= $r->remaining_credits;
                }
                if ($needed_credits <= 0)
                    break;
            }
            return collect($used_subscriptions);
        }
    }


    public function downloads()
    {
        return $this->belongsToMany(Download::class)->withPivot('credits', 'created_at');
    }

    public function promocode()
    {
        return $this->belongsTo(Promocode::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('completed', function (Builder $builder) {
            $builder->where('completed', 1);
        });
    }

    public function getFinishedAtAttribute()
    {
        if ($this->plan_type == 'annual')
            return Carbon::parse($this->starts_at)->addYear();
        return Carbon::parse($this->ends_at);
    }

    public function renewals()
    {
        return $this->hasMany(SubscriptionRenewal::class);
    }

    public function getTitleAttribute()
    {
        if ($this->plan->on_demand) {
            return __(':credits credits (custom)', ['credits' => $this->credits]);
        }
        return $this->plan->description;
    }
}
