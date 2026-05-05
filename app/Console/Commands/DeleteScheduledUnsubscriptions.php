<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use App\ScheduledUnsubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteScheduledUnsubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:delete-scheduled';

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
        $results = ScheduledUnsubscription::where('done', 0)->whereDate('date', now()->format('Y-m-d'))->has('subscription')->with('subscription')->get();
        if ($results->count() == 0)
            $this->info('No ScheduledUnsubscription');
        foreach ($results as $r) {
            $subscription = $r->subscription;
            $params = ['reason' => 'Not satisfied with the service'];
            if ($subscription->payment_method_id == PaymentMethod::PAYPAL) {
                \App\Contexts\PayPal::suspend_subscription($subscription->subscription_id, $params);
                Log::channel('info')->warning("Suspend Paypal subscription ", ['id' => $subscription->id, 'subscription_id' => $subscription->subscription_id]);
            } elseif ($subscription->payment_method_id == PaymentMethod::STRIPE) {
                \Stripe\Subscription::update(
                    $subscription->subscription_id,
                    [
                        'pause_collection' => [
                            'behavior' => 'keep_as_draft',
                        ],
                    ]
                );
//                \App\Contexts\Stripe::cancel_subscription($subscription->subscription_id, $params);
                Log::channel('info')->warning("Cancel Stripe subscription ", ['id' => $subscription->id, 'subscription_id' => $subscription->subscription_id]);
            }
            $r->update(['done' => 1]);
        }
    }
}
