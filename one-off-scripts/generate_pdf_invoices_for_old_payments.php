<?php

use App\Models\ImageSubscription;
use App\Models\VectorSubscription;
use App\Models\VideoSubscription;
use App\Models\Videos;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
echo "********* Start update invoce ********* \r\n";

// dispatch for images packages
$subscriptions = ImageSubscription::whereNotNull('payment_id')
    ->where('invoice_file', '')
    ->where('status', 1)
    ->orderBy('id', 'desc')
    ->get();
echo "images packages without invoice pdf file count: {count($subscriptions)} \r\n";

foreach ($subscriptions as $subscription) {
    if ($subscription->user) {
        $client_name = $subscription->user->name;
        $client_email = $subscription->user->email;
    } else {
        $client_name = "";
        $client_email = "";
    }

    dispatch(
        new \App\Jobs\CreateInvoicePDF([
            'id' => $subscription->id,
            'invoice_id' => $subscription->payment_id,
            'type' => 'images',
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_is_business' => $subscription->user->is_business ? '1': '0',
            'client_company_contact' => $subscription->user->company_email.' , '.$subscription->user->company_phone,
            'client_company_address' => $subscription->user->company_address,
            'client_company_tax_id' => $subscription->user->company_tax_id,
            'date' => explode(' ', $subscription->created_at)[0],
            'amount' => "{$subscription->amount} USD",
            'payment_method' => "PayPal",
            'payment_status' => "paid",
            'paid' => "{$subscription->amount} USD",
            'items' => [['title'=>$subscription->plan->title_en, 'amount'=>"{$subscription->amount} USD"]],
            'terms' => __('views.Immediate payment', [], 'en'),
        ])
    )->onConnection('redis');
}

// dispatch for images subscriptions
$subscriptions = ImageSubscription::whereNotNull('subscription_id')
    ->where('invoice_file', '')
    ->whereIn('status', [ImageSubscription::STATUS_ACTIVE,ImageSubscription::STATUS_CANCEL])
    ->orderBy('id', 'desc')
    ->get();
echo " images subscriptions without invoice pdf file count: {count($subscriptions)} \r\n";

foreach ($subscriptions as $subscription) {
    if ($subscription->user) {
        $client_name = $subscription->user->name;
        $client_email = $subscription->user->email;
    } else {
        $client_name = "";
        $client_email = "";
    }
    dispatch(
        new \App\Jobs\CreateInvoicePDF([
            'id' => $subscription->id,
            'invoice_id' => $subscription->subscription_id,
            'type' => 'images',
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_is_business' => $subscription->user->is_business ? '1': '0',
            'client_company_contact' => $subscription->user->company_email.' , '.$subscription->user->company_phone,
            'client_company_address' => $subscription->user->company_address,
            'client_company_tax_id' => $subscription->user->company_tax_id,
            'date' => explode(' ', $subscription->created_at)[0],
            'amount' => "{$subscription->amount} USD",
            'payment_method' => "PayPal",
            'payment_status' => "paid",
            'paid' => "{$subscription->amount} USD",
            'items' => [['title'=>$subscription->plan->title_en, 'amount'=>"{$subscription->amount} USD"]],
            'terms' => __('views.Immediate payment', [], 'en'),
        ])
    )->onConnection('redis');
}
/* s:videos */
// dispatch for videos packages
$subscriptions = VideoSubscription::whereNotNull('payment_id')
    ->where('invoice_file', '')
    ->where('status', 1)
    ->orderBy('id', 'desc')
    ->get();
echo "videos packages without invoice pdf file count: {count($subscriptions)} \r\n";

foreach ($subscriptions as $subscription) {
    if ($subscription->user) {
        $client_name = $subscription->user->name;
        $client_email = $subscription->user->email;
    } else {
        $client_name = "";
        $client_email = "";
    }

    dispatch(
        new \App\Jobs\CreateInvoicePDF([
            'id' => $subscription->id,
            'invoice_id' => $subscription->payment_id,
            'type' => "video",
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_is_business' => $subscription->user->is_business ? '1': '0',
            'client_company_contact' => $subscription->user->company_email.' , '.$subscription->user->company_phone,
            'client_company_address' => $subscription->user->company_address,
            'client_company_tax_id' => $subscription->user->company_tax_id,
            'date' => explode(' ', $subscription->created_at)[0],
            'amount' => "{$subscription->amount} USD",
            'payment_method' => "PayPal",
            'payment_status' => "paid",
            'paid' => "{$subscription->amount} USD",
            'items' => [['title'=>$subscription->plan->title_en, 'amount'=>"{$subscription->amount} USD"]],
            'terms' => __('views.Immediate payment', [], 'en'),
        ])
    )->onConnection('redis');
}

// dispatch for videos subscriptions
$subscriptions = VideoSubscription::whereNotNull('subscription_id')
    ->where('invoice_file', '')
    ->whereIn('status', [VideoSubscription::STATUS_ACTIVE,VideoSubscription::STATUS_CANCEL])
    ->orderBy('id', 'desc')
    ->get();
echo " videos subscriptions without invoice pdf file count: {count($subscriptions)} \r\n";

foreach ($subscriptions as $subscription) {
    if ($subscription->user) {
        $client_name = $subscription->user->name;
        $client_email = $subscription->user->email;
    } else {
        $client_name = "";
        $client_email = "";
    }
    dispatch(
        new \App\Jobs\CreateInvoicePDF([
            'id' => $subscription->id,
            'invoice_id' => $subscription->subscription_id,
            'type' => 'videos',
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_is_business' => $subscription->user->is_business ? '1': '0',
            'client_company_contact' => $subscription->user->company_email.' , '.$subscription->user->company_phone,
            'client_company_address' => $subscription->user->company_address,
            'client_company_tax_id' => $subscription->user->company_tax_id,
            'date' => explode(' ', $subscription->created_at)[0],
            'amount' => "{$subscription->amount} USD",
            'payment_method' => "PayPal",
            'payment_status' => "paid",
            'paid' => "{$subscription->amount} USD",
            'items' => [['title'=>$subscription->plan->title_en, 'amount'=>"{$subscription->amount} USD"]],
            'terms' => __('views.Recurring Invoice', [], 'en'),
        ])
    )->onConnection('redis');
}

/* e:videos */
/* s:vector */
// dispatch for vectors packages
$subscriptions = VectorSubscription::whereNotNull('payment_id')
    ->where('invoice_file', '')
    ->where('status', 1)
    ->orderBy('id', 'desc')
    ->get();
echo "vectors packages without invoice pdf file count: {count($subscriptions)} \r\n";

foreach ($subscriptions as $subscription) {
    if ($subscription->user) {
        $client_name = $subscription->user->name;
        $client_email = $subscription->user->email;
    } else {
        $client_name = "";
        $client_email = "";
    }

    dispatch(
        new \App\Jobs\CreateInvoicePDF([
            'id' => $subscription->id,
            'invoice_id' => $subscription->payment_id,
            'type' => 'vectors',
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_is_business' => $subscription->user->is_business ? '1': '0',
            'client_company_contact' => $subscription->user->company_email.' , '.$subscription->user->company_phone,
            'client_company_address' => $subscription->user->company_address,
            'client_company_tax_id' => $subscription->user->company_tax_id,
            'date' => explode(' ', $subscription->created_at)[0],
            'amount' => "{$subscription->amount} USD",
            'payment_method' => "PayPal",
            'payment_status' => "paid",
            'paid' => "{$subscription->amount} USD",
            'items' => [['title'=>$subscription->plan->title_en, 'amount'=>"{$subscription->amount} USD"]],
            'terms' => __('views.Immediate payment', [], 'en'),
        ])
    )->onConnection('redis');
}

// dispatch for vectors subscriptions
$subscriptions = VectorSubscription::whereNotNull('subscription_id')
    ->where('invoice_file', '')
    ->whereIn('status',[VectorSubscription::STATUS_ACTIVE,VectorSubscription::STATUS_CANCEL] )
    ->orderBy('id', 'desc')
    ->get();
echo " vectors subscriptions without invoice pdf file count: {count($subscriptions)} \r\n";

foreach ($subscriptions as $subscription) {
    if ($subscription->user) {
        $client_name = $subscription->user->name;
        $client_email = $subscription->user->email;
    } else {
        $client_name = "";
        $client_email = "";
    }
    dispatch(
        new \App\Jobs\CreateInvoicePDF([
            'id' => $subscription->id,
            'invoice_id' => $subscription->subscription_id,
            'type' => 'vectors',
            'client_name' => $client_name,
            'client_email' => $client_email,
            'client_is_business' => $subscription->user->is_business ? '1': '0',
            'client_company_contact' => $subscription->user->company_email.' , '.$subscription->user->company_phone,
            'client_company_address' => $subscription->user->company_address,
            'client_company_tax_id' => $subscription->user->company_tax_id,
            'date' => explode(' ', $subscription->created_at)[0],
            'amount' => "{$subscription->amount} USD",
            'payment_method' => "PayPal",
            'payment_status' => "paid",
            'paid' => "{$subscription->amount} USD",
            'items' => [['title'=>$subscription->plan->title_en, 'amount'=>"{$subscription->amount} USD"]],
            'terms' => __('views.Recurring Invoice', [], 'en'),
        ])
    )->onConnection('redis');
}
/* e:vector */

    echo "********* End update invoce ********* \r\n";
