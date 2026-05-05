<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\PayoutBatch;
use App\Models\PayoutItem;
use App\Models\Withdraw;
use App\Models\AccountLedger;
class CheckPayoutBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payout_batchs = PayoutBatch::where('payout_batch_status','PENDING')->orWhere('payout_batch_status','PROCESSING')->get();
        $count_pending=count($payout_batchs);
        foreach ($payout_batchs as $payout_batch) {
            $payout_items = PayoutItem::where('payout_batch_tbl_id',$payout_batch->id)->get();
            foreach ($payout_items as $payout_item) {
                $payout_item->payout_item_id = 0;
                $payout_item->transaction_id = '';
                $payout_item->transaction_status = "SUCCESS";
                $payout_item->payout_item_fee = "0";
                $payout_item->withdraw_status = "SUCCESS";
                $payout_item->payout_batch_status = "SUCCESS" ;
                $payout_item->save();

                $withdraw =Withdraw::find($payout_item->withdraw_id);
                $withdraw->status_desc_payout =  "SUCCESS";
                $withdraw->status_payout = 1;
                $withdraw->save();

                $account_ledger = new AccountLedger();
                $account_ledger->proccess = "withdraw";
                $account_ledger->value = -($withdraw->value_withdraw);
                $account_ledger->contributor_id = $withdraw->contributor_id;
                $account_ledger->accountable_id = $withdraw->id;
                $account_ledger->accountable_type = Withdraw::class;
                $account_ledger->save();
            }
            $payout_batch->save();
        }
        // foreach ($payout_batchs as $payout_batch) {
        //      $response = \App\Contexts\PayPal::show_payout_details($payout_batch->payout_batch_id);
        //      $items=$response->result->items;
        //      $batch_status=$response->result->batch_header->batch_status;

        //      $payout_batch->payout_batch_status = $batch_status;

        //      if($batch_status=="SUCCESS"){
        //         $count_pending--;
        //     }

        //     $payout_items = PayoutItem::where('payout_batch_tbl_id',$payout_batch->id)->get();
        //      foreach ($payout_items as $payout_item) {
        //          foreach ($items as $item) {
        //              if($item->payout_item->sender_item_id == $payout_item->withdraw_id){
        //                  $payout_item->payout_item_id = $item->payout_item_id;
        //                  $payout_item->transaction_id = $item->transaction_id??'';
        //                  $payout_item->transaction_status = $item->transaction_status;
        //                  $payout_item->payout_item_fee = $item->payout_item_fee->value ;
        //                  $payout_item->withdraw_status = $item->transaction_status ;
        //                  $payout_item->payout_batch_status = $batch_status ;
        //                  // $payout_item->save();

        //                  $withdraw =Withdraw::find($payout_item->withdraw_id);
        //                  $withdraw->status_desc_payout =  $item->transaction_status;
        //                  $withdraw->status_payout = 0;
        //                  if($item->transaction_status == "SUCCESS"){
        //                      $withdraw->status_payout = 1;

        //                      $account_ledger = new AccountLedger();
        //                      $account_ledger->proccess = "withdraw";
        //                      $account_ledger->value = -($withdraw->value_withdraw);
        //                      $account_ledger->contributor_id = $withdraw->contributor_id;
        //                      $account_ledger->accountable_id = $withdraw->id;
        //                      $account_ledger->accountable_type = Withdraw::class;
        //                      $account_ledger->save();
        //                  }
        //                  elseif ($item->transaction_status == "FAILED" || $item->transaction_status == "RETURNED" || $item->transaction_status == "BLOCKED" || $item->transaction_status == "REFUNDED" || $item->transaction_status == "REVERSED") {
        //                      $withdraw->can_payout = 1;
        //                      $withdraw->status_payout = 0;
        //                  }
        //                  $withdraw->save();
        //                  $payout_item->save();
        //              }
        //          }
        //      }

        //      $payout_batch->save();

        //      if($count_pending>0){
        //             //ToDo :: disbatch every 5 min;
        //      }

        // }
    }
}
