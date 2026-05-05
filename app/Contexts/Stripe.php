<?php

namespace App\Contexts;

use App\Models\FlexPlan;
use App\Models\ImagePlan;
use App\Models\VideoPlan;
use App\Models\VectorPlan;
use Carbon\Carbon;
use Stripe\PaymentIntent;
use Stripe\Plan;
use Stripe\Subscription;


class Stripe
{


    public static function initialize_plans()
    {
        $plans = \App\Models\Plan::whereNull('stripe_plan')->where('status', 1)->where('type', '!=', 'package')->get();
        foreach ($plans as $plan) {
            if (!$plan->stripe_plan) {
                $product = \Stripe\Product::create([
                    'name' => $plan->title_en,
                    'metadata' => [
                        'plan_id' => $plan->id,
                        'plan_type' => get_class($plan),
                    ],

                ]);

                $params = [
                    'amount' => $plan->price * 100,
                    'currency' => 'usd',
                    'interval' => $plan->frequency,
                    'product' => $product->id,
                    'metadata' => [
                        'plan_id' => $plan->id,
                    ],
                ];
                if ($plan->trial_days)
                    $params['trial_period_days'] = $plan->trial_days;
                $stripe_plan = Plan::create($params);
                $plan->update(['stripe_plan' => $stripe_plan->id]);
            }
        }
    }

    public static function complete_subscription(Subscription $stripe_subscription)
    {
        $stripe_subscription = Subscription::retrieve([
            'id' => $stripe_subscription->id,
            'expand' => ['latest_invoice'],
        ]);
        $subscription = \App\Models\Subscription::withoutGlobalScope('completed')->where('subscription_id', $stripe_subscription->id)->orderBy('id', 'desc')->first();
        if (!$subscription)
            return ['status' => 0, 'Subscription not found'];
        if (!in_array($stripe_subscription->status, ['active', 'trialing']))
            return ['status' => 0, 'message' => "status {$stripe_subscription->status} not allowed"];
        if ($subscription->completed)
            return ['status' => 1, 'message' => "subscription already completed before"];
        $subscription->update([
            'payment_id' => $stripe_subscription->latest_invoice->payment_intent,
            'status' => \App\Models\Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'completed' => 1,
        ]);
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        return ['status' => 1];
    }

    public static function complete_payment(PaymentIntent $payment_intent)
    {
        $subscription = \App\Models\Subscription::withoutGlobalScope('completed')->where('payment_id', $payment_intent->id)->first();
        if (!$subscription)
            return ['status' => 0, 'Subscription not found'];
        if ($payment_intent->status != 'succeeded')
            return ['status' => 0, 'message' => "Payment not succeeded!"];
        $subscription->update([
            'completed' => 1,
            'status' => \App\Models\Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
        ]);
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        return ['status' => 1];
    }

    public static function cancel_subscription($subscription_id, $params)
    {
        try {
            $response = Subscription::retrieve($subscription_id)->cancel();
            return (object)['error_code' => $response->canceled_at ? 0 : 1, 'info' => ['http_code' => 204]];
        } catch (\Exception $exception) {
            return (object)['error_code' => 1];
        }
    }

}
