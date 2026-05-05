<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PayoutItem;
use App\Models\Withdraw;
use App\Models\AccountLedger;

class CheckPayoutItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $items = PayoutItem::where('transaction_status','!=','SUCCESS')->where('payout_batch_status','SUCCESS')->get();
        foreach ($items as $item) {
            $response = \App\Contexts\PayPal::show_payout_item_details($item->payout_item_id);
            $item->transaction_status = $response->result->transaction_status;
            $item->withdraw_status = $response->result->transaction_status;
            $item->save();

            $withdraw =Withdraw::find($payout_item->withdraw_id);
            if($withdraw){
                $withdraw->status_desc_payout= $response->result->transaction_status ;
            }
            if($response->result->transaction_status =="SUCCESS"){

                $withdraw->status_payout = 1;
                $account_ledger = new AccountLedger();
                $account_ledger->proccess = "withdraw";
                $account_ledger->value = -($withdraw->value_withdraw);
                $account_ledger->contributor_id = $withdraw->contributor_id;
                $account_ledger->accountable_id = $withdraw->id;
                $account_ledger->accountable_type = Withdraw::class;
                $account_ledger->save();
            }
            $withdraw->save();
        }
    }
}
