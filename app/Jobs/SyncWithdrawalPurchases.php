<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncWithdrawalPurchases implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $withdraw;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($withdraw)
    {
        $this->withdraw = $withdraw;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $value_withdraw = $this->withdraw->value_withdraw;
        foreach ($this->withdraw->contributor->purchases()->whereDoesntHave('withdrawals', function ($q) {
            $q->where('purchase_withdraw.complete', 1);
        })->get() as $purchase) {
            if (!$value_withdraw)
                break;
            $purchase_value = $purchase->profit_value + 0;
            if (optional($purchase->pivot)->value)
                $purchase_value = $purchase->profit_value - $purchase->pivot->value;
            if ($value_withdraw >= $purchase_value) {
                $this->withdraw->purchases()->attach($purchase->id, ['value' => $purchase_value, 'complete' => 1]);
                $value_withdraw -= $purchase_value;
            } else {
                $purchase_value = $value_withdraw;
                $this->withdraw->purchases()->attach($purchase->id, ['value' => $purchase_value, 'complete' => 0]);
                $value_withdraw -= $purchase_value;
            }
        }
    }
}
