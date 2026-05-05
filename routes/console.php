<?php

use App\Console\Commands\Statistics\Update;
use App\Contexts\PayPal;
use App\Jobs\SendNewsletterChunk;
use App\Models\ImageSubscription;
use App\Models\Purchase;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('initialize_new_paypal_account', function () {
    \App\Contexts\Plans::initialize_new_paypal_account();
    \App\Contexts\Plans::initialize_plans_videos();
    \App\Contexts\Plans::initialize_plans_vectors();
});

Artisan::command('fix-videos-urls/{id*}', function ($id) {
    $data = \App\Models\Video::select('id')->where('status', 'active')->whereNull('parent_id')->orderBy('id', 'desc');
    if ($id[0] !== "all") {
        $data = $data->whereIn('id', $id);
    }
    $data = $data->get();

    foreach ($data as $r) {
        dispatch(new \App\Jobs\SeoVideos($r->id));
    }
});

Artisan::command('fix-vectors-urls/{id*}', function ($id) {
    $data = \App\Models\Vector::select('id')->where('status', 'active')->where('is_uploaded', 1)->orderBy('id', 'desc');
    if ($id[0] !== "all") {
        $data = $data->whereIn('id', $id);
    }

    $data = $data->get();
    foreach ($data as $r) {
        dispatch(new \App\Jobs\SeoVectors($r->id));
    }
});

Artisan::command('generate_invoice {ids?*}', function ($ids) {
    foreach ($ids as $id) {
        $subscription = Subscription::withoutGlobalScope('completed')->find($id);
        if ($subscription)
            dispatch(new \App\Jobs\CreateInvoicePDF($subscription))->onConnection('sync');
    }
});

Artisan::command('generate_image_invoice {ids?*}', function ($ids) {
    foreach ($ids as $id) {
        $subscription = ImageSubscription::findOrFail($id);
        if ($subscription)
            dispatch(new \App\Jobs\CreateInvoicePDF($subscription))->onConnection('sync');
    }
});
Artisan::command('set_transaction_id', function () {
    foreach (\App\Models\Subscription::whereIn('plan_type', ['monthly', 'annual'])
                 ->whereNull('payment_id')
                 ->groupBy('subscription_id')->get()->pluck('subscription_id') as $subscription_id) {
        $payments = \App\Models\Subscription::where('subscription_id', $subscription_id)->get();
        if ($payments->first()->payment_method_id == \App\Models\PaymentMethod::PAYPAL)
            $transactions = collect(\App\Contexts\PayPal::get_subscription_payments($subscription_id, [
                'Content-Type' => 'application/json',
                'start_time' => now()->subYears(4)->format('c'),
                'end_time' => now()->format('c'),
            ])->result->transactions)->pluck('id')->toArray();
        elseif ($payments->first()->payment_method_id == \App\Models\PaymentMethod::STRIPE) {
            $transactions = collect(\Stripe\Invoice::search([
                'query' => "subscription:\"{$subscription_id}\"",
                'limit' => 100,
            ])->data)->filter(function ($r) {
                return $r->paid;
            })->sortBy('created:')->pluck('payment_intent')->toArray();
        }
        $loop = 0;
        foreach ($payments as $payment) {
            $id = $loop;
            if (!isset($transactions[$id]))
                dd(['subscription_id' => $subscription_id, 'transactions' => $transactions]);
            $payment->update(['payment_id' => $transactions[$id]]);
            $loop++;
        }
    }
    $this->comment('Updated successfully');
});
Artisan::command('compare-stripe-payments', function () {
    $payments = cache()->remember('stripe_payments', now()->addDay(), function () {
        $payments = [];
        $results = \Stripe\PaymentIntent::search([
            'query' => 'status:\'succeeded\'',
            'limit' => 100,
        ]);;

        /**@var $pi \Stripe\PaymentIntent */
        foreach ($results->autoPagingIterator() as $pi) {
            /**@var \Stripe\Collection */
            $charges = $pi->charges->toArray()['data'];
            if (count(\Illuminate\Support\Arr::where($charges, function ($r) {
                return $r['paid'] == true && $r['amount_refunded'] == 0 && $r['disputed'] == false;
            })))
                $payments[] = $pi->id;
        }
        return $payments;
    });
    $local_payments = Subscription::where('payment_method_id', \App\Models\PaymentMethod::STRIPE)->where('status', 1)->pluck('payment_id')->toArray();
    dd([
        array_diff($payments, $local_payments),
        array_diff($local_payments, $payments)
    ]);
});
Artisan::command('old-removebg-images', function () {
    foreach (\App\Models\Image::select('id', 'removebg_image')->whereNotNull('removebg_image')->get() as $image) {
        dispatch(function () use ($image) {
            if (!\Illuminate\Support\Facades\Storage::disk('s3')->exists($image->removebg_image)) {
                \Illuminate\Support\Facades\Log::channel('info')->info("file not found: {$image->removebg_image}");
            }
        })->onQueue('sss');
    }
});

Artisan::command('sss', function () {
    Update::notifications();
});

