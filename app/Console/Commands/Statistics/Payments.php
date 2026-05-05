<?php

namespace App\Console\Commands\Statistics;

use App\Contexts\PayPal;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Payments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->stripe_payments();
        $this->paypal_payments();
        cache()->forget('statistics');
        $this->info('Updated successfully');
    }

    private function stripe_payments()
    {
        $payments = cache()->remember('stripe_payments', now()->addMinutes(10), function () {
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
        DB::table('statistics')->where('key', 'stripe_payments')->update(['value' => count($payments)]);
        DB::table('statistics')->where('key', 'local_stripe_payments')->update(['value' => count($local_payments)]);
        $not_found = count(array_diff($payments, $local_payments)) + count(array_diff($local_payments, $payments));

        if ($not_found)
            $this->log_to_telegram("UpdatePaymentsDetails \n$not_found payments error");
    }

    private function paypal_payments()
    {
        $payments = [];
        $paypal = new PayPal();
        $api_context = $paypal->api_context;
        $next_id = true;
        $loop = 1;
        $descriptions = [];
        while ($next_id) {
            $params = ['count' => 20];
            if ($next_id !== true)
                $params['start_id'] = $next_id;
            $results = cache()->remember("paypal_payments_{$loop}", now()->addMinutes(5), function () use ($params, $api_context) {
                return \PayPal\Api\Payment::all($params, $api_context);;
            });
            $loop++;
            /**@var $payment \PayPal\Api\Payment */
            foreach ($results->payments as $payment) {
                $descriptions[] = $payment->transactions[0]->description;
                if ($payment->transactions[0]->related_resources[0]->sale->state == 'completed' && (strpos($payment->transactions[0]->description, 'Credits') or strpos($payment->transactions[0]->description, 'نقاط') or strpos($payment->transactions[0]->description, 'نقطة'))) {
                    $payments[] = $payment;
                }
            }
            $next_id = $results->next_id;
        }

        $payments_ids = \Illuminate\Support\Arr::pluck($payments, 'id');
        $payments_ids = array_unique($payments_ids);
        $local_payments = Subscription::where('payment_method_id', \App\Models\PaymentMethod::PAYPAL)->where('plan_type', 'package')->where('status', 1)->pluck('payment_id')->toArray();
        DB::table('statistics')->where('key', 'paypal_payments')->update(['value' => count($payments_ids)]);
        DB::table('statistics')->where('key', 'local_paypal_payments')->update(['value' => count($local_payments)]);
        $not_found = count(array_diff($payments_ids, $local_payments)) + count(array_diff($local_payments, $payments_ids));
        if ($not_found)
            $this->log_to_telegram("UpdatePaymentsDetails \n$not_found payments error");
    }

    function log_to_telegram($text)
    {
        return;
        try {
            $token = "6310260874:AAH3i_d8pOY818KxunrMY-knfQTKdGMye7U";
            $chat = "592122444";
            $text = urlencode($text);
            return file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat&text=$text");
        } catch (\Exception $exception) {
        }
    }
}
