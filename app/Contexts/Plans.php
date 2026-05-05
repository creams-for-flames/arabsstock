<?php

namespace App\Contexts;

use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Promocode;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Redis;
use App\Models\ImagePlan;
use App\Models\VideoPlan;
use App\Models\VectorPlan;


class Plans
{
    public static function create_paypal_product_for_image()
    {
        $client_id = PayPal::get_config()->client_id;
        $product = Redis::hgetall("paypal:{$client_id}:product:1");
        if (!empty($product)) {
            return $product;
        }

        // check if created before
        $response = \App\Contexts\PayPal::list_products();

        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $products = collect($response->result->products);
        $product = $products->keyBy("id")->get("000001");

        if (!$product) {
            // create new
            $params = [
                'id' => sprintf('%06d', 1),
                'name' => 'Image',
                'type' => 'DIGITAL',
                'category' => 'DIGITAL_MEDIA_BOOKS_MOVIES_MUSIC',
                'image_url' => 'https://arabsstock.com' . '/img/logo-en.png',
                'home_url' => 'https://arabsstock.com' . '/photos',
            ];

            $response = \App\Contexts\PayPal::create_product($params);

            if ($response->error_code !== 0) {
                abort(500, $response->error);
            }
            $product = $response->result;
        }
        $product = (array)$product;
        $product['description'] = isset($product['description']) ?: "";
        unset($product['links']);

        Redis::hmset("paypal:{$client_id}:product:1", $product);
        return $product;
    }

    public static function create_paypal_product_for_video()
    {
        $client_id = PayPal::get_config()->client_id;
        $product = Redis::hgetall("paypal:{$client_id}:product:2");

        if (!empty($product)) {
            return $product;
        }
        // check if created before
        $response = \App\Contexts\PayPal::list_products();
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $products = collect($response->result->products);
        $product = $products->keyBy("id")->get("000002");

        if (!$product) {
            // create new
            $params = [
                'id' => sprintf('%06d', 2),
                'name' => 'Video',
                'type' => 'DIGITAL',
                'category' => 'DIGITAL_MEDIA_BOOKS_MOVIES_MUSIC',
                'image_url' => 'https://arabsstock.com' . '/img/logo-en.png',
                'home_url' => 'https://arabsstock.com' . '/videos',
            ];

            $response = \App\Contexts\PayPal::create_product($params);
            if ($response->error_code !== 0) {
                abort(500, $response->error);
            }
            $product = $response->result;
        }
        $product = (array)$product;
        $product['description'] = isset($product['description']) ?: "";
        unset($product['links']);

        Redis::hmset("paypal:{$client_id}:product:2", $product);
        return $product;
    }

    public static function create_paypal_product_for_vector()
    {
        $client_id = PayPal::get_config()->client_id;
        $product = Redis::hgetall("paypal:{$client_id}:product:3");
        if (!empty($product)) {
            return $product;
        }

        // check if created before
        $response = \App\Contexts\PayPal::list_products();
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $products = collect($response->result->products);
        $product = $products->keyBy("id")->get("000003");

        if (!$product) {
            // create new
            $params = [
                'id' => sprintf('%06d', 3),
                'name' => 'Vector',
                'type' => 'DIGITAL',
                'category' => 'DIGITAL_MEDIA_BOOKS_MOVIES_MUSIC',
                'image_url' => 'https://arabsstock.com' . '/img/logo-en.png',
                'home_url' => 'https://arabsstock.com' . '/vectors',
            ];

            $response = \App\Contexts\PayPal::create_product($params);
            if ($response->error_code !== 0) {
                abort(500, $response->error);
            }
            $product = $response->result;
        }
        $product = (array)$product;
        $product['description'] = isset($product['description']) ?: "";
        unset($product['links']);

        Redis::hmset("paypal:{$client_id}:product:3", $product);
        return $product;
    }

    public static function create_paypal_product_for_flex()
    {
        $client_id = PayPal::get_config()->client_id;
        $product = Redis::hgetall("paypal:{$client_id}:product:4");
        if (!empty($product)) {
            return $product;
        }

        // check if created before
        $response = \App\Contexts\PayPal::list_products();
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $products = collect($response->result->products);
        $product = $products->keyBy("id")->get("000004");

        if (!$product) {
            // create new
            $params = [
                'id' => sprintf('%06d', 4),
                'name' => 'Flex',
                'type' => 'DIGITAL',
                'category' => 'DIGITAL_MEDIA_BOOKS_MOVIES_MUSIC',
                'image_url' => 'https://arabsstock.com' . '/img/logo-en.png',
                'home_url' => 'https://arabsstock.com' . '/',
            ];

            $response = \App\Contexts\PayPal::create_product($params);
            if ($response->error_code !== 0) {
                abort(500, $response->error);
            }
            $product = $response->result;
        }
        $product = (array)$product;
        $product['description'] = isset($product['description']) ?: "";
        unset($product['links']);

        Redis::hmset("paypal:{$client_id}:product:4", $product);
        return $product;
    }


    public static function update_paypal_plan($plan)
    {
        // TODO
    }

    public static function disable_paypal_plan($plan)
    {

        $response = \App\Contexts\PayPal::deactivate_plan($plan->id);

        $client_id = PayPal::get_config()->client_id;
        $paypal_plan = Redis::del("paypal:{$client_id}:plan:{$plan->id}");

        return $response;
    }

    public static function disable_paypal_plans()
    {
        $plans = Paypal::list_plans()->result->plans;

        foreach ($plans as $plan) {
            self::disable_paypal_plan($plan);
        }
    }

    public static function create_paypal_subscription($plan, $type = 'flex')
    {
        \Illuminate\Support\Facades\Log::channel('info')->info("#6 subscribe:create_paypal_subscription plan_id {$plan->paypal_plan}  type {$type} " . '--auth:' . auth()->id());
        $promocode = session()->has('promocode') ? \App\Models\Promocode::whereHas('plans', function ($q) use ($plan) {
            $q->where('plans.id', $plan->id);
        })->find(session()->get('promocode')) : null;
        if ($promocode && ($promocode->max_usage <= $promocode->subscriptions()->where('user_id', auth()->id())->count()))
            $promocode = null;
        $plan_id = $plan->paypal_plan;
        if ($promocode)
            $plan_id = $promocode->plans()->find($plan->id)->pivot->paypal_plan ?: $plan->paypal_plan;
        $params = [
            "plan_id" => $plan_id,
            /* "start_time" => '', so paypal set current time */
            "application_context" => [
                "brand_name" => "Arabsstock",
                "locale" => "en-US",
                "shipping_preference" => "NO_SHIPPING",
                "user_action" => "SUBSCRIBE_NOW",
                "payment_method" => [
                    "payer_selected" => "PAYPAL",
                    "payee_preferred" => "IMMEDIATE_PAYMENT_REQUIRED",
                ],
                "return_url" => route('paypal.subscribtion.status', ['plan_id' => $plan->id, 'resource' => $type, 'redirect' => route("me.plans")]),
                "cancel_url" => route('payment.fail', ['status' => 'failed', 'plan_id' => $plan->id, 'redirect' => route("plans")]),
            ],
        ];
        $params['type'] = $type;

        $response = \App\Contexts\PayPal::create_subscription($params);
        if ($response->error_code !== 0) {
            \Illuminate\Support\Facades\Log::channel('info')->error("#0 subscribe: error code " . json_encode($response) . "  create_subscription   type {$type} params " . json_encode($params) . '--auth:' . auth()->id());

            abort(500, $response->error);
        }
        $approval_url = $response->result->links[0]->href;
        $geoip = geoip(request()->ip());
        $subscription = Plans::store_subscription(auth()->user(), $plan, [
            'payment_method_id' => PaymentMethod::PAYPAL,
            'status' => Subscription::STATUS_PENDING,
            'subscription_id' => $response->result->id,
            'country_id' => @$geoip['country_id'],
            'city_id' => @$geoip['city_id'],
            'ip' => request()->ip(),
            'promocode' => $promocode,
        ]);
        \Illuminate\Support\Facades\Log::channel('info')->info("#7 subscribe:create_subscription params approval_url: {$approval_url}  type {$type} " . '--auth:' . auth()->id());

        return $approval_url;
    }

    public static function store_subscription(User $user, $plan, $params)
    {
        $amount = $plan->price;
        $promocode = @$params['promocode'];
        if ($promocode) {
            $amount = $promocode->calculate_price($plan->price);
        }
        $subscription_data = array_filter([
            'quantity' => 1,
            'plan_id' => $plan->id,
            'created_by_hook' => 0,
            'download_remaining' => $plan->downloads_count,
            'remaining_credits' => $plan->credits_count,
            'credits' => $plan->credits_count,
            'credit_price' => $plan->credits_count ? ($plan->price / $plan->credits_count) : 0,
            'plan_type' => $plan->type,
            'amount' => $amount,
            'currency' => 'USD',
            'renewal' => 1,
            'data' => "",
            'start_period' => $plan->type == 'annual' ? now() : null,
            'promocode_id' => optional(@$promocode)->id,
            'plan_price' => $plan->price,
        ]);
        if (count($params))
            $subscription_data = array_merge($subscription_data, $params);
        if (get_class($plan) == Plan::class)
            return $user->subscriptions()->create($subscription_data);
        $user_relation = "{$plan->content_type}_subscriptions";
        return $user->{$user_relation}()->create($subscription_data);
    }

    public static function store_package_subscription(User $user, $plan, $params)
    {
        if ($plan->type != 'package')
            abort(404);

        $amount = $total = $plan->on_demand ? \App\Models\Plan::onDemandCreditsPrice($plan->credits_count) * $plan->credits_count : $plan->price;
        $promocode = @$params['promocode'];
        if ($promocode) {
            $total = $promocode->calculate_price($plan->price);
        }
        $subscription_data = [
            'ends_at' => now()->addYear(),
            'plan_id' => $plan->id,
            'quantity' => 1,
            'created_by_hook' => 0,
            'remaining_credits' => $plan->credits_count,
            'credits' => $plan->credits_count,
            'credit_price' => $amount / $plan->credits_count,
            'plan_type' => $plan->type,
            'currency' => 'USD',
            'amount' => $total,
            'plan_price' => $amount,
            'renewal' => 0,
            'promocode_id' => optional(@$promocode)->id,
        ];
        if (count($params))
            $subscription_data = array_merge($subscription_data, $params);
        if (get_class($plan) == Plan::class)
            return $user->subscriptions()->create($subscription_data);
        $user_relation = "{$plan->content_type}_subscriptions";
        return $user->{$user_relation}()->create($subscription_data);
    }

    public static function initialize_plans_images()
    {
        $plans = ImagePlan::where('type', '!=', 'package')
            ->where('status', 1)
            ->whereNull('paypal_plan')
            ->get();

        foreach ($plans as $plan) {
            self::create_paypal_plan_image($plan);
        }

    }

    public static function initialize_plans_vectors()
    {
        $plans = VectorPlan::where('type', '!=', 'package')
            ->whereNull('paypal_plan')
            ->where('status', 1)
            ->get();
        foreach ($plans as $plan) {
            self::create_paypal_plan_vector($plan);
        }
    }

    public static function initialize_plans_videos()
    {
        $plans = VideoPlan::where('type', '!=', 'package')
            ->whereNull('paypal_plan')
            ->where('status', 1)
            ->get();
        foreach ($plans as $plan) {
            self::create_paypal_plan_video($plan);
        }

    }

    public static function initialize_plans_flex()
    {
        $plans = Plan::where('type', '!=', 'package')
            ->whereNull('paypal_plan')
            ->where('status', 1)
            ->get();
        foreach ($plans as $plan) {
            self::create_paypal_plan_flex($plan);
        }
    }

    public static function create_paypal_plan_image($plan)
    {
        $client_id = PayPal::get_config()->client_id;
        $paypal_plan = Redis::hgetall("paypal:{$client_id}:plan:{$plan->id}");
        if (!empty($paypal_plan)) {
            return $paypal_plan;
        }

        // @note allow admin to create new plans
        // @note allow admin to create new yearly plans
        self::create_paypal_product_for_image();

        $params = [
            'product_id' => sprintf('%06d', 1),
            'name' => $plan->title_en,
            'description' => "#Arabsstock{$plan->id}",
            "billing_cycles" => [
                [
                    "frequency" => [
                        "interval_unit" => strtoupper($plan->frequency),
                        "interval_count" => 1,
                    ],
                    "tenure_type" => "REGULAR",
                    "sequence" => 1,
                    "total_cycles" => 0,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $plan->price,
                            "currency_code" => "USD",
                        ],
                    ],
                ],
            ],
            "payment_preferences" => [
                "auto_bill_outstanding" => false,
                "setup_fee" => [
                    "value" => 0,
                    "currency_code" => "USD",
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 3,
            ],
            /* "taxes" => [ */
            /*     "percentage" => "10", */
            /*     "inclusive" => false, */
            /* ], */
        ];


        $response = \App\Contexts\PayPal::create_plan($params);
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $paypal_plan = (array)$response->result;
        unset($paypal_plan['links']);
        unset($paypal_plan['billing_cycles']);
        unset($paypal_plan['payment_preferences']);
        unset($paypal_plan['taxes']);
        $plan->update(['paypal_plan' => $paypal_plan['id']]);
        Redis::hmset("paypal:{$client_id}:plan:{$plan->id}", $paypal_plan);

        return $paypal_plan;
    }

    public static function create_paypal_plan_video($plan)
    {
        $client_id = PayPal::get_config()->client_id;

        // @note allow admin to create new plans
        // @note allow admin to create new yearly plans

        self::create_paypal_product_for_video();

        $params = [
            'product_id' => sprintf('%06d', 2),
            'name' => $plan->title_en,
            'description' => "#Arabsstock{$plan->id}",
            "billing_cycles" => [
                [
                    "frequency" => [
                        "interval_unit" => strtoupper($plan->frequency),
                        "interval_count" => 1,
                    ],
                    "tenure_type" => "REGULAR",
                    "sequence" => 1,
                    "total_cycles" => 0,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $plan->price,
                            "currency_code" => "USD",
                        ],
                    ],
                ],
            ],
            "payment_preferences" => [
                "auto_bill_outstanding" => false,
                "setup_fee" => [
                    "value" => 0,
                    "currency_code" => "USD",
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 3,
            ],
            /* "taxes" => [ */
            /*     "percentage" => "10", */
            /*     "inclusive" => false, */
            /* ], */
        ];


        $response = \App\Contexts\PayPal::create_plan($params);
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $paypal_plan = (array)$response->result;
        unset($paypal_plan['links']);
        unset($paypal_plan['billing_cycles']);
        unset($paypal_plan['payment_preferences']);
        unset($paypal_plan['taxes']);
        $plan->update(['paypal_plan' => $paypal_plan['id']]);
        Redis::hmset("paypal:{$client_id}:plan:{$plan->id}", $paypal_plan);

        return $paypal_plan;
    }

    public static function create_paypal_plan_vector($plan)
    {
        $client_id = PayPal::get_config()->client_id;

        // @note allow admin to create new plans
        // @note allow admin to create new yearly plans

        self::create_paypal_product_for_vector();

        $params = [
            'product_id' => sprintf('%06d', 3),
            'name' => $plan->title_en,
            'description' => "#Arabsstock{$plan->id}",
            "billing_cycles" => [
                [
                    "frequency" => [
                        "interval_unit" => strtoupper($plan->frequency),
                        "interval_count" => 1,
                    ],
                    "tenure_type" => "REGULAR",
                    "sequence" => 1,
                    "total_cycles" => 0,
                    "pricing_scheme" => [
                        "fixed_price" => [
                            "value" => $plan->price,
                            "currency_code" => "USD",
                        ],
                    ],
                ],
            ],
            "payment_preferences" => [
                "auto_bill_outstanding" => false,
                "setup_fee" => [
                    "value" => 0,
                    "currency_code" => "USD",
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 3,
            ],
            /* "taxes" => [ */
            /*     "percentage" => "10", */
            /*     "inclusive" => false, */
            /* ], */
        ];


        $response = \App\Contexts\PayPal::create_plan($params);
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $paypal_plan = (array)$response->result;
        unset($paypal_plan['links']);
        unset($paypal_plan['billing_cycles']);
        unset($paypal_plan['payment_preferences']);
        unset($paypal_plan['taxes']);
        $plan->update(['paypal_plan' => $paypal_plan['id']]);
        Redis::hmset("paypal:{$client_id}:plan:{$plan->id}", $paypal_plan);

        return $paypal_plan;
    }

    public static function create_paypal_plan_flex($plan)
    {
        $client_id = PayPal::get_config()->client_id;

        // @note allow admin to create new plans
        // @note allow admin to create new yearly plans

        self::create_paypal_product_for_flex();
        $billing_cycles = [];
        if ($plan->trial_days)
            $billing_cycles[] = [
                "frequency" => [
                    "interval_unit" => 'MONTH',
                    "interval_count" => 1,
                ],
                "tenure_type" => "TRIAL",
                "sequence" => 1,
                "total_cycles" => 1,
                "pricing_scheme" => [
                    "fixed_price" => [
                        "value" => 0,
                        "currency_code" => "USD",
                    ],
                ],
            ];
        $billing_cycles[] = [
            "frequency" => [
                "interval_unit" => strtoupper($plan->frequency),
                "interval_count" => 1,
            ],
            "tenure_type" => "REGULAR",
            "sequence" => count($billing_cycles) + 1,
            "total_cycles" => 0,
            "pricing_scheme" => [
                "fixed_price" => [
                    "value" => $plan->price,
                    "currency_code" => "USD",
                ],
            ],
        ];
        $params = [
            'product_id' => sprintf('%06d', 4),
            'name' => $plan->title_en,
            'description' => "#Arabsstock{$plan->id}",
            "billing_cycles" => $billing_cycles,
            "payment_preferences" => [
                "auto_bill_outstanding" => false,
                "setup_fee" => [
                    "value" => 0,
                    "currency_code" => "USD",
                ],
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 3,
            ],
        ];

        $response = \App\Contexts\PayPal::create_plan($params);
        if ($response->error_code !== 0) {
            abort(500, $response->error);
        }
        $paypal_plan = (array)$response->result;
        unset($paypal_plan['links']);
        unset($paypal_plan['billing_cycles']);
        unset($paypal_plan['payment_preferences']);
        unset($paypal_plan['taxes']);
        $plan->update(['paypal_plan' => $paypal_plan['id']]);
        Redis::hmset("paypal:{$client_id}:plan:{$plan->id}", $paypal_plan);

        return $paypal_plan;
    }


}
