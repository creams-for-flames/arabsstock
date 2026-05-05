<?php

namespace App\Contexts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Contexts\Utils\Http;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

/*
 *
 * always check
 * response->info->error_code === 0
 * */

class PayPal
{
    public $paypal_mode;
    public $client_id;
    public $secret;
    public $base_url = "https://api.paypal.com";
    public $api_context;

    public function __construct()
    {
        $this->paypal_mode = env('PAYPAL_MODE') === "sandbox" ? "sandbox" : "live";
        if ($this->paypal_mode === "sandbox") {
            $this->client_id = env('PAYPAL_SANDBOX_CLIENT_ID');
            $this->secret = env('PAYPAL_SANDBOX_SECRET');
            $this->base_url = "https://api.sandbox.paypal.com";
        } else {
            $this->client_id = env('PAYPAL_LIVE_CLIENT_ID');
            $this->secret = env('PAYPAL_LIVE_SECRET');
            $this->base_url = "https://api.paypal.com";
        }
        $this->api_context = new ApiContext(
            new OAuthTokenCredential($this->client_id, $this->secret)
        );
        $this->api_context->setConfig(config('paypal.settings'));

    }

    private function get_authrization_value()
    {

        $access_token = Redis::get("paypal:{$this->client_id}:access_token");
        $token_type = Redis::get("paypal:{$this->client_id}:token_type");
        $expires_at = intval(Redis::get("paypal:{$this->client_id}:expires_at"));

        if (!$expires_at || $expires_at - 300 < time()) {
            $path = "v1/oauth2/token";
            $params = ["grant_type" => "client_credentials"];
            $headers = [
                // accept type
                "Accept: application/json",
                // content type
                "Content-Type: application/x-www-form-urlencoded",
                // lang
                "Accept-Language: en_US",
            ];
            $options = [
                'headers' => $headers,
                'curl_options' => [CURLOPT_USERPWD => "{$this->client_id}:{$this->secret}"],
            ];
            $response = Http::request("POST", "{$this->base_url}/{$path}", $params, $options)->call();

            $token_type = $response->result->token_type;
            $access_token = $response->result->access_token;
            $expires_at = intval($response->result->expires_in) + time();
            Redis::set("paypal:{$this->client_id}:token_type", $token_type);
            Redis::set("paypal:{$this->client_id}:access_token", $access_token);
            Redis::set("paypal:{$this->client_id}:expires_at", $expires_at);
        }

        return [
            'access_token' => $access_token,
            'token_type' => $token_type,
        ];
    }

    function commit($method, $path, $params = [], $headers = [])
    {
        $auth = $this->get_authrization_value();
        // dd($auth);
        $headers = array_merge(
            [
                // accept type
                "Accept: application/json",
                // Authorization
                "Authorization: {$auth['token_type']} {$auth['access_token']}",
            ],
            $headers
        );

        $response = Http::request($method, "{$this->base_url}/{$path}", $params, ['headers' => $headers])->call();
        return $response;
    }

    public static function get_config()
    {
        return new self();
    }

    public static function create_product($params)
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_create
        $object = new self();
        /* $params = [ */
        /*     'name' => 'image', // && video */
        /*     /1* 'description' => '', *1/ */
        /*     'type' => 'DIGITAL', */
        /*     'category' => 'DIGITAL_MEDIA_BOOKS_MOVIES_MUSIC', */
        /*     'image_url' => 'https://arabsstock.com/img/logo-en.png', */
        /*     'home_url' => 'https://arabsstock.com/photos', */
        /* ]; */
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/catalogs/products", $params, $headers);
    }

    public static function list_products()
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_list
        $object = new self();
        return $object->commit("GET", "v1/catalogs/products");
    }

    public static function get_product($product_id)
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_get
        $object = new self();
        return $object->commit("GET", "v1/catalogs/products/{$product_id}");
    }

    public static function update_product($product_id, $params)
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_patch
        $object = new self();
        /* $params = [ */
        /*     [ */
        /*         'op' => 'replace', // add, replace, remove */
        /*         'path' => '/description', */
        /*         'value' => '20 High Quality Image', */
        /*     ] */
        /* ]; */
        $headers = ["Content-Type: application/json"];
        return $object->commit("PATCH", "v1/catalogs/products/{$product_id}", $params, $headers);
    }

    public static function delete_product($product_id, $params)
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_patch
        $object = new self();
        /* $params = [ */
        /*     [ */
        /*         'op' => 'replace', // add, replace, remove */
        /*         'path' => '/description', */
        /*         'value' => '20 High Quality Image', */
        /*     ] */
        /* ]; */
        $headers = ["Content-Type: application/json"];
        return $object->commit("PATCH", "v1/catalogs/products/{$product_id}", $params, $headers);
    }

    public static function create_plan($params)
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#plans_create
        $object = new self();
        /* $params = [ */
        /*     "product_id" => "000001", */
        /*     "name" => "Basic Plan2", */
        /*     "description" => "Basic plan2", */
        /*     "billing_cycles" => [ */
        /*         [ */
        /*             "frequency" => [ */
        /*                 "interval_unit" => "MONTH", */
        /*                 "interval_count" => 1, */
        /*             ], */
        /*             "tenure_type" => "REGULAR", */
        /*             "sequence" => 1, */
        /*             "total_cycles" => 0, */
        /*             "pricing_scheme" => [ */
        /*                 "fixed_price" => [ */
        /*                     "value" => "10", */
        /*                     "currency_code" => "USD", */
        /*                 ], */
        /*             ], */
        /*         ], */
        /*     ], */
        /*     "payment_preferences" => [ */
        /*         "auto_bill_outstanding" => true, */
        /*         "setup_fee" => [ */
        /*             "value" => "10", */
        /*             "currency_code" => "USD", */
        /*         ], */
        /*         "setup_fee_failure_action" => "CONTINUE", */
        /*         "payment_failure_threshold" => 3, */
        /*     ], */
        /*     "taxes" => [ */
        /*         "percentage" => "10", */
        /*         "inclusive" => false, */
        /*     ], */
        /* ]; */

        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/billing/plans", $params, $headers);
    }

    public static function list_plans($page = 1)
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#plans_list
        $object = new self();
        return $object->commit("GET", "v1/billing/plans", ['page' => $page]);
    }

    public static function get_plan($plan_id)
    {
        $object = new self();
        return $object->commit("GET", "v1/billing/plans/{$plan_id}");
    }

    public static function create_subscription($params)
    {
        # please use paypal javascript sdk for better experience
        # https://developer.paypal.com/docs/subscriptions/integrate/#subscriptions-with-smart-payment-buttons
        $object = new self();
        /* $params = [ */
        /*     "plan_id" => "P-9U094116H5392951PL3MLUAY", */
        /*     "start_time" => now()->addhour()->startOfHour()->format('c'), */
        /*     "application_context" => [ */
        /*         "brand_name" => "Arabsstock", */
        /*         "locale" => "en-US", */
        /*         "shipping_preference" => "NO_SHIPPING", */
        /*         "user_action" => "SUBSCRIBE_NOW", */
        /*         "payment_method" => [ */
        /*             "payer_selected" => "PAYPAL", */
        /*             "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED", */
        /*         ], */
        /*         /1* local domains not working *1/ */
        /*         /1* "return_url" => route('paypal.subscribtion.status', ['plan_id' => 1]), *1/ */
        /*         /1* "cancel_url" => route('payment.paypal.success', ['plan_id' => 1]), *1/ */
        /*     ], */
        /* ]; */
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/billing/subscriptions", $params, $headers);
    }

    public static function get_subscription($subscription_id)
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_get
        $object = new self();
        return $object->commit("GET", "v1/billing/subscriptions/{$subscription_id}");
    }


    public static function execute_payment($payment_id, $params)
    {
        # please use paypal javascript sdk for better experience
        # https://developer.paypal.com/docs/paypal-plus/germany/integrate/execute-payment/#sample-request
        $object = new self();

        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/payments/payment/{$payment_id}/execute", $params, $headers);
    }


    public static function get_sale($sale_id)
    {
        $object = new self();
        return $object->commit("GET", "v1/payments/sale/{$sale_id}");
    }


    public static function refund_payment($sale_id, $params)
    {
        # please use paypal javascript sdk for better experience
        # https://developer.paypal.com/docs/paypal-plus/germany/integrate/execute-payment/#sample-request
        $object = new self();

        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/payments/sale/{$sale_id}/refund", $params, $headers);
    }

    public static function deactivate_plan($plan_id)
    {
        # https://developer.paypal.com/docs/subscriptions/full-integration/plan-management/#deactivate-plan
        $object = new self();
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/billing/plans/{$plan_id}/deactivate", [], $headers);
    }

    public static function suspend_subscription($subscription_id, $params)
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_suspend
        $object = new self();
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/billing/subscriptions/{$subscription_id}/suspend", $params, $headers);
    }

    public static function active_subscription($subscription_id, $params)
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_active
        $object = new self();
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/billing/subscriptions/{$subscription_id}/activate", $params, $headers);
    }


    public static function cancel_subscription($subscription_id, $params)
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_suspend
        $object = new self();
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/billing/subscriptions/{$subscription_id}/cancel", $params, $headers);
    }


    public static function create_webhook($params)
    {
        # https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_post
        $object = new self();
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/notifications/webhooks", $params, $headers);
    }

    public static function list_webhooks()
    {
        # https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_list
        $object = new self();
        return $object->commit("GET", "v1/notifications/webhooks");
    }

    public static function get_webhook($webhook_id)
    {
        # https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_get
        $object = new self();
        return $object->commit("GET", "v1/notifications/webhooks/{$webhook_id}");
    }

    public static function update_webhook($webhook_id, $params)
    {
        # https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_update
        $object = new self();
        /* $params = [ */
        /*     [ */
        /*         'op' => 'replace', // add, replace, remove */
        /*         'path' => '/url', */
        /*         'value' => 'https://example.com/example_webhook2', */
        /*     ] */
        /* ]; */
        $headers = ["Content-Type: application/json"];
        return $object->commit("PATCH", "v1/notifications/webhooks/{$webhook_id}", $params, $headers);
    }

    public static function delete_webhook($webhook_id)
    {
        # https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_delete
        $object = new self();
        $headers = ["Content-Type: application/json"];
        return $object->commit("DELETE", "v1/notifications/webhooks/{$webhook_id}", [], $headers);
    }


    public static function list_payments()
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_get
        $object = new self();
        return $object->commit("GET", "v1/payments/payment");
    }


    public static function get_payment($payment_id)
    {
        # https://developer.paypal.com/docs/api/catalog-products/v1/#products_get
        $object = new self();
        return $object->commit("GET", "v1/payments/payment/{$payment_id}");
    }

    public static function create_payout($params)
    {
        #https://developer.paypal.com/docs/payouts/integrate/api-integration/#create-payout
        $object = new self();
        // $params = [
        //     'sender_batch_header'=> [
        //         'sender_batch_id'=> "2011021801",
        //         'email_subject' => "You have a payout!" ,
        //         'email_message' => "You have received a payout! Thanks for using our service!",
        //     ],
        //     'items' => [
        //         [
        //             'recipient_type' => 'EMAIL',
        //             'amount' => [
        //                 'value' => "10",
        //                 'currency'=> 'USD',
        //             ],
        //             'note' => 'Thanks for your patronage!',
        //             'sender_item_id' => '201403140004',
        //             'receiver' => 'ahedeid-buyer@gmail.com',
        //         ],
        //     ]
        // ];
        // dd($params);
        $headers = ["Content-Type: application/json"];
        return $object->commit("POST", "v1/payments/payouts", $params, $headers);
    }


    public static function show_payout_details($payout_batch_id)
    {
        # https://developer.paypal.com/docs/payouts/integrate/api-integration/#show-payout-details
        $object = new self();
        $headers = ["Content-Type: application/json"];
        //['fields' => 'batch_header'],
        return $object->commit("GET", "v1/payments/payouts/{$payout_batch_id}", $headers);
    }

    public static function show_payout_item_details($payout_item_id)
    {
        # https://developer.paypal.com/docs/payouts/integrate/api-integration/#show-payout-details
        $object = new self();
        $headers = ["Content-Type: application/json"];

        return $object->commit("GET", "v1/payments/payouts-item/{$payout_item_id}", $headers);
    }

    public static function get_subscription_payments($subscription_id, $params = [])
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_transactions
        $object = new self();
        $headers = [
            'Content-Type' => 'application/json',
            'start_time' => now()->subMonth()->format('c'),
            'end_time' => now()->format('c'),
        ];
        $headers = array_merge($headers, $params);
        return $object->commit("GET", "v1/billing/subscriptions/{$subscription_id}/transactions", $headers);
    }

    public static function get_subscriptions($params = [])
    {
        # https://developer.paypal.com/docs/api/subscriptions/v1/#subscriptions_transactions
        $object = new self();
        return $object->commit("GET", "v1/billing/subscriptions", [
            'Content-Type' => 'application/json',
            'start_time' => now()->subMonth()->format('c'),
            'end_time' => now()->format('c'),
        ]);
    }


}
