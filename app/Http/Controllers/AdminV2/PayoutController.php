<?php

namespace App\Http\Controllers\AdminV2;

use App\Export\ContributorsStatisticsExport;
use App\Export\WithdrawExport;
use App\Models\Image;
use App\Models\Vector;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Withdraw;
use App\Models\Contributor;
use App\Models\PayoutItem;
use App\Models\PayoutBatch;
use App\Models\AccountLedger;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $index_url = route('admin.payout.datatable');
        $edit_url = route('admin.payout.edit', 0);
        $destroy_url = route('admin.payout.destroy', 0);

        $object = new Withdraw();

        $html_breadcrumbs = [
            'title' => __('views.Withdraws'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.payout.create');
        $is_request=0;
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views._payout'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.payout.payout', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Cancel_all'),
                'url' => route('admin.payout.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.payout.index_payout',
            compact('html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url','is_request')
        );
    }

    public function payout_request()
    {
        $index_url = route('admin.payout.datatable');
        $edit_url = route('admin.payout.edit', 0);
        $destroy_url = route('admin.payout.destroy', 0);

        $object = new Withdraw();

        $html_breadcrumbs = [
            'title' => __('views.Withdraws'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $is_request = 1;
        $html_new_path = route('admin.payout.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views._payout'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.payout.payout', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Cancel_all'),
                'url' => route('admin.payout.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];
        return view(
            'admin_v2.payout.index',
            compact('html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url','is_request')
        );
    }

    public function datatable(Request $request)
    {
        $query = Withdraw::with(['contributor' => function ($q) {
            $q->withCount(['account_ledgers as current_profit' => function ($query) {
                $query->select(DB::raw('sum(value)'));
            }]);
        }]);

        if (isset($request->is_request) && $request->is_request == 1)
            $query->where('status_desc_payout', 'WAITING');
        else
            $query->where('status_desc_payout', '!=', 'WAITING');

        $search = $request->input('query.generalSearch');
        if ($search)
            $query->where(function ($query) use ($search) {
                $query->orderBy('id', 'desc');
                $query->whereHas('contributor', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            });
        if ($request->input('query.contributor_id'))
            $query->whereHas('contributor', function ($q) {
                $q->where('contributors.id', request('query.contributor_id'));
            });
        $datatable_params = get_datatable_params(request()->all());
        if (isset($datatable_params['date_range'][1]) && isset($datatable_params['date_range'][0])) {
            $from = date('Y-m-d', strtotime($datatable_params['date_range'][0]));
            $to = date('Y-m-d', strtotime($datatable_params['date_range'][1]));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }
        $data = process_datatable_query($query);
        return $data;
    }

    public function export_payout(Request $request)
    {
        $query = Withdraw::with(['contributor' => function ($q) {
            $q->withCount(['account_ledgers as current_profit' => function ($query) {
                $query->select(DB::raw('sum(value)'));
            }]);
        }])->withCount([
            'purchases as images_purchases' => function ($q) {
                $q->select(DB::raw('sum(purchase_withdraw.value)'))->where('purchaseable_type', Image::class);
            },
            'purchases as videos_purchases' => function ($q) {
                $q->select(DB::raw('sum(purchase_withdraw.value)'))->where('purchaseable_type', Video::class);
            },
            'purchases as vectors_purchases' => function ($q) {
                $q->select(DB::raw('sum(purchase_withdraw.value)'))->where('purchaseable_type', Vector::class);
            },
        ])->where('status_desc_payout', 'SUCCESS');

        $search = $request->input('generalSearch');
        if ($search)
            $query->where(function ($query) use ($search) {
                $query->orderBy('id', 'desc');
                $query->whereHas('contributor', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                });
            });
        if ($request->input('contributor_id'))
            $query->whereHas('contributor', function ($q) {
                $q->where('contributors.id', request('contributor_id'));
            });
        $datatable_params = request()->all();
        if ($request->date_from && $request->date_to) {
            $from = date('Y-m-d', strtotime($request->date_from));
            $to = date('Y-m-d', strtotime($request->date_to));
            $query = $query->whereDate('created_at', '>=', $from);
            $query = $query->whereDate('created_at', '<=', $to);
        }
        return Excel::download(new  WithdrawExport($query->get()), now() . '.xlsx');
    }

    public function statistics_export(Request $request)
    {
        if ($request->has('with_date') && $request->has('date_to'))
            $request->merge(['date_to' => Carbon::parse($request->date_to)->format('Y-m-d')]);
        $query = Contributor::has('account_ledgers')->withCount(['account_ledgers as current_profit' => function ($q) {
            $q->select(DB::raw('sum(value)'));
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('account_ledgers.created_at', '<', request('date_to'));
        }, 'account_ledgers as total_profit' => function ($q) {
            $q->select(DB::raw('sum(value)'))->where('proccess', 'pay');
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('created_at', '<', request('date_to'));
        }, 'account_ledgers as total_withdrawals' => function ($q) {
            $q->select(DB::raw('sum(value)'))->where('proccess', 'withdraw');
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('account_ledgers.created_at', '<', request('date_to'));
        }, 'purchases as total_purchases' => function ($q) {
            $q->select(DB::raw('sum(unit_price)'));
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }, 'purchases as images_purchases' => function ($q) {
            $q->select(DB::raw('sum(profit_value)'))->where('purchaseable_type', Image::class);
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }, 'purchases as videos_purchases' => function ($q) {
            $q->select(DB::raw('sum(profit_value)'))->where('purchaseable_type', Video::class);
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }, 'purchases as vectors_purchases' => function ($q) {
            $q->select(DB::raw('sum(profit_value)'))->where('purchaseable_type', Vector::class);
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }, 'purchases as sold_images' => function ($q) {
            $q->where('purchaseable_type', Image::class);
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }, 'purchases as sold_videos' => function ($q) {
            $q->where('purchaseable_type', Video::class);
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }, 'purchases as sold_vectors' => function ($q) {
            $q->where('purchaseable_type', Vector::class);
            if (\request('date_to') && request()->has('with_date'))
                $q->whereDate('purchases.created_at', '<', request('date_to'));
        }]);
        if ($request->contributor_id)
            $query->where('id', request('contributor_id'));
        $results = $query->orderBy('id', 'desc')->get();
        return Excel::download(new  ContributorsStatisticsExport($results), 'contributors-statistics-export' . now() . '.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        // TODO allow multiple delete
        $id = explode(',', $id)[0];
        $withdraw = Withdraw::findOrFail($id);
        $withdraw->status_desc_payout = "STOPPED";
        $withdraw->can_payout=0;
        $withdraw->status_payout=0;
        $withdraw->save();
        return redirect()->route('admin.payout.index');
    }

    public function payout($id)
    {
        $ids = explode(',', $id);
        $payout_batch = PayoutBatch::latest()->first();
        $sender_batch_id = \Carbon\Carbon::now()->year . (\Carbon\Carbon::now()->month) . (\Carbon\Carbon::now()->day);
        if ($payout_batch != null)
            $sender_batch_id = $sender_batch_id . (++$payout_batch->id);
        $payout_batch = new PayoutBatch();
        $payout_batch->sender_batch_id = $sender_batch_id;
        $payout_batch->email_subject = "Payouts " . config('app.name');
        $payout_batch->email_message = "You have received a payout! Thanks for using our service!";
        $payout_batch->save();
        $items = [];
        foreach ($ids as $id) {
            $withdraw = Withdraw::findOrFail($id);
            if ($withdraw->can_payout == false || $withdraw->status_payout == 1)
                break;

            $item = [
                'recipient_type' => 'EMAIL',
                "amount" => [
                    'value' => ($withdraw->value_withdraw - $withdraw->fees),
                    'currency' => 'USD',
                ],
                'note' => 'Thanks for your patronage!',
                'sender_item_id' => $withdraw->id,
                'receiver' => $withdraw->email,
            ];
            array_push($items, $item);
        }

        $params = [
            'sender_batch_header' => [
                'sender_batch_id' => $sender_batch_id,
                'email_subject' => "Subject",
                'email_message' => "You have received a payout! Thanks for using our service!",
                'recipient_type' => 'EMAIL',
            ],
            'items' => $items
        ];

        // $response = \App\Contexts\PayPal::create_payout($params);
        $payout_batch_id = $sender_batch_id;
        //$response->result->batch_header->payout_batch_id;
        $payout_batch_status = "PROCESSING";
        //$response->result->batch_header->batch_status;

        $payout_batch->payout_batch_id = $payout_batch_id;
        $payout_batch->payout_batch_status = $payout_batch_status;
        $payout_batch->save();


        foreach ($ids as $id) {

            $withdraw = Withdraw::findOrFail($id);
            $withdraw->status_desc_payout = "PROCESSING";
            $withdraw->can_payout = false;
            $withdraw->save();

            $payout_item = new PayoutItem();
            $payout_item->payout_batch_tbl_id = $payout_batch->id;
            $payout_item->payout_batch_id = $payout_batch_id;
            $payout_item->withdraw_id = $id;
            $payout_item->save();
        }
        dispatch(new \App\Jobs\CheckPayoutBatch())->onConnection('sync');

        return redirect()->route('admin.payout.index');

    }

    public function check_payout()
    {
        dispatch(new \App\Jobs\CheckPayoutBatch());
        //    $payout_batchs = PayoutBatch::where('payout_batch_status','PENDING')->orWhere('payout_batch_status','PROCESSING')->get();

        //    foreach ($payout_batchs as $payout_batch) {
        //         $response = \App\Contexts\PayPal::show_payout_details($payout_batch->payout_batch_id);
        //         $items=$response->result->items;
        //         $batch_status=$response->result->batch_header->batch_status;

        //         $payout_batch->payout_batch_status = $batch_status;

        //         $payout_items = PayoutItem::where('payout_batch_tbl_id',$payout_batch->id)->get();
        //         foreach ($payout_items as $payout_item) {
        //             foreach ($items as $item) {
        //                 if($item->payout_item->sender_item_id == $payout_item->withdraw_id){
        //                     $payout_item->payout_item_id = $item->payout_item_id;
        //                     $payout_item->transaction_id = $item->transaction_id;
        //                     $payout_item->transaction_status = $item->transaction_status;
        //                     $payout_item->payout_item_fee = $item->payout_item_fee->value ;
        //                     $payout_item->withdraw_status = $item->transaction_status ;
        //                     $payout_item->payout_batch_status = $batch_status ;
        //                     // $payout_item->save();

        //                     $withdraw =Withdraw::find($payout_item->withdraw_id);
        //                     $withdraw->status_desc_payout =  $item->transaction_status;
        //                     $withdraw->status_payout = 0;
        //                     if($item->transaction_status == "SUCCESS"){
        //                         $withdraw->status_payout = 1;

        //                         $account_ledger = new AccountLedger();
        //                         $account_ledger->proccess = "withdraw";
        //                         $account_ledger->value = -($withdraw->value_withdraw);
        //                         $account_ledger->contributor_id = $withdraw->contributor_id;
        //                         $account_ledger->account_ledgerable_id = $withdraw->id;
        //                         $account_ledger->accoun_ledgertable_type = Withdraw::class;
        //                         $account_ledger->save();
        //                     }
        //                     elseif ($item->transaction_status == "FAILED" || $item->transaction_status == "RETURNED" || $item->transaction_status == "BLOCKED" || $item->transaction_status == "REFUNDED" || $item->transaction_status == "REVERSED") {
        //                         $withdraw->can_payout = 1;
        //                         $withdraw->status_payout = 0;
        //                     }
        //                     $withdraw->save();
        //                     $payout_item->save();
        //                 }
        //             }
        //         }

        //         $payout_batch->save();
        //    }
    }


    public function index_payoutBatch()
    {
        $index_url = route('admin.payout.datatable_payoutBatch');
        $edit_url = route('admin.payout.bayout_item', 0);
        $destroy_url = route('admin.payout.destroy', 0);

        $object = new PayoutBatch();

        $html_breadcrumbs = [
            'title' => __('views.Withdraws'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views._payout'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.payout.payout', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Cancel_all'),
                'url' => route('admin.payout.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.payout_batch.index',
            compact('html_breadcrumbs', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable_payoutBatch()
    {
        $data = process_datatable_query(PayoutBatch::with('payoutItem'), function (
            $query,
            $search
        ) {
            return $query
                ->where(function($query) use ($search) {
                    $query->orderBy('id','desc');
                    // $query->where('username', 'like', '%' . $search . '%')
                    //       ->orWhere('name', 'like', '%' . $search . '%')
                    //       ->orWhere('email', 'like', '%' . $search . '%');
                });
        });

        return $data;
    }

    public function index_payoutItem($id)
    {
        $index_url = route('admin.payout.datatable_payoutItem');
        $edit_url = route('admin.payout.edit', 0);
        $destroy_url = route('admin.payout.destroy', 0);

        $object = new PayoutItem();

        $html_breadcrumbs = [
            'title' => __('views.Withdraws'),
            'subtitle' => __('views.Index'),
            'datatable' => true,
        ];
        $html_new_path = route('admin.payout.create');
        $subheader_actions = [
            'update_status' => [
                'type' => 'dropdown',
                'text' => __('views.Update Status'),
                'options' => [
                    [
                        'text' => __('views._payout'),
                        'value' => 'active',
                        'class' => 'kt-badge--unified-success',
                        'url' => route('admin.payout.payout', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                    [
                        'text' => __('views.Inactivate'),
                        'value' => 'suspended',
                        'class' => 'kt-badge--unified-danger',
                        'url' => route('admin.members.activate', 0),
                        'method' => 'post',
                        'confirm' => __('views.Are you sure to update :number selected records status to :text ?', ['number' => 0, 'text' => 'ttt']),
                    ],
                ]
            ],
            'delete' => [
                'type' => 'button',
                'text' => __('views.Cancel_all'),
                'url' => route('admin.payout.destroy', 0),
                'method' => 'delete',
                'confirm' => __('views.Are you sure to delete :number selected records ?', ['number' => 0]),
            ],
        ];

        return view(
            'admin_v2.payout_item.index',
            compact('id','html_breadcrumbs', 'html_new_path', 'subheader_actions', 'index_url', 'edit_url', 'destroy_url')
        );
    }

    public function datatable_payoutItem(Request $request)
    {
        $pyaout_items = PayoutItem::where('payout_batch_tbl_id', $request->id)
            ->with(['withdraw' => function ($query) {
                $query->with('contributor'); // without `order_id`
            }]);

        $data = process_datatable_query($pyaout_items, function ($query, $search) {
            // return $query->withdraw;
            //     ->where(function($query) use ($search) {


            //         // $query->where('username', 'like', '%' . $search . '%')
            //         //       ->orWhere('name', 'like', '%' . $search . '%')
            //         //       ->orWhere('email', 'like', '%' . $search . '%');
            //     }
            // );
        });

        return $data;
    }

    public function check_item_payout()
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
                $account_ledger->accountable_type = "App\Models\Withdraw";
                $account_ledger->save();
            }
            $withdraw->save();
        }
    }

    public function edit($id)
    {
        try {
            DB::beginTransaction();
            $ids = explode(',', $id);
            $payout_batch = PayoutBatch::latest()->first();
            $sender_batch_id = \Carbon\Carbon::now()->year . (\Carbon\Carbon::now()->month) . (\Carbon\Carbon::now()->day);
            if ($payout_batch != null)
                $sender_batch_id = $sender_batch_id . (++$payout_batch->id);
            $payout_batch = new PayoutBatch();
            $payout_batch->sender_batch_id = $sender_batch_id;
            $payout_batch->email_subject = "Payouts " . config('app.name');
            $payout_batch->email_message = "You have received a payout! Thanks for using our service!";
            $payout_batch->save();
            $items = [];
            foreach ($ids as $id) {
                $withdraw = Withdraw::findOrFail($id);
                if ($withdraw->can_payout == false || $withdraw->status_payout == 1)
                    break;

                $item = [
                    'recipient_type' => 'EMAIL',
                    "amount" => [
                        'value' => ($withdraw->value_withdraw - $withdraw->fees),
                        'currency' => 'USD',
                    ],
                    'note' => 'Thanks for your patronage!',
                    'sender_item_id' => $withdraw->id,
                    'receiver' => $withdraw->email,
                ];
                array_push($items, $item);
            }

            $params = [
                'sender_batch_header' => [
                    'sender_batch_id' => $sender_batch_id,
                    'email_subject' => "Subject",
                    'email_message' => "You have received a payout! Thanks for using our service!",
                    'recipient_type' => 'EMAIL',
                ],
                'items' => $items
            ];

            // $response = \App\Contexts\PayPal::create_payout($params);
            $payout_batch_id = $sender_batch_id;
            //$response->result->batch_header->payout_batch_id;
            $payout_batch_status = "SUCCESS";
            //$response->result->batch_header->batch_status;

            $payout_batch->payout_batch_id = $payout_batch_id;
            $payout_batch->payout_batch_status = $payout_batch_status;
            $payout_batch->save();


            foreach ($ids as $id) {

                $withdraw = Withdraw::findOrFail($id);
                ///////////////
                $withdraw->status_desc_payout = "SUCCESS";
                $withdraw->status_payout = 1;
                $withdraw->save();
                dispatch(new \App\Jobs\SyncWithdrawalPurchases($withdraw));
                $account_ledger = new AccountLedger();
                $account_ledger->proccess = "withdraw";
                $account_ledger->value = -($withdraw->value_withdraw);
                $account_ledger->contributor_id = $withdraw->contributor_id;
                $account_ledger->accountable_id = $withdraw->id;
                $account_ledger->accountable_type = "App\Models\Withdraw";
                $account_ledger->save();

                $payout_item = new PayoutItem();
                $payout_item->payout_batch_tbl_id = $payout_batch->id;
                $payout_item->payout_batch_id = $payout_batch_id;
                $payout_item->withdraw_id = $withdraw->id;
                $payout_item->payout_item_id = 0;
                $payout_item->transaction_id = '';
                $payout_item->transaction_status = "SUCCESS";
                $payout_item->payout_item_fee = "0";
                $payout_item->withdraw_status = "SUCCESS";
                $payout_item->payout_batch_status = "SUCCESS";
                $payout_item->save();
                // $payout_item->save();
                ////////////////////


            }

            DB::commit();
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . ' line : ' . $th->getLine());
            DB::rollback();
            return redirect()->back()->withErrors('Error Payment contact with developers');

        }
        // dispatch(new \App\Jobs\CheckPayoutBatch())->onConnection('sync');

        return redirect()->route('admin.payout.index');
    }
}
