<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PhpParser\Node\Expr\Cast\Array_;
use App\Models\ImageSubscription;
use App\Models\Subscription;
use App\Models\VectorSubscription;
use App\Models\VideoSubscription;

class GeneratePdfInvoicesForOldPayments implements ShouldQueue
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
        echo "********* Start generate_pdf_invoices_for_old_payments ********* \r\n";
        /* s:new */
                // dispatch for images packages
                $subscriptions = Subscription::where('invoice_file', '')
                ->where('completed', 1)
                ->orderBy('id', 'desc');
            $count =  $subscriptions->count();
            $subscriptions =  $subscriptions->get();
            echo "dispatch for images subscription new count: {$count} \r\n";


            foreach ($subscriptions as $subscription) {
                if ($subscription->user) {
                    $client_name = $subscription->user->name;
                    if ($subscription->user->is_business){
                        $client_name = $client_name .' ('.$subscription->user->company_name.')';
                    }
                    $client_email = $subscription->user->email;
                } else {
                    $client_name = "";
                    $client_email = "";
                }

                    echo "dispatch for subscription new id: {$subscription->id} \r\n";
                    $this->DispatchCreateInvoicePdf($subscription);

            }

        /* e:new */
        // dispatch for images packages
        $subscriptions = ImageSubscription::whereNotNull('payment_id')
            ->where('invoice_file', '')
            ->where('status', 1)
            ->orderBy('id', 'desc');
        $count =  $subscriptions->count();
        $subscriptions =  $subscriptions->get();
        echo "dispatch for images packages count: {$count} \r\n";


        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                $client_name = $subscription->user->name;
                if ($subscription->user->is_business){
                    $client_name = $client_name .' ('.$subscription->user->company_name.')';
                }
                $client_email = $subscription->user->email;
            } else {
                $client_name = "";
                $client_email = "";
            }

                echo "dispatch for images packages id: {$subscription->id} \r\n";

                $data = [
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
                ];
                $this->DispatchCreateInvoicePdf($data);

        }

        // dispatch for images subscriptions
        $subscriptions = ImageSubscription::whereNotNull('subscription_id')
            ->where('invoice_file', '')
            ->whereIn('status', [ImageSubscription::STATUS_ACTIVE,ImageSubscription::STATUS_CANCEL])
            ->orderBy('id', 'desc');
            $count =  $subscriptions->count();
            $subscriptions =  $subscriptions->get();
            echo "dispatch for images subscriptions count: {$count} \r\n";



        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                $client_name = $subscription->user->name;
                if ($subscription->user->is_business){
                    $client_name = $client_name .' ('.$subscription->user->company_name.')';
                }
                $client_email = $subscription->user->email;
            } else {
                $client_name = "";
                $client_email = "";
            }
            echo "dispatch for images subscriptions id: {$subscription->id} \r\n";

                $data = [
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
                ];
                $this->DispatchCreateInvoicePdf($data);

        }
        /* s:videos */
        // dispatch for videos packages
        $subscriptions = VideoSubscription::whereNotNull('payment_id')
            ->where('invoice_file', '')
            ->where('status', 1)
            ->orderBy('id', 'desc');
            $count =  $subscriptions->count();
            $subscriptions =  $subscriptions->get();
            echo "dispatch for videos packages count: {$count} \r\n";


        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                $client_name = $subscription->user->name;
                if ($subscription->user->is_business){
                    $client_name = $client_name .' ('.$subscription->user->company_name.')';
                }
                $client_email = $subscription->user->email;
            } else {
                $client_name = "";
                $client_email = "";
            }
            echo "dispatch for videos packages id: {$subscription->id} \r\n";


                $data = [
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
                ];
                $this->DispatchCreateInvoicePdf($data);

        }

        // dispatch for videos subscriptions
        $subscriptions = VideoSubscription::whereNotNull('subscription_id')
            ->where('invoice_file', '')
            ->whereIn('status', [VideoSubscription::STATUS_ACTIVE,VideoSubscription::STATUS_CANCEL])
            ->orderBy('id', 'desc');
            $count =  $subscriptions->count();
            $subscriptions =  $subscriptions->get();
            echo "dispatch for videos subscriptions count: {$count} \r\n";

        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                $client_name = $subscription->user->name;
                if ($subscription->user->is_business){
                    $client_name = $client_name .' ('.$subscription->user->company_name.')';
                }
                $client_email = $subscription->user->email;
            } else {
                $client_name = "";
                $client_email = "";
            }
            echo "dispatch for videos subscriptions id: {$subscription->id} \r\n";

                $data =[
                    'id' => $subscription->id,
                    'invoice_id' => $subscription->subscription_id,
                    'type' => 'video',
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
                ];
                $this->DispatchCreateInvoicePdf($data);

        }

        /* e:videos */
        /* s:vector */
        // dispatch for vectors packages
        $subscriptions = VectorSubscription::whereNotNull('payment_id')
            ->where('invoice_file', '')
            ->where('status', 1)
            ->orderBy('id', 'desc');
            $count =  $subscriptions->count();
            $subscriptions =  $subscriptions->get();
            echo "dispatch for vectors packages count: {$count} \r\n";


        foreach ($subscriptions as $subscription) {
            if ($subscription->user) {
                $client_name = $subscription->user->name;
                if ($subscription->user->is_business){
                    $client_name = $client_name .' ('.$subscription->user->company_name.')';
                }
                $client_email = $subscription->user->email;
            } else {
                $client_name = "";
                $client_email = "";
            }

            echo "dispatch for vectors packages id: {$subscription->id} \r\n";


                $data =[
                    'id' => $subscription->id,
                    'invoice_id' => $subscription->payment_id,
                    'type' => 'vector',
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
                ];
            $this->DispatchCreateInvoicePdf($data);

        }

        // dispatch for vectors subscriptions
        $subscriptions = VectorSubscription::whereNotNull('subscription_id')
            ->where('invoice_file', '')
            ->whereIn('status',[VectorSubscription::STATUS_ACTIVE,VectorSubscription::STATUS_CANCEL] )
            ->orderBy('id', 'desc');
            $count =  $subscriptions->count();
            $subscriptions =  $subscriptions->get();
            echo "dispatch for vectors subscriptions count: {$count} \r\n";

        foreach ($subscriptions as $subscription) {

            if ($subscription->user) {
                $client_name = $subscription->user->name;
                if ($subscription->user->is_business){
                    $client_name = $client_name .' ('.$subscription->user->company_name.')';
                }
                $client_email = $subscription->user->email;
            } else {
                $client_name = "";
                $client_email = "";
            }

            echo "dispatch for vectors subscriptions id: {$subscription->id} \r\n";

            $data =[
                'id' => $subscription->id,
                'invoice_id' => $subscription->subscription_id,
                'type' => 'vector',
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
            ];
            $this->DispatchCreateInvoicePdf($data);
        }
        /* e:vector */

        echo "********* End generate_pdf_invoices_for_old_payments ********* \r\n";
    }

    public function DispatchCreateInvoicePdf($subscription)
    {
        dispatch(
            new \App\Jobs\CreateInvoicePDF($subscription)
        );
    }
}
