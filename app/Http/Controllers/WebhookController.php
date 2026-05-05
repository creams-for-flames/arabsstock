<?php

namespace App\Http\Controllers;

use App\Cashier\Invoice;
use App\Contexts\PayPal;
use App\Contexts\Stripe;
use App\Models\BouncedEmail;
use App\Models\Download;
use App\Models\ImagePlan;
use App\Models\ImageSubscription;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\VectorPlan;
use App\Models\VideoPlan;
use App\Models\VideoSubscription;
use App\Models\PaymentsLog;
use App\Models\PaypalPayer;
use App\Models\ImageDownload;
use App\Models\VideoDownload;
use App\Models\VectorDownload;
use App\Models\VectorSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PayPal\Api\WebhookEvent;
use Stripe\Customer;
use Stripe\Dispute;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use SendGrid\EventWebhook\EventWebhook;
use SendGrid\EventWebhook\EventWebhookHeader;
use Stripe\PaymentIntent;


class WebhookController extends Controller
{

    private $is_images_hook = false;
    private $is_type_hook = '';

    public function handlePaypalWebhook(Request $request)
    {
        Log::channel('webhooks')->info("Paypal Webhook: " . json_encode($request->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $paypal = new PayPal();
        $data = null;
        if (!app()->isLocal()) {
            try {
                $data = WebhookEvent::get($request->id, $paypal->api_context)->toArray();
            } catch (\Exception $exception) {
                return ['status' => 0];
            }
        } else
            $data = $request->all();


        // we only need these hooks
        if (!isset($data['event_type']) || !in_array($data['event_type'], ['PAYMENT.SALE.COMPLETED', 'BILLING.SUBSCRIPTION.ACTIVATED', 'BILLING.SUBSCRIPTION.SUSPENDED', 'BILLING.SUBSCRIPTION.CANCELLED', 'PAYMENTS.PAYMENT.CREATED', 'PAYMENT.SALE.REFUNDED', 'PAYMENT.SALE.REVERSED'])) {
            return ['status' => 0];
        }


        // get payment_id or $subscription_id
        $payment_id = null;
        $subscription_id = null;

        if ($data['event_type'] === 'PAYMENT.SALE.COMPLETED') {

            if (isset($data['resource']['billing_agreement_id'])) {
                $subscription_id = $data['resource']['billing_agreement_id'];
            } else {
                $payment_id = $data['resource']['parent_payment'];
            }
        }

        if (in_array($data['event_type'], ['PAYMENT.SALE.REFUNDED', 'PAYMENT.SALE.REVERSED'])) {

            $response = \App\Contexts\PayPal::get_sale($data['resource']['sale_id']);
            // dd($response);
            if ($response->error_code !== 0) {
                abort(500, $response->error);
            }
            $sale = $response->result;

            if (property_exists($sale, 'parent_payment')) {
                $payment_id = $sale->parent_payment;
            } else if (property_exists($sale, 'billing_agreement_id')) {
                $subscription_id = $sale->billing_agreement_id;
            } else {
                Log::channel('webhooks')->error("paypal {$data['event_type']} has some errors");
                return ['status' => 0];
            }
        }


        if ($data['event_type'] === 'BILLING.SUBSCRIPTION.ACTIVATED') {
            $subscription_id = $data['resource']['id'];
        }

        if ($data['event_type'] === 'BILLING.SUBSCRIPTION.SUSPENDED') {
            $subscription_id = $data['resource']['id'];
        }

        if ($data['event_type'] === 'BILLING.SUBSCRIPTION.CANCELLED') {
            $subscription_id = $data['resource']['id'];
        }

        if ($subscription_id) {
            $subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $subscription_id)->first();
            if (!$subscription)
                $subscription = ImageSubscription::where('subscription_id', $subscription_id)->first();
            if (!$subscription)
                $subscription = VideoSubscription::where('subscription_id', $subscription_id)->first();
            if (!$subscription)
                $subscription = VectorSubscription::where('subscription_id', $subscription_id)->first();
            if (!$subscription)
                return ['status' => 0];
        } else {
            $subscription = Subscription::withoutGlobalScope('completed')->where('payment_id', $payment_id)->first();
            if (!$subscription)
                $subscription = ImageSubscription::where('payment_id', $payment_id)->first();
            if (!$subscription)
                $subscription = VideoSubscription::where('payment_id', $payment_id)->first();
            if (!$subscription)
                $subscription = VectorSubscription::where('payment_id', $payment_id)->first();
            if (!$subscription)
                return ['status' => 0];
        }
        $log = PaymentsLog::where('webhook_id', request()->get('id'))->first();
        if ($log) {
            return ['status' => 0];
        }
        //here you now
        if ($data['event_type'] == 'PAYMENT.SALE.COMPLETED') {
            if ($subscription_id) {
                if (get_class($subscription) == ImageSubscription::class) {
                    // first payment for subscription
                    $this->payment_sale_completed_subscription_image($data['resource']);
                } elseif (get_class($subscription) == VideoSubscription::class) {
                    // first payment for subscription
                    $this->payment_sale_completed_subscription_video($data['resource']);

                } elseif (get_class($subscription) == VideoSubscription::class) {
                    // first payment for subscription
                    $this->payment_sale_completed_subscription_vector($data['resource']);

                } else {
                    // first payment for subscription
                    $this->payment_sale_completed_subscription($data['resource']);
                }
            } else {
                if (get_class($subscription) == ImageSubscription::class) {
                    // first payment for subscription
                    $this->payment_sale_completed_package_image($data['resource']);
                } elseif (get_class($subscription) == VideoSubscription::class) {
                    // first payment for subscription
                    $this->payment_sale_completed_package_video($data['resource']);

                } elseif (get_class($subscription) == VectorSubscription::class) {
                    // first payment for subscription
                    $this->payment_sale_completed_package_vector($data['resource']);
                } else {
                    // first payment for subscription
                    $this->payment_sale_completed_package($data['resource']);
                }
            }
        } // end event sale.completed

        if (in_array($data['event_type'], ['PAYMENT.SALE.REFUNDED', 'PAYMENT.SALE.REVERSED'])) {
            if ($subscription_id) {
                // first payment for subscription
                if ($this->is_type_hook == 'image') {
                    // first payment for subscription
                    $this->payment_sale_refunded_subscription_image($subscription_id);
                } elseif ($this->is_type_hook == 'video') {
                    // first payment for subscription
                    $this->payment_sale_refunded_subscription_video($subscription_id);

                } elseif ($this->is_type_hook == 'vector') {
                    // first payment for subscription
                    $this->payment_sale_refunded_subscription_vector($subscription_id);
                } else {
                    // first payment for subscription
                    $this->payment_sale_refunded_subscription($subscription_id);
                }

            } else {
                if ($this->is_type_hook == 'image') {
                    $this->payment_sale_refunded_package_image($payment_id);
                } elseif ($this->is_type_hook == 'video') {
                    $this->payment_sale_refunded_package_video($payment_id);
                } elseif ($this->is_type_hook == 'vector') {
                    $this->payment_sale_refunded_package_vector($payment_id);
                } else {
                    $this->payment_sale_refunded_package($payment_id);
                }
            }
        } // end event sale.refunded

        if ($data['event_type'] == 'BILLING.SUBSCRIPTION.ACTIVATED') {
            // after first payment for subscription , paypal tell us it is activated
            if ($this->is_type_hook == 'image') {
                // first payment for subscription
                $this->billing_subscription_activated_image($data['resource']);
            } elseif ($this->is_type_hook == 'video') {
                // first payment for subscription
                $this->billing_subscription_activated_video($data['resource']);

            } elseif ($this->is_type_hook == 'vector') {
                // first payment for subscription
                $this->billing_subscription_activated_vector($data['resource']);
            } else {
                // first payment for subscription
                $this->billing_subscription_activated($data['resource']);
            }

        }

        if ($data['event_type'] == 'BILLING.SUBSCRIPTION.SUSPENDED') {
            // after first payment for subscription , paypal tell us it is suspended
            if ($this->is_type_hook == 'image') {
                // first payment for subscription
                $this->billing_subscription_suspended_image($data['resource']);
            } elseif ($this->is_type_hook == 'video') {
                // first payment for subscription
                $this->billing_subscription_suspended_video($data['resource']);

            } elseif ($this->is_type_hook == 'vector') {
                // first payment for subscription
                $this->billing_subscription_suspended_vector($data['resource']);
            } else {
                // first payment for subscription
                $this->billing_subscription_suspended($data['resource']);
            }

        }

        if ($data['event_type'] == 'BILLING.SUBSCRIPTION.CANCELLED') {
            // after first payment for subscription , paypal tell us it is suspended
            if ($this->is_type_hook == 'image') {
                // first payment for subscription
                $this->billing_subscription_canceled_image($data['resource']);
            } elseif ($this->is_type_hook == 'video') {
                // first payment for subscription
                $this->billing_subscription_canceled_video($data['resource']);

            } elseif ($this->is_type_hook == 'vector') {
                // first payment for subscription
                $this->billing_subscription_canceled_vector($data['resource']);

            } else {
                // first payment for subscription
                $this->billing_subscription_canceled($data['resource']);
            }
        }

        if (!in_array($data['event_type'], ['PAYMENT.SALE.COMPLETED', 'PAYMENTS.PAYMENT.CREATED', 'BILLING.SUBSCRIPTION.ACTIVATED', 'BILLING.SUBSCRIPTION.SUSPENDED', 'BILLING.SUBSCRIPTION.CANCELLED'])) {
            $payment_log = PaymentsLog::create([
                'user_id' => null,
                'payment_method_id' => 2, // paypal
                'webhook_id' => request()->get('id'),
                'event_type' => request()->get('event_type'),
                'resource_type' => request()->get('resource_type'),
                'category' => $this->is_type_hook,
                'data' => request()->all(),
            ]);
        }
        return ['status' => 1];
    }

    private function payment_sale_completed_subscription_image($data)
    {
        $payer = PaypalPayer::where('subscription_id', $data['billing_agreement_id'])
            ->orderBy('id', 'desc')->first();
        if (!$payer) {
            Log::channel('webhooks')->error("payment_sale_completed_subscription_image not exist subscription_id {$data['billing_agreement_id']}");
            return ['status' => 0];
        }

        $user = User::findOrFail($payer->user_id);
        $plan = ImagePlan::findOrFail($payer->plan_id);
        $params['subscription_id'] = $data['billing_agreement_id'];
        $params['plan_id'] = $plan->id;
        $params['user_id'] = $user->id;
        $params['geoip'] = geoip($payer->ip);
        // $params['geoip'] = '192.120.168.143';
        $params['status'] = Subscription::STATUS_ACTIVE;
        $params['starts_at'] = now()->setTimezone('Asia/Riyadh');
        $params['amount'] = $data['amount']['total'];
        $params['payment_gateway_fee'] = $data['transaction_fee']['value'];
        $params['payment_method_id'] = PaymentMethod::PAYPAL;
        //  TODO research more;
        /* $params['currency'] = $data['amount']['currency']; */
        /* $params['sale_id'] = $data['id']; */

        $last_subscription = ImageSubscription::where(['subscription_id' => $data['billing_agreement_id']])
            ->orderBy('id', 'desc')
            ->first();

        if ($last_subscription && $last_subscription->ends_at) {
//            $params['starts_at'] = \Carbon\Carbon::parse($last_subscription->ends_at);
            $params['country_id'] = $last_subscription->country_id;
            $params['city_id'] = $last_subscription->city_id;
        }
        if ($plan->type == 'monthly')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addDays(30);
        elseif ($plan->type == 'annual')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addYear();
        if ($last_subscription)
            $last_subscription->update(['ends_at' => now()]);
        $subscription = \App\Contexts\Plans::store_subscription($user, $plan, $params);
        if (!$subscription) {
            Log::channel('webhooks')->error('Payment Processing');
        }

        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end pdf generation

        $payment_log = PaymentsLog::create([
            'user_id' => $payer->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'plan_id' => $payer->plan_id,
            'subscription_id' => $subscription ? $subscription->id : null,
            'category' => $this->is_type_hook,
            'data' => request()->all(),
        ]);

        return ['status' => 0];
    }

    private function payment_sale_completed_package_image($data)
    {
        $payment_id = $data['id'];
        if (isset($data['parent_payment'])) {
            $payment_id = $data['parent_payment'];
        }

        $subscription = ImageSubscription::where(['payment_id' => $payment_id])->first();
        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (request()->get('event_type') == 'PAYMENT.SALE.COMPLETED') {
            return ['status' => 0];
        }

        if (!$subscription) {
            Log::channel('webhooks')->error("subscription id {$data['id']} does not exist");
            return ['status' => 0];
        }

        if ($subscription->status == ImageSubscription::STATUS_ACTIVE) {
            return ['status' => 0];
        }

        $payer_id = $data['payer']['payer_info']['payer_id'];
        $response = \App\Contexts\PayPal::execute_payment($data['id'], ['payer_id' => $payer_id]);

        if ($response->result->state !== 'approved') {
            Log::channel('webhooks')->error("subscription id for payment : {$data['id']} does not approved");
            return ['status' => 0];
        }

        $price = $data['transactions'][0]['amount']['total'];
        $currency = $data['transactions'][0]['amount']['currency'];
        //$fee = $data['transaction_fee']['value'];

        $subscribtion_data = [
            'created_by_hook' => true,
            'starts_at' => now()->setTimezone('Asia/Riyadh'),
            'ends_at' => now()->addYear(),
            'status' => ImageSubscription::STATUS_ACTIVE,
            'amount' => $price,
            'currency' => $currency,
            'data' => json_encode($data),
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function payment_sale_completed_package($data)
    {
        $payment_id = $data['id'];
        if (isset($data['parent_payment'])) {
            $payment_id = $data['parent_payment'];
        }

        $subscription = Subscription::withoutGlobalScope('completed')->where(['payment_id' => $payment_id])->first();
        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (request()->get('event_type') == 'PAYMENT.SALE.COMPLETED') {
            return ['status' => 0];
        }

        if (!$subscription) {
            Log::channel('webhooks')->error("subscription id {$data['id']} does not exist");
            return ['status' => 0];
        }

        if ($subscription->status == ImageSubscription::STATUS_ACTIVE) {
            return ['status' => 0];
        }

        $payer_id = $data['payer']['payer_info']['payer_id'];
        $response = \App\Contexts\PayPal::execute_payment($data['id'], ['payer_id' => $payer_id]);

        if ($response->result->state !== 'approved') {
            Log::channel('webhooks')->error("subscription id for payment : {$data['id']} does not approved");
            return ['status' => 0];
        }

        $price = $data['transactions'][0]['amount']['total'];
        $currency = $data['transactions'][0]['amount']['currency'];
        //$fee = $data['transaction_fee']['value'];

        $subscribtion_data = [
            'created_by_hook' => true,
            'starts_at' => now()->setTimezone('Asia/Riyadh'),
            'ends_at' => now()->addYear(),
            'status' => ImageSubscription::STATUS_ACTIVE,
            'amount' => $price,
            'currency' => $currency,
            'data' => json_encode($data),
            'completed' => 1,
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_canceled_image($data)
    {
        $subscription = ImageSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_canceled_image not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'status' => ImageSubscription::STATUS_CANCEL,
            'renewal' => false,
        ];
        ImageSubscription::where('subscription_id', $data['id'])->update(['renewal' => 0]);
        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_suspended($data)
    {
        $subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_suspended not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'renewal' => false,
        ];
        Subscription::where('subscription_id', $data['id'])->update(['renewal' => 0]);
        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_suspended_image($data)
    {
        $subscription = ImageSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_suspended_image not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'renewal' => false,
        ];
        ImageSubscription::where('subscription_id', $data['id'])->update(['renewal' => 0]);
        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_activated($data)
    {
        $subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_activated not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }
        $subscribtion_data = [
            'status' => Subscription::STATUS_ACTIVE,
            'renewal' => true,
            'ends_at' => now()->addMonth(),
            'data' => json_encode($data),
            'completed' => 1,
        ];
        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_activated_image($data)
    {
        $subscription = ImageSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_activated_image not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'status' => ImageSubscription::STATUS_ACTIVE,
            'renewal' => true,
            'data' => json_encode($data),
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function payment_sale_refunded_package($payment_id, $payment_method = 2)
    {
        $subscription = Subscription::withoutGlobalScope('completed')->where('payment_id', $payment_id)->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_package_image not exist payment_id {$payment_id}");
            return ['status' => 0];
        }


        $subscription->update([
            'status' => Subscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);
        $subscription->downloads()->delete();
        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        $client_name = $subscription->user->name;
        // update pdf file refund
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund
        return ['status' => 1];
    }

    private function payment_sale_refunded_package_image($payment_id, $payment_method = 2)
    {
        $subscription = ImageSubscription::where('payment_id', $payment_id)->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_package_image not exist payment_id {$payment_id}");
            return ['status' => 0];
        }


        $subscription->update([
            'status' => ImageSubscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);

        // $subscriptions_ids =  ImageSubscription::where('subscription_id',$subscription->subscription_id)->pluck('id')->toArray();

        ImageDownload::where('subscription_id', $subscription->id)->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        // update pdf file refund
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund

        return ['status' => 0];
    }

    private function payment_sale_refunded_subscription($subscription_id, $payment_method = 2)
    {
        $subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $subscription_id)
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_subscription_image not exist subscription_id $subscription_id");
            return ['status' => 0];
        }

        $subscription->update([
            'status' => Subscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);
        Subscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);
        $subscription->downloads()->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method,
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        // update pdf file refund
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund

        return ['status' => 1];
    }

    private function payment_sale_refunded_subscription_image($subscription_id, $payment_method = 2)
    {
        $subscription = ImageSubscription::where('subscription_id', $subscription_id)
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_subscription_image not exist subscription_id $subscription_id");
            return ['status' => 0];
        }

        $subscription->update([
            'status' => ImageSubscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);
        ImageSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);
        ImageDownload::where('subscription_id', $subscription->id)->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method,
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        // update pdf file refund
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund

        return ['status' => 1];
    }

    private function payment_sale_completed_subscription_video($data)
    {
        $payer = PaypalPayer::where('subscription_id', $data['billing_agreement_id'])
            ->orderBy('id', 'desc')->first();
        if (!$payer) {
            Log::channel('webhooks')->error("payment_sale_completed_subscription_image not exist subscription_id {$data['billing_agreement_id']}");
            return ['status' => 0];
        }

        $user = User::findOrFail($payer->user_id);
        $plan = VideoPlan::findOrFail($payer->plan_id);

        $params['subscription_id'] = $data['billing_agreement_id'];
        $params['plan_id'] = $plan->id;
        $params['user_id'] = $user->id;
        $params['type'] = 'video';
        $params['geoip'] = geoip($payer->ip);
        $params['status'] = VideoSubscription::STATUS_ACTIVE;
        $params['starts_at'] = now()->setTimezone('Asia/Riyadh');
        $params['amount'] = $data['amount']['total'];
        $params['payment_gateway_fee'] = $data['transaction_fee']['value'];
        $params['payment_method_id'] = PaymentMethod::PAYPAL;
        $last_subscription = VideoSubscription::where(['subscription_id' => $data['billing_agreement_id']])
            ->orderBy('id', 'desc')
            ->first();


        if ($last_subscription && $last_subscription->ends_at) {
//            $params['starts_at'] = \Carbon\Carbon::parse($last_subscription->ends_at);
            $params['country_id'] = $last_subscription->country_id;
            $params['city_id'] = $last_subscription->city_id;
        }
        if ($plan->type == 'monthly')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addDays(30);
        elseif ($plan->type == 'annual')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addYear();
        if ($last_subscription)
            $last_subscription->update(['ends_at' => now()]);
        $subscription = \App\Contexts\Plans::store_subscription($user, $plan, $params);
        if (!$subscription) {
            Log::channel('webhooks')->error('Payment Processing');
        }

        // generate pdf file
        $subscription = VideoSubscription::where(['subscription_id' => $data['billing_agreement_id']])
            ->orderBy('id', 'desc')
            ->first();
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end pdf generation

        $payment_log = PaymentsLog::create([
            'user_id' => $payer->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'plan_id' => $payer->plan_id,
            'subscription_id' => $subscription ? $subscription->id : null,
            'category' => $this->is_type_hook,
            'data' => request()->all(),
        ]);

        return ['status' => 0];
    }

    private function payment_sale_completed_package_video($data)
    {
        $payment_id = $data['id'];
        if (isset($data['parent_payment'])) {
            $payment_id = $data['parent_payment'];
        }

        $subscription = VideoSubscription::with('user', 'plan')->where(['payment_id' => $payment_id])->first();
        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (request()->get('event_type') == 'PAYMENT.SALE.COMPLETED') {
            return ['status' => 0];
        }

        if (!$subscription) {
            Log::channel('webhooks')->error("subscription id {$data['id']} does not exist");
            return ['status' => 0];
        }

        if ($subscription->status == VideoSubscription::STATUS_ACTIVE) {
            return ['status' => 0];
        }

        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));


        $payer_id = $data['payer']['payer_info']['payer_id'];
        $response = \App\Contexts\PayPal::execute_payment($data['id'], ['payer_id' => $payer_id]);

        if ($response->result->state !== 'approved') {
            Log::channel('webhooks')->error("subscription id for payment : {$data['id']} does not approved");
            return ['status' => 0];
        }

        $price = $data['transactions'][0]['amount']['total'];
        $currency = $data['transactions'][0]['amount']['currency'];
        //$fee = $data['transaction_fee']['value'];

        $subscribtion_data = [
            'created_by_hook' => true,
            'starts_at' => now()->setTimezone('Asia/Riyadh'),
            'ends_at' => now()->addYear(),
            'status' => VideoSubscription::STATUS_ACTIVE,
            'amount' => $price,
            'currency' => $currency,
            'data' => json_encode($data),
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function payment_sale_refunded_subscription_video($subscription_id, $payment_method = 2)
    {
        $subscription = VideoSubscription::where('subscription_id', $subscription_id)
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_subscription_video not exist subscription_id $subscription_id");
            return ['status' => 0];
        }

        $subscription->update([
            'status' => VideoSubscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);
        VideoSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);
        VideoDownload::where('subscription_id', $subscription->id)->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method,
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        // update pdf file refund
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund

        return ['status' => 0];
    }

    private function payment_sale_refunded_package_video($payment_id, $payment_method = 2)
    {
        $subscription = VideoSubscription::where('payment_id', $payment_id)->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_package_video not exist payment_id {$payment_id}");
            return ['status' => 0];
        }

        $subscription->update([
            'status' => VideoSubscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);

        // $subscriptions_ids =  ImageSubscription::where('subscription_id',$subscription->subscription_id)->pluck('id')->toArray();

        VideoDownload::where('subscription_id', $subscription->id)->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method,
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        // update pdf file refund
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund
        return ['status' => 0];
    }

    private function billing_subscription_activated_video($data)
    {
        $subscription = VideoSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_activated_video not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'status' => VideoSubscription::STATUS_ACTIVE,
            'renewal' => true,
            'data' => json_encode($data),
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_suspended_video($data)
    {
        $subscription = VideoSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_suspended_video not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'renewal' => false,
        ];

        $subscription->update($subscribtion_data);
        VideoSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);
        return ['status' => 0];
    }

    private function billing_subscription_canceled_video($data)
    {
        $subscription = VideoSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_canceled_video not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'status' => VideoSubscription::STATUS_CANCEL,
            'renewal' => false,
        ];

        $subscription->update($subscribtion_data);
        VideoSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);

        return ['status' => 0];
    }

    private function payment_sale_completed_subscription_vector($data)
    {
        $payer = PaypalPayer::where('subscription_id', $data['billing_agreement_id'])
            ->orderBy('id', 'desc')->first();
        if (!$payer) {
            Log::channel('webhooks')->error("payment_sale_completed_subscription_image not exist subscription_id {$data['billing_agreement_id']}");
            return ['status' => 0];
        }


        $user = User::findOrFail($payer->user_id);
        $plan = VectorPlan::findOrFail($payer->plan_id);
        $params['subscription_id'] = $data['billing_agreement_id'];
        $params['plan_id'] = $payer->plan_id;
        $params['user_id'] = $payer->user_id;
        $params['geoip'] = geoip($payer->ip);
        // $params['geoip'] = '192.120.168.143';
        $params['status'] = VectorSubscription::STATUS_ACTIVE;
        $params['starts_at'] = now()->setTimezone('Asia/Riyadh');
        $params['amount'] = $data['amount']['total'];
        $params['payment_gateway_fee'] = $data['transaction_fee']['value'];
        $params['payment_method_id'] = PaymentMethod::PAYPAL;
        //  TODO research more;
        /* $params['currency'] = $data['amount']['currency']; */
        /* $params['sale_id'] = $data['id']; */

        $last_subscription = VectorSubscription::where(['subscription_id' => $data['billing_agreement_id']])
            ->orderBy('id', 'desc')
            ->first();

        if ($last_subscription && $last_subscription->ends_at) {
//            $params['starts_at'] = \Carbon\Carbon::parse($last_subscription->ends_at);
            $params['country_id'] = $last_subscription->country_id;
            $params['city_id'] = $last_subscription->city_id;
        }
        if ($plan->type == 'monthly')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addDays(30);
        elseif ($plan->type == 'annual')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addYear();
        if ($last_subscription)
            $last_subscription->update(['ends_at' => now()]);
        $subscription = \App\Contexts\Plans::store_subscription($user, $plan, $params);
        if (!$subscription) {
            Log::channel('webhooks')->error('Payment Processing');
        }

        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end pdf generation

        $payment_log = PaymentsLog::create([
            'user_id' => $payer->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'plan_id' => $payer->plan_id,
            'subscription_id' => $subscription ? $subscription->id : null,
            'category' => $this->is_type_hook,
            'data' => request()->all(),
        ]);

        return ['status' => 0];
    }

    private function payment_sale_completed_subscription($data)
    {
        $payer = PaypalPayer::where('subscription_id', $data['billing_agreement_id'])
            ->orderBy('id', 'desc')->first();

        if (!$payer) {
            Log::channel('webhooks')->error("payment_sale_completed_subscription not exist subscription_id {$data['billing_agreement_id']}");
            return ['status' => 0];
        }

        $params['geoip'] = geoip($payer->ip);
        $params['status'] = Subscription::STATUS_ACTIVE;
        $params['amount'] = $data['amount']['total'];
        $params['payment_gateway_fee'] = $data['transaction_fee']['value'];
        $params['starts_at'] = now()->setTimezone('Asia/Riyadh');

        $last_subscription = Subscription::withoutGlobalScope('completed')->where(['subscription_id' => $data['billing_agreement_id']])
            ->orderBy('id', 'desc')
            ->first();
        if (!$last_subscription)
            return ['status' => 0];
        $last_subscription->update($params);
        if ($last_subscription->payment_id)//first subscription payment
            $last_subscription->update(['payment_id' => $data['id']]);
        else {
            $params['subscription_id'] = $last_subscription->subscription_id;
            $params['payment_id'] = $data['id'];
            $params['plan_id'] = $last_subscription->plan_id;
            $params['user_id'] = $last_subscription->user_id;
            $params['status'] = Subscription::STATUS_ACTIVE;
            $params['starts_at'] = now()->setTimezone('Asia/Riyadh');
            $params['payment_method_id'] = PaymentMethod::PAYPAL;
            $params['completed'] = 1;
            $params['country_id'] = $last_subscription->country_id;
            $params['city_id'] = $last_subscription->city_id;
            $params['ip'] = $last_subscription->ip;
        }
        if ($last_subscription->plan_type == 'monthly')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addDays(30);
        elseif ($last_subscription->plan_type == 'annual')
            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addYear();
        if ($last_subscription)
            $last_subscription->update(['ends_at' => now()]);
        $subscription = \App\Contexts\Plans::store_subscription($last_subscription->user, $last_subscription->plan, $params);
        // generate pdf file
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end pdf generation

        $payment_log = PaymentsLog::create([
            'user_id' => $payer->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'plan_id' => $payer->plan_id,
            'subscription_id' => $last_subscription ? $last_subscription->id : null,
            'category' => $this->is_type_hook,
            'data' => request()->all(),
        ]);

        return ['status' => 1];
    }

    private function payment_sale_completed_package_vector($data)
    {
        $payment_id = $data['id'];
        if (isset($data['parent_payment'])) {
            $payment_id = $data['parent_payment'];
        }

        $subscription = VectorSubscription::with('user', 'plan')->where(['payment_id' => $payment_id])->first();
        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (request()->get('event_type') == 'PAYMENT.SALE.COMPLETED') {
            return ['status' => 0];
        }

        if (!$subscription) {
            Log::channel('webhooks')->error("subscription id {$data['id']} does not exist");
            return ['status' => 0];
        }

        if ($subscription->status == VectorSubscription::STATUS_ACTIVE) {
            return ['status' => 0];
        }

        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));


        $payer_id = $data['payer']['payer_info']['payer_id'];
        $response = \App\Contexts\PayPal::execute_payment($data['id'], ['payer_id' => $payer_id]);

        if ($response->result->state !== 'approved') {
            Log::channel('webhooks')->error("subscription id for payment : {$data['id']} does not approved");
            return ['status' => 0];
        }

        $price = $data['transactions'][0]['amount']['total'];
        $currency = $data['transactions'][0]['amount']['currency'];
        //$fee = $data['transaction_fee']['value'];

        $subscribtion_data = [
            'created_by_hook' => true,
            'starts_at' => now()->setTimezone('Asia/Riyadh'),
            'ends_at' => now()->addYear(),
            'status' => VectorSubscription::STATUS_ACTIVE,
            'amount' => $price,
            'currency' => $currency,
            'data' => json_encode($data),
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function payment_sale_refunded_subscription_vector($subscription_id, $payment_method = 2)
    {
        $subscription = VectorSubscription::where('subscription_id', $subscription_id)
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_subscription_vector not exist subscription_id $subscription_id");
            return ['status' => 0];
        }

        $subscription->update([
            'status' => VectorSubscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);
        VectorSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);

        VectorDownload::where('subscription_id', $subscription->id)->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method,
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        // update pdf file refund
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund
        return ['status' => 0];
    }

    private function payment_sale_refunded_package_vector($payment_id, $payment_method = 2)
    {
        $subscription = VectorSubscription::where('payment_id', $payment_id)->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("payment_sale_refunded_package_vector not exist payment_id {$payment_id}");
            return ['status' => 0];
        }


        $subscription->update([
            'status' => VectorSubscription::STATUS_REFUND,
            'ends_at' => now()->subMinutes(3),
            'download_remaining' => 0,
            'renewal' => 0,
        ]);

        // $subscriptions_ids =  ImageSubscription::where('subscription_id',$subscription->subscription_id)->pluck('id')->toArray();

        VectorDownload::where('subscription_id', $subscription->id)->delete();

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => $payment_method,
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);
        // update pdf file refund
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end update pdf file refund
        return ['status' => 0];
    }

    private function billing_subscription_activated_vector($data)
    {
        $subscription = VectorSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_activated_vector not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'status' => VectorSubscription::STATUS_ACTIVE,
            'renewal' => true,
            'data' => json_encode($data),
        ];

        $subscription->update($subscribtion_data);
        return ['status' => 0];
    }

    private function billing_subscription_suspended_vector($data)
    {
        $subscription = VectorSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_suspended_vector not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'renewal' => false,
        ];

        $subscription->update($subscribtion_data);
        VectorSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);

        return ['status' => 0];
    }

    private function billing_subscription_canceled_vector($data)
    {
        $subscription = VectorSubscription::where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_canceled_vector not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
            'status' => VectorSubscription::STATUS_CANCEL,
            'renewal' => false,
        ];

        $subscription->update($subscribtion_data);
        VectorSubscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);

        return ['status' => 0];
    }

    private function billing_subscription_canceled($data)
    {
        $subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $data['id'])
            ->orderBy('id', 'desc')->first();
        if (!$subscription) {
            Log::channel('webhooks')->error("billing_subscription_canceled not exist subscription_id {$data['id']}");
            return ['status' => 0];
        }

        $payment_log = PaymentsLog::create([
            'user_id' => optional($subscription)->user_id,
            'payment_method_id' => 2, // paypal
            'webhook_id' => request()->get('id'),
            'event_type' => request()->get('event_type'),
            'resource_type' => request()->get('resource_type'),
            'category' => $this->is_type_hook,
            'plan_id' => optional($subscription)->plan_id,
            'subscription_id' => optional($subscription)->id,
            'data' => request()->all(),
        ]);

        if (!$subscription) {
            return ['status' => 0];
        }

        $subscribtion_data = [
//            'status' => Subscription::STATUS_CANCEL,
            'renewal' => false,
        ];

        $subscription->update($subscribtion_data);
        Subscription::where('subscription_id', $subscription->subscription_id)->update(['renewal' => 0]);

        return ['status' => 0];
    }

    public function cpdf($id)
    {
        $last_subscription = VideoSubscription::with('user', 'plan')->where('id', $id)->orderBy('id', 'desc')->first();

        //  return $last_subscription ;
        $client_name = $last_subscription->user->name;
        if ($last_subscription->user->is_business) {
            $client_name = $client_name . ' (' . $last_subscription->user->company_name . ')';
        }
        dispatch(new \App\Jobs\CreateInvoicePDF($last_subscription));
    }

    public function handleSendgridWebhook(Request $request)
    {
        Log::channel('webhooks')->info('SendgridWebhook', $request->all());
        if (!$this->isValidSendgridSignature($request))
            return ['status' => 0, 'message' => 'Invalid Signature'];
        $events = collect($request->all())->whereIn('event', ['bounce', 'dropped', 'spamreport']);
        foreach ($events as $event) {
            if (@$event['email']) {
                BouncedEmail::firstOrCreate(
                    ['email' => $event['email']],
                    ['event' => $event['event']]
                );
            }
        }
        return ['status' => 1];
    }

    private function isValidSendgridSignature($request)
    {
        $publicKey = config('services.sendgrid.webhook_key');

        $eventWebhook = new EventWebhook();
        $ecPublicKey = $eventWebhook->convertPublicKeyToECDSA($publicKey);

        return $eventWebhook->verifySignature(
            $ecPublicKey,
            $request->getContent(),
            $request->header(EventWebhookHeader::SIGNATURE),
            $request->header(EventWebhookHeader::TIMESTAMP)
        );
    }

    public function handleStripeWebhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = @$_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        if (app()->isLocal()) {
            $data = \json_decode($payload, true);
            $event = Event::constructFrom($data);
        } else {
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $payload, $sig_header, config('cashier.webhook.secret')
                );
            } catch (\UnexpectedValueException $e) {
                // Invalid payload
                return response(['status' => 0, 'message' => $e->getMessage()], 400);
            } catch (SignatureVerificationException $e) {
                // Invalid payload
                return response(['status' => 0, 'message' => $e->getMessage()], 400);
            } catch (SignatureVerificationException $e) {
                // Invalid signature
                return response(['status' => 0, 'message' => $e->getMessage()], 400);
            }
        }
        Log::channel('webhooks')->info("Stripe WebHook ({$event->type}):" . json_encode(request()->all()));
        $user = @$event->data->object->customer ? User::where('stripe_id', $event->data->object->customer)->first() : null;
        if (in_array($event->type, [
            'payment_intent.succeeded',
            'customer.subscription.created',
            'customer.subscription.deleted',
            'charge.refunded',
        ]))
            PaymentsLog::create([
                'payment_method_id' => PaymentMethod::STRIPE, // paypal
                'user_id' => $user ? $user->id : null,
                'subscription_id' => @$event->data->object->metadata['subscription_id'],
                'webhook_id' => request()->get('id'),
                'event_type' => $event->type,
                'data' => request()->all(),
            ]);
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $object = $event->data->object;
                $subscription = Subscription::withoutGlobalScope('completed')->where('payment_id', $object->id)->first();
                if (!$subscription)
                    return ['status' => 0, 'message' => 'Subscription not found'];
                $payment_intent = PaymentIntent::retrieve([
                    'id' => $object->id,
                    'expand' => ['invoice.subscription'],
                ]);
//                dd($payment_intent->invoice->subscription->latest_invoice->payment_intent);
                if (@$payment_intent->invoice->subscription) {
                    Stripe::complete_subscription($payment_intent->invoice->subscription);
                } else {
                    Stripe::complete_payment($payment_intent);
                }
                break;
            case 'customer.subscription.created':
                $object = $event->data->object;
                return Stripe::complete_subscription($object);
            case 'customer.subscription.updated':
                /**@var $stripe_subscription \Stripe\Subscription */
                $stripe_subscription = $event->data->object;
                if (@$stripe_subscription->pause_collection->behavior == 'keep_as_draft') {
                    Subscription::withoutGlobalScope('completed')->where('subscription_id', $stripe_subscription->id)->update(['renewal' => 0]);
                    return ['status' => 1];
                }
                Stripe::complete_subscription($stripe_subscription);
                break;
            case 'invoice.payment_succeeded':
                /**@var $invoice Invoice */
                $invoice = $event->data->object;
                if ($invoice->subscription) {
                    if ($invoice->billing_reason == 'subscription_cycle') {
                        $last_subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $invoice->subscription)->firstOrFail();
                        $user = $last_subscription->user;
                        $plan = $last_subscription->plan;
                        $params['subscription_id'] = $invoice->subscription;
                        $params['payment_id'] = $invoice->payment_intent;
                        $params['plan_id'] = $plan->id;
                        $params['user_id'] = $user->id;
                        $params['status'] = Subscription::STATUS_ACTIVE;
                        $params['starts_at'] = now()->setTimezone('Asia/Riyadh');
                        $params['amount'] = $invoice->amount_paid / 100;
                        $params['payment_method_id'] = PaymentMethod::STRIPE;
                        $params['completed'] = 1;
                        $params['country_id'] = $last_subscription->country_id;
                        $params['city_id'] = $last_subscription->city_id;
                        $params['card_fingerprint'] = $last_subscription->card_fingerprint;
                        $params['ip'] = $last_subscription->ip;

                        if ($last_subscription && $last_subscription->ends_at) {
//                            $params['starts_at'] = \Carbon\Carbon::parse($last_subscription->ends_at);
                            $params['country_id'] = $last_subscription->country_id;
                            $params['city_id'] = $last_subscription->city_id;
                        }
                        if ($plan->type == 'monthly')
                            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addDays(30);
                        elseif ($plan->type == 'annual')
                            $params['ends_at'] = (new \Carbon\Carbon($params['starts_at']))->addYear();
                        if ($last_subscription)
                            $last_subscription->update(['ends_at' => now()]);
                        $subscription = \App\Contexts\Plans::store_subscription($user, $plan, $params);
                        if (!$subscription) {
                            Log::channel('webhooks')->error('Payment Processing');
                        }

                        $subscription->update([
                            'completed' => 1,
                            'status' => \App\Models\Subscription::STATUS_ACTIVE,
                            'starts_at' => now(),
                            'ends_at' => now()->addMonth(),
                        ]);
                        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
                    }
                }
                return ['status' => 1];
                break;
            case 'customer.subscription.deleted':
                $object = $event->data->object;
                $subscription = Subscription::withoutGlobalScope('completed')->findOrFail($object->metadata['subscription_id']);
                if ($subscription->subscription_id != $object->id)
                    return ['status' => 0];
                $resource = ['id' => $object->id];
                $this->billing_subscription_canceled($resource);
                break;
            case 'charge.dispute.closed':
                /**@var $dispute Dispute */
                $dispute = $event->data->object;
                $charge = \Stripe\Charge::retrieve([
                    'id' => $dispute->charge,
                    'expand' => ['invoice.subscription', 'payment_intent'],
                ]);
                if (@$charge->invoice->subscription)
                    $subscription = Subscription::withoutGlobalScope('completed')->findOrFail($charge->invoice->subscription->metadata['subscription_id']);
                else
                    $subscription = Subscription::withoutGlobalScope('completed')->findOrFail($charge->payment_intent->metadata['subscription_id']);
                if ($subscription->plan_type == 'package') {
                    return $this->payment_sale_refunded_package($charge->payment_intent->id, PaymentMethod::STRIPE);
                } else {
                    if ($subscription->subscription_id != $charge->invoice->subscription->id)
                        return ['status' => 0];

                    if ($subscription->subscription_id != $charge->invoice->subscription->id)
                        return ['status' => 0];
                    $resource = ['id' => $charge->invoice->subscription->id];
                    return $this->payment_sale_refunded_subscription($resource, PaymentMethod::STRIPE);
                }
                break;
            case 'charge.refunded':
                $object = $event->data->object;
                $charge = \Stripe\Charge::retrieve([
                    'id' => $object->id,
                    'expand' => ['invoice.subscription', 'payment_intent'],
                ]);
                if (@$charge->invoice->subscription)
                    $subscription = Subscription::withoutGlobalScope('completed')->findOrFail($charge->invoice->subscription->metadata['subscription_id']);
                else
                    $subscription = Subscription::withoutGlobalScope('completed')->findOrFail($charge->payment_intent->metadata['subscription_id']);
                if ($subscription->plan_type == 'package') {
                    $this->payment_sale_refunded_package($charge->payment_intent->id, PaymentMethod::STRIPE);
                } else {
                    if ($subscription->subscription_id != $charge->invoice->subscription->id)
                        return ['status' => 0];

                    if ($subscription->subscription_id != $charge->invoice->subscription->id)
                        return ['status' => 0];
                    $resource = ['id' => $charge->invoice->subscription->id];

                    $this->payment_sale_refunded_subscription($resource, PaymentMethod::STRIPE);
                }
                break;
            default:
                return response(['status' => 0, 'message' => 'Received unknown event type ' . $event->type]);
        }
        return response(['status' => 1]);
    }
}
