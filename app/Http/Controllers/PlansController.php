<?php

namespace App\Http\Controllers;

use App\Contexts\Plans;
use App\Contexts\Stripe;
use App\Jobs\CreateInvoicePDF;
use App\Models\FlexPlan;
use App\Models\FlexSubscription;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Promocode;
use App\Models\Subscription;
use App\Models\User;
use App\ScheduledUnsubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ImageSubscription;
use App\Models\VectorSubscription;
use Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\InputFields;
use PayPal\Api\WebProfile;
use PayPal\Api\Payer;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use App\Models\PaypalPayer;
use App\Models\VideoSubscription;
use Stripe\Customer;
use Stripe\Exception\CardException;
use Stripe\Invoice;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;

class PlansController extends Controller
{
    private $apiContext;
    private $mode;
    private $client_id;
    private $secret;

    // Create a new instance with our paypal credentials
    public function __construct()
    {
        // Detect if we are running in live mode or sandbox
        if (config('paypal.settings.mode') == 'live') {
            $this->client_id = config('paypal.live_client_id');
            $this->secret = config('paypal.live_secret');
        } else {
            $this->client_id = config('paypal.sandbox_client_id');
            $this->secret = config('paypal.sandbox_secret');
        }

        // Set the Paypal API Context/Credentials
        $this->apiContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->secret));
        $this->apiContext->setConfig(config('paypal.settings'));
    }

    function index()
    {
        /**@var $subscription_plans Collection */
        $subscription_plans = Plan::where('status', 1)->where('hidden', 0)
            ->whereDoesntHave('subscriptions', function ($q) {
                $q->where('user_id', auth()->id())->where('subscriptions.status', Subscription::STATUS_ACTIVE)
                    ->where('renewal', 1);
            })
            ->whereIn('type', ['monthly', 'annual'])
            ->get();

        if (!auth()->check() or (auth()->check() && auth()->user()->subscriptions()->count() == 0)) {
            $free_trial = Plan::where('trial_days', '>', 0)->first();

            if ($free_trial)
                $subscription_plans->add($free_trial);
        }
        $subscription_plans = $subscription_plans/*->sortBy('credits_count')->sortBy('id')*/ ->groupBy('credits_count')->sortKeys();
        $package_plans = Plan::where('status', 1)
            ->whereIn('type', ['package'])
            ->orderBy('credits_count')
            ->where('on_demand', 0)
            ->where('for_teams', 0)
            ->where('hidden', 0)
            ->get()->groupBy('credits_count');
        $on_demand_plan = Plan::where('status', 1)
            ->whereIn('type', ['package'])
            ->where('on_demand', 1)
            ->orderBy('id', 'desc')
            ->where('hidden', 0)
            ->take(1)
            ->first();
        return view('plans.index', compact('subscription_plans', 'package_plans', 'on_demand_plan'));
    }


    public function subscribe(Request $request)
    {
        if (!$request->plan_id)
            abort(404);
        $plan_id = intval($request->plan_id);
        $subscription = Subscription::join('plans', 'plans.id', 'subscriptions.plan_id')
            ->where([
                ['plan_id', '=', $plan_id],
                ['remaining_credits', '>', 0],
                ['user_id', '=', Auth::id()],
                ['ends_at', '>=', date('Y-m-d H:i:s')],
                ['plans.type', '=', 'monthly'], // تم اضافة شرط أن تكون الحزمة شهرية لمنع الاشتراك
            ])
            ->whereIn('subscriptions.status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_PENDING])
            ->first();

        if ($subscription) {
            request()
                ->session()
                ->flash('your_are_subscribed_already', true);
            return redirect()->back()->send();
        }
        $plan = Plan::where(['status' => 1])->findOrFail($plan_id);
        return redirect()->away($this->createSubscription($plan));
    }

    public function executePaymentPaypalPackage(Request $request)
    {
        // dd($request->all());
        $payment_id = $request->paymentId;
        if (empty($request->PayerID) || empty($request->token)) {
            return redirect()->route('payment.fail');
        }

        $user = Auth::user();
        $resource = $request->get('resource');
        PaypalPayer::create([
            'payer_id' => $request->get('PayerID', ''),
            'subscription_id' => $request->get('paymentId', ''),
            'user_id' => $user->id,
            'plan_id' => $request->get('plan_id', 0),
            'resource' => $resource,
            'ip' => request()->ip(),
            'status' => 1,
            'notes' => '',
        ]);

        $subscription = Subscription::withoutGlobalScope('completed')->where(['payment_id' => $payment_id, 'status' => Subscription::STATUS_PENDING])->first();

        if (!$subscription) {
            return redirect()->route('landPage');
        }

        try {
            $response = \App\Contexts\PayPal::execute_payment($payment_id, ['payer_id' => $request->PayerID]);
        } catch (\Exception $e) {
            return redirect()->route('payment.fail', ['redirect' => route("plans")]);
        }

        if (!$response->result || $response->result->state != "approved") {
            return redirect()->route('payment.fail', ['redirect' => route("plans")]);
        }
        if (@$response->result->transactions[0]->related_resources[0]->sale->state != 'completed')
            return redirect()->route('payment.fail', ['redirect' => route("plans")]);


        $plan = Plan::where(['id' => $request->plan_id])->firstOrFail();
        $subscription_data = [
            'payment_method_id' => 2,
            // 'payment_id' => $payment_id,
            // 'user_id'    => $user->id,
            // 'plan_id'            => $plan->id,
            'plan_type' => $plan->type,
            'created_by_hook' => false,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'status' => Subscription::STATUS_ACTIVE,
            'amount' => $response->result->transactions[0]->amount->total,
            'currency' => $response->result->transactions[0]->amount->currency,
            'data' => json_encode($response),
            'renewal' => false,
            'remaining_credits' => $plan->credits_count,
            'credits' => $plan->credits_count,
            'completed' => 1,
        ];

        $subscription->update($subscription_data);
        $client_name = $subscription->user->name;
        if ($subscription->user->is_business) {
            $client_name = $client_name . ' (' . $subscription->user->company_name . ')';
        }
        // generate pdf file
        dispatch(new \App\Jobs\CreateInvoicePDF($subscription));
        // end pdf generation
        return redirect()->route('payment.success', ['subscription' => $subscription->id, 'redirect' => route("me.plans")]);
    }

    private function trigger_cjevent($subscription)
    {
        if (!\request()->hasCookie('cje'))
            return;
        $curl = curl_init();
        $data = [
            'CID' => 1563613,
            'TYPE' => 429409,
            'METHOD' => 'S2S',
            'SIGNATURE' => '61d210142e170eddce66dcfeb7ede9b1',
            'CJEVENT' => \request()->cookie('cje'),
            'eventTime' => Carbon::now()->format('c'),
            'OID' => $subscription->order_id,
            'currency' => 'USD',
            'ITEM1' => \Illuminate\Support\Str::slug("{$subscription->plan->title_en} {$subscription->plan->id}"),
            'AMT1' => $subscription->amount,
            'QTY1' => 1,
            'DCNT1' => 0,
            'discount' => 0,
        ];
        \Log::channel('info')->info('cje', $data);
        \Log::channel('info')->info('cje', ['link' => 'https://www.emjcd.com/u?' . http_build_query($data)]);
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.emjcd.com/u?' . http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

    }

    public function paymentSuccess(Request $request)
    {
        if ($request->subscription) {
            $subscription = Subscription::with('plan')->find(intval($request->subscription));
            if ($subscription)
                $this->trigger_cjevent($subscription);
        }
        session()->forget('on_demand_subscription');
        return view('payment-callback', ['status' => true, 'subscription' => @$subscription]);
    }


    public function paymentFail(Request $request)
    {
        $message_error = 'error : Payment failed';
        if (Auth::check()) {
            $message_error .= ' user : ' . Auth::id();
            if ($request->get('subscription_id')) {
                $this->save_paypal_payer([
                    'subscription_id' => $request->get('subscription_id'),
                    'user_id' => Auth::id(),
                    'plan_id' => $request->get('plan_id'),
                    'type' => $request->get('type'),
                    'ip' => request()->ip(),
                ]);
            }

        }

        \Log::channel('info')->info($message_error . ' Payload : ' . json_encode($request->all()));

        return view('payment-callback', ['status' => false]);

    }

    private function save_paypal_payer($params)
    {
        // TODO process all paypal request in background
        $response = \App\Contexts\PayPal::get_subscription($params['subscription_id']);
        if ($response->error_code !== 0) {
            \Log::error('Paypal Error paypalFail: ' . json_encode($params));
        }
        $paypal_subscription = $response->result;
        $payer_id = 0;
        try {
            $payer_id = $paypal_subscription->subscriber->payer_id;
        } catch (\Throwable $e) { // For PHP 7
            $payer_id = 0;
        } catch (\Exception $e) { // For PHP 5
            $payer_id = 0;
        }
        PaypalPayer::create([
            'payer_id' => $payer_id,
            'subscription_id' => $params['subscription_id'],
            'user_id' => $params['user_id'],
            'plan_id' => $params['plan_id'],
            'resource' => $params['type'],
            'ip' => $params['ip'],
            'status' => 1,
            'notes' => '',
        ]);
    }

    public function subscribtionPaypalStatus(Request $request)
    {
        if ($request->get('status') === 'failed') {
            return redirect()->route('payment.fail', $request->all());
        }

        $user = Auth::user();

        $response = \App\Contexts\PayPal::get_subscription($request->get('subscription_id'));
        if ($response->error_code !== 0) {
            return redirect()->route('payment.fail');
        }
        $paypal_subscription = $response->result;
        $resource = $request->resource;
        PaypalPayer::create([
            'payer_id' => $paypal_subscription->subscriber->payer_id,
            'subscription_id' => $request->subscription_id,
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'resource' => $resource,
            'ip' => request()->ip(),
            'status' => 1,
            'notes' => '',
        ]);
        if ($resource == 'flex')
            $subscription = Subscription::withoutGlobalScope('completed')->where('subscription_id', $request->subscription_id)->first();
        else {
            $class = '\\App\\Models\\' . ucfirst($resource) . 'Subscription';
            $subscription = $class::where('subscription_id', $request->subscription_id)->first();
        }
        if ($paypal_subscription->status == 'ACTIVE') {
            $subscription->update([
                'completed' => 1,
                'status' => \App\Models\Subscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => now()->addMonth(),
            ]);
        }
        return redirect()->route('payment.success', ['subscription' => @$subscription->id, 'type' => $resource, 'plan_id' => $request->plan_id, 'redirect' => route("me.plans")]);
    }

    private function createSubscription($plan)
    {
        \Illuminate\Support\Facades\Log::channel('info')->info("#5 subscribe:createSubscription plan  " . '--auth:' . auth()->id());
        $approval_url = \App\Contexts\Plans::create_paypal_subscription($plan);
        \Illuminate\Support\Facades\Log::channel('info')->info("#8 subscribe:createSubscription plan redirect {$approval_url} " . '--auth:' . auth()->id());

        return $approval_url;
    }

    private function makeOneOffPayment($plan)
    {
        $inputFields = new InputFields();
        $inputFields
            ->setAllowNote(true)
            ->setNoShipping(1) // Important step
            ->setAddressOverride(0);

        $webProfile = new WebProfile();
        $webProfile
            ->setName(uniqid())
            ->setInputFields($inputFields)
            ->setTemporary(true);

        $createProfile = $webProfile->create($this->apiContext);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $title = 'title_' . app()->getLocale();
        $title = $plan->$title;
        $amount = floatval($plan->on_demand ? \App\Models\Plan::onDemandCreditsPrice($plan->credits_count) * $plan->credits_count : $plan->price);


        $plan_item = new Item();
        $plan_item
            ->setName($plan->title_en) // item name
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($amount); // unit price

// add item to list
        $item_list = new ItemList();
        $item_list->setItems([$plan_item]);

        if (session()->has('promocode')) {
            /**@var $promocode Promocode */
            $promocode = \App\Models\Promocode::active()->whereHas('plans', function ($q) use ($plan) {
                $q->where('plans.id', $plan->id);
            })->find(session()->get('promocode'));
            if ($promocode && (($promocode->max_usage <= $promocode->subscriptions()->where('user_id', auth()->id())->count()) || (($promocode->max_users > 0) && ($promocode->subscriptions()->count() >= $promocode->max_users)))) {
                session()->forget('promocode');
                $promocode = null;
            }
            if ($promocode) {
                $total = $amount;
                $amount = $promocode->calculate_price($amount);
                session()->forget('promocode');

                $promocode_item = new Item();
                $promocode_item
                    ->setName($promocode->title_en) // item name
                    ->setCurrency('USD')
                    ->setQuantity(1)
                    ->setPrice($total - $amount); // unit price
                $item_list->addItem($promocode_item);
            }
        }

        $paypal_amount = new Amount();
        $paypal_amount->setCurrency('USD')->setTotal($amount);


        $transaction = new Transaction();
        $transaction
            ->setAmount($paypal_amount)
            ->setItemList($item_list)
            ->setDescription($title);

        $redirect_urls = new RedirectUrls();
        $redirect_urls
            ->setReturnUrl(route('payment.paypal.execute', ['plan_id' => $plan->id])) // Specify return URL
            ->setCancelUrl(url()->route('payment.fail', ['plan_id' => $plan->id, 'redirect' => route('plans')]));


        $payment = new Payment();
        $payment
            ->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions([$transaction])
            ->setExperienceProfileId($createProfile->getId()); // Important step;

        // return $payment;

        try {
            $pay = $payment->create($this->apiContext);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            echo $ex->getCode(); // Prints the Error Code
            echo $ex->getData(); // Prints the detailed error message
            die($ex);

            if (config('app.debug')) {
                $message = "Exception: " . $ex->getMessage() . PHP_EOL;
                return ['payment_status' => false, 'data' => $message];
            } else {
                return ['payment_status' => false, 'data' => 'Some error occur, sorry for inconvenient'];
                // die('Some error occur, sorry for inconvenient');
            }
        }

        $payment_url = '';
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $payment_url = $link->getHref();
                break;
            }
        }


        // dd( $payment_url);

        $subscription_data = [
            'payment_method_id' => PaymentMethod::PAYPAL,
            'payment_id' => $payment->getId(),
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'plan_type' => $plan->type,
            'created_by_hook' => false,
            'status' => Subscription::STATUS_PENDING,
            'renewal' => false,
            'remaining_credits' => $plan->credits_count,
            'credits' => $plan->credits_count,
            'credit_price' => ($amount / $plan->credits_count),
            'promocode_id' => @$promocode->id,
            'amount' => $amount,
            'plan_price' => $amount,
        ];


        $geoip = geoip(request()->ip());

        if (isset($geoip['country_id'])) {
            $subscription_data['country_id'] = $geoip['country_id'];
            $subscription_data_video['country_id'] = $geoip['country_id'];
            $subscription_data_vector['country_id'] = $geoip['country_id'];
        }
        if (isset($geoip['city_id'])) {
            $subscription_data['city_id'] = $geoip['city_id'];
            $subscription_data_video['city_id'] = $geoip['city_id'];
            $subscription_data_vector['city_id'] = $geoip['city_id'];
        }

        $subscription = Subscription::create($subscription_data);
        return $payment_url;
    }

//    public function suspendSubscription($subscription_id)
//    {
//        $subscription = ImageSubscription::where([['subscription_id', '=', $subscription_id], ['user_id', '=', Auth::id()], ['renewal', '=', true], ['plan_type', '=', 'monthly']])->firstOrFail();
//
//        $params = ['reason' => 'ImageSubscription Suspended Temporarily'];
//        $response = \App\Contexts\PayPal::suspend_subscription($subscription_id, $params);
//
//        if ($response->error_code !== 0) {
//            abort(500, $response->error);
//        }
//
//        if ($response->info['http_code'] == 204) {
//            $subscription->update(['renewal' => false]);
//        }
//
//        return back();
//    }

    public function activateSubscription($id)
    {
        $subscription = Subscription::where([['user_id', '=', Auth::id()], ['renewal', '=', 0]])
            ->whereIn('plan_type', ['monthly', 'annual'])
            ->findOrFail($id);
        if (auth()->user()->subscriptions()->where('renewal', 1)->where('status', Subscription::STATUS_ACTIVE)->where('plan_id', $subscription->plan_id)->count())
            return ['status' => 0, 'message' => __('You have another active subscription!')];

        if ($subscription->payment_method_id == PaymentMethod::STRIPE) {
            \Stripe\Subscription::update(
                $subscription->subscription_id,
                [
                    'pause_collection' => '',
                ]
            );
            $subscription->update(['renewal' => 1]);
        } elseif ($subscription->payment_method_id == PaymentMethod::PAYPAL) {
            $params = ['reason' => 'Reactivating the subscription'];
            $response = \App\Contexts\PayPal::active_subscription($subscription->subscription_id, $params);
            if ($response->error_code !== 0) {
                return ['status' => 0, 'message' => $response->error];
            }
            if ($response->info['http_code'] == 204) {
                $subscription->update(['renewal' => 1]);
            }
        }
        return ['status' => 1, 'message' => __('Auto-renewal successfully turned on')];
    }

    public function cancelSubscription($id)
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->whereIn('plan_type', ['monthly', 'annual'])
            ->where('status', Subscription::STATUS_ACTIVE)
            ->where('renewal', 1)
            ->findOrFail($id);
        $subscribtion_data = [
            'renewal' => 0,
        ];
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
            Log::channel('info')->warning("Cancel Stripe subscription ", ['id' => $subscription->id, 'subscription_id' => $subscription->subscription_id]);
        }
        $subscription->update($subscribtion_data);
        return ['status' => 1, 'message' => __('Auto-renewal successfully turned off')];
    }

    public function cancelSubscription_image($id)
    {
        $subscription = ImageSubscription::where('user_id', Auth::id())
            ->whereIn('plan_type', ['monthly', 'annual'])
            ->where('status', ImageSubscription::STATUS_ACTIVE)
            ->findOrFail($id);

        $params = ['reason' => 'Not satisfied with the service'];
        if ($subscription->payment_method_id == PaymentMethod::PAYPAL)
            \App\Contexts\PayPal::cancel_subscription($subscription->subscription_id, $params);
        elseif ($subscription->payment_method_id == PaymentMethod::STRIPE)
            \App\Contexts\Stripe::cancel_subscription($subscription->subscription_id, $params);
        $subscribtion_data = [
//            'status' => Subscription::STATUS_CANCEL,
            'renewal' => false,
        ];
        $subscription->update($subscribtion_data);
        return back()->with('success', trans('global.Subscription canceled successfully'));
    }

    public function cancelSubscription_video($id)
    {
        $subscription = VideoSubscription::where('user_id', Auth::id())
            ->whereIn('plan_type', ['monthly', 'annual'])
            ->whereIn('status', [VideoSubscription::STATUS_PENDING, VideoSubscription::STATUS_ACTIVE])
            ->findOrFail($id);

        $params = ['reason' => 'Not satisfied with the service'];
        if ($subscription->payment_method_id == PaymentMethod::PAYPAL)
            \App\Contexts\PayPal::cancel_subscription($subscription->subscription_id, $params);
        elseif ($subscription->payment_method_id == PaymentMethod::STRIPE)
            \App\Contexts\Stripe::cancel_subscription($subscription->subscription_id, $params);
        $subscribtion_data = [
//            'status' => Subscription::STATUS_CANCEL,
            'renewal' => false,
        ];
        $subscription->update($subscribtion_data);
        return back()->with('success', trans('global.ImageSubscription canceled successfully'));
    }

    public function cancelSubscription_vector($id)
    {
        $subscription = VectorSubscription::where('user_id', Auth::id())
            ->whereIn('plan_type', ['monthly', 'annual'])
            ->whereIn('status', [VectorSubscription::STATUS_PENDING, VectorSubscription::STATUS_ACTIVE])
            ->findOrFail($id);

        $params = ['reason' => 'Not satisfied with the service'];
        if ($subscription->payment_method_id == PaymentMethod::PAYPAL)
            \App\Contexts\PayPal::cancel_subscription($subscription->subscription_id, $params);
        elseif ($subscription->payment_method_id == PaymentMethod::STRIPE)
            \App\Contexts\Stripe::cancel_subscription($subscription->subscription_id, $params);
        $subscribtion_data = [
//            'status' => Subscription::STATUS_CANCEL,
            'renewal' => false,
        ];
        $subscription->update($subscribtion_data);
        return back()->with('success', trans('global.ImageSubscription canceled successfully'));
    }


    public function purchase(Request $request)
    {
        /**@var $user User */
        $user = auth()->user();
        if ($user->role !== 'normal')
            return redirect()->route('landPage');
        if (!$request->plan_id)
            abort(404);
        $plan_id = intval($request->plan_id);
        $plan = Plan::where(['status' => true])->findOrFail($plan_id);
        if ($plan->on_demand) {
            if ($request->credits_count) {
                $request->merge(['credits_count' => intval($request->credits_count)]);
                session()->put('on_demand_subscription', ['plan_id' => $plan->id, 'credits_count' => $request->credits_count]);
                return redirect()->route('purchase', ['plan_id' => $plan->id]);
            } elseif (!session()->has('on_demand_subscription'))
                return redirect()->route('plans');
        }
        if (auth()->user()->subscriptions()->where('renewal', 1)->where('status', Subscription::STATUS_ACTIVE)->where('plan_id', $request->plan_id)->count())
            return redirect()->route('plans');
        if ($request->method() == 'GET' && $request->payment_method != 'paypal') {
            try {
                if (in_array(app('router')->getRoutes()->match(app('request')->create(request()->headers->get('referer')))->getName(), ['photo.show', 'video.show', 'vector.show'])) {
                    session()->put('redirect_after_purchase', request()->headers->get('referer'));
                    session()->put('open_download_options', [
                        'license_type' => $request->license_type,
                        'user_email' => $request->user_email,
                    ]);
                }
            } catch (\Exception $exception) {
            }
            return view('purchase', compact('plan', 'plan_id'));
        }
        $this->validate($request, [
            'payment_method' => ['required', Rule::in(['paypal', 'credit', 'card'])],
            'pmethod' => ['required_if:payment_method,credit'],
        ]);
        $promocode = session()->has('promocode') ? \App\Models\Promocode::active()->whereHas('plans', function ($q) use ($plan) {
            $q->where('plans.id', $plan->id);
        })->find(session()->get('promocode')) : null;
        if ($promocode && (($promocode->max_usage <= $promocode->subscriptions()->where('user_id', auth()->id())->count()) || ($promocode->max_users > 0 && ($promocode->subscriptions()->count() >= $promocode->max_users)))) {
            if (session()->has('promocode')) {
                session()->forget('promocode');
                return redirect()->back();
            }
            $promocode = null;
        }
        if ($promocode && $promocode->calculate_price($plan->price) == 0) {
            $geoip = geoip(request()->ip());
            $subscription = Plans::store_package_subscription($user, $plan, [
                'payment_method_id' => PaymentMethod::FREE,
                'country_id' => @$geoip['country_id'],
                'city_id' => @$geoip['city_id'],
                'ip' => $request->ip(),
                'promocode' => $promocode,
                'completed' => 1,
                'status' => \App\Models\Subscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => now()->addYear(),
            ]);
            return redirect()->route('payment.success');
        }

        if ($request->payment_method == 'paypal') {
            if ($plan->type == 'package') {
                return redirect()->away($this->makeOneOffPayment($plan));
            }
            return redirect()->route('plan.subscribe', ['plan_id' => $plan_id]);
        } elseif (in_array($request->payment_method, ['credit', 'card'])) {
            /**@var $customer Customer */
            $geoip = geoip(request()->ip());
            $customer = $user->createOrGetStripeCustomer(['name' => $user->name, 'email' => $user->email]);
            /**@var $paymentMethod \Stripe\PaymentMethod */
            $paymentMethod = \Stripe\PaymentMethod::retrieve($request->pmethod);
            try {
                $paymentMethod->attach([
                    'customer' => $user->stripe_id,
                ]);
                $user->updateDefaultPaymentMethod($paymentMethod->id);
            } catch (CardException $exception) {
                return response()->json(['status' => 0, 'error' => __($exception->getMessage())], 500);
            }
            if ($plan->type == 'package') {
                $amount = $plan->on_demand ? \App\Models\Plan::onDemandCreditsPrice($plan->credits_count) * $plan->credits_count : $plan->price;
                $amount = $amount * 100;
                $subscription = Plans::store_package_subscription($user, $plan, [
                    'payment_method_id' => PaymentMethod::STRIPE,
                    'status' => Subscription::STATUS_PENDING,
                    'country_id' => @$geoip['country_id'],
                    'city_id' => @$geoip['city_id'],
                    'card_fingerprint' => $paymentMethod->card->fingerprint,
                    'ip' => $request->ip(),
                    'promocode' => $promocode,
                ]);
                $paymentIntent = PaymentIntent::create([
                    'payment_method' => $request->pmethod,
                    'payment_method_types' => ['card'],
                    'amount' => $amount,
                    'currency' => 'usd',
                    'customer' => $customer->id,
                    'metadata' => ['subscription_id' => $subscription->id, 'subscription_type' => get_class($subscription)],
                    'description' => $plan->{'title_en'}
                ]);
                $subscription->update(['payment_id' => $paymentIntent->id]);
                return response()->json([
                    'payment_status' => $paymentIntent->status,
                    'subscription_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret
                ]);
            } else {
                if ($plan->trial_days && Subscription::where('plan_id', $plan->id)->where('card_fingerprint', $paymentMethod->card->fingerprint)->count())
                    return ['status' => 0];
                $subscription = Plans::store_subscription($user, $plan, [
                    'payment_method_id' => PaymentMethod::STRIPE,
                    'status' => Subscription::STATUS_PENDING,
                    'country_id' => @$geoip['country_id'],
                    'city_id' => @$geoip['city_id'],
                    'card_fingerprint' => $paymentMethod->card->fingerprint,
                    'ip' => request()->ip(),
                    'promocode' => @$promocode
                ]);
                $subscription_params = [
                    'customer' => $user->stripe_id,
                    'items' => [
                        ['price' => $plan->stripe_plan],
                    ],
                    'metadata' => ['subscription_id' => $subscription->id, 'subscription_type' => get_class($subscription)],
                    'payment_behavior' => 'default_incomplete',
                    'expand' => ['latest_invoice.payment_intent'],
                ];
                if ($promocode)
                    $subscription_params['coupon'] = $promocode->code;
                if ($plan->trial_days) {
                    $subscription_params['trial_end'] = now()->addDays($plan->trial_days)->timestamp;
                }
                $stripe_subscription = \Stripe\Subscription::create($subscription_params);
                $subscription->update(['subscription_id' => $stripe_subscription->id]);
                return response()->json([
                    'payment_status' => $stripe_subscription->latest_invoice->payment_intent->status,
                    'subscription_id' => $stripe_subscription->id,
                    'client_secret' => $stripe_subscription->latest_invoice->payment_intent->client_secret
                ]);
            }
        }
    }

    public function stripePaymentStatus(Request $request, $id)
    {
        /**@var $payment_intent PaymentIntent */
        /**@var $setup_intent SetupIntent */
        if (Str::startsWith($id, 'pi_') !== false) {
            $payment_intent = PaymentIntent::retrieve([
                'id' => $id,
                'expand' => ['invoice.subscription'],
            ]);
            if (@$payment_intent->invoice->subscription) {
                Stripe::complete_subscription($payment_intent->invoice->subscription);
            } else {
                Stripe::complete_payment($payment_intent);
            }
            if ($payment_intent->status === 'succeeded')
                return redirect()->route('payment.success', $request->all());
        } elseif (Str::startsWith($id, 'seti_') !== false) {
            $setup_intent = SetupIntent::retrieve([
                'id' => $id,
            ]);

            if ($setup_intent->status === 'succeeded')
                return redirect()->route('payment.success', $request->all());
        } elseif (Str::startsWith($id, 'sub_') !== false) {
            $subscription = \Stripe\Subscription::retrieve([
                'id' => $id,
            ]);
            if (Stripe::complete_subscription($subscription)['status'])
                return redirect()->route('payment.success', $request->all());
        } else
            abort(404);
        return redirect()->route('payment.fail', $request->all());
    }

    public function stripe_invoice()
    {
        return view('stripe_invoice');
    }

    public function check_promocode(Request $request, Plan $plan)
    {
        $this->validate($request, [
            'promocode' => ['required'],
            'plan_id' => ['required'],
        ]);
        /**@var $promocode Promocode */
        $promocode = \App\Models\Promocode::where('code', $request->promocode)->whereHas('plans', function ($q) {
            $q->where('plans.id', \request('plan_id'));
        })->first();
        if ($promocode) {
            if (($promocode->expired_at && $promocode->expired_at->lt(now())) or !$promocode->status)
                return ['status' => 0, 'msg' => 'لقد انتهت صلاحية الخصم.'];
            $user = auth()->user();
            $user_usage = $promocode->subscriptions()->where('subscriptions.user_id', $user->id)->count();
            if ($user_usage) {
                if ($user_usage >= $promocode->max_usage) {
                    if ($promocode->max_usage > 1)
                        return ['status' => 0, 'msg' => "الحد الأقصى لاستخدام الخصم هو {$promocode->max_usage} مرة."];
                    else
                        return ['status' => 0, 'msg' => "قمت باستخدام الكود سابقاً."];
                }
            }
            if (($promocode->subscriptions()->count() >= $promocode->max_users) && ($promocode->max_users > 0))
                return ['status' => false, 'msg' => "عذراً، اكتمل عدد المستفيدين."];
            session()->put('promocode', $promocode->id);
            $amount = $plan->on_demand ? \App\Models\Plan::onDemandCreditsPrice($plan->credits_count) * $plan->credits_count : $plan->price;
            return ['status' => 1, 'msg' => "تم تطبيق الكود", 'promocode' => [
                'title' => __('Discount') . " ({$promocode->title})",
                'code' => $promocode->code,
                'type' => $promocode->type,
                'value' => intval($promocode->value),
                'total' => $promocode->calculate_price($amount),
                'discount' => $promocode->calculate_discount($amount),
                'calculated' => number_format($promocode->calculate_price($amount), 2),
            ]];
        } else
            return ['status' => 0, 'msg' => 'أدخلت كود خاطئ.'];
    }

    public function delete_promocode(Request $request)
    {
        session()->forget('promocode');
        return ['status' => 1];
    }

    public function download_options(Request $request, $type, $id)
    {
        $record_class = "App\\Models\\" . ucfirst($type);
        $record = $record_class::findOrFail($id);
        $license = $request->input('license', 'standard');
        $old_subscriptions = "active_" . strtolower($type) . "_subscriptions";
        $old_subscriptions = auth()->user()->{$old_subscriptions}()->get();
        $active_subscriptions = auth()->user()->active_subscriptions()->has('plan')->with('plan')->get();

        $standard_const_credits = $record_class::standard_credits();
        $enhanced_const_credits = $record_class::enhanced_credits();
        $exclusive_const_credits = $record_class::exclusive_credits();
        $can_download = false;
        $need_to_download = \App\Models\Subscription::need_to_download($record, $license);
        if (($license == 'standard' && $old_subscriptions->count()) or $need_to_download->count())
            $can_download = true;

        $routes = ['image' => 'photos.download', 'video' => 'video.download', 'vector' => 'vectors.download'];
        $download_url = \Illuminate\Support\Facades\URL::temporarySignedRoute($routes[$type], now()->addMinutes(30), [$record->token_id]);
        $data = compact('record', 'license', 'type',
            'old_subscriptions', 'active_subscriptions', 'standard_const_credits', 'enhanced_const_credits', 'exclusive_const_credits'
            , 'need_to_download', 'can_download', 'download_url');
        $class = get_class($record);
        $method = "{$license}_credits";
        $needed_credits = $class::{$method}();
        if (request('removebg'))
            $needed_credits += 1;
        if (request('raw'))
            $needed_credits += 30;
        $data['needed_credits'] = $needed_credits;
        if ($request->subscription_type == 'team_subscriptions') {
            $data['need_to_download_team'] = \App\Models\Subscription::need_to_download($record, request('license'), 1);
            $data['active_team_subscriptions'] = auth()->user()->active_team_subscriptions()->has('plan')->with('plan')->get();
            $data['can_download'] = false;
            $const_credits = "{$license}_const_credits";
            if ($data['active_team_subscriptions']->sum('pivot.remaining_credits') >= $$const_credits)
                $data['can_download'] = true;
            return ['html' => view("download-options.team_subscriptions", $data)->render(), 'credits' => $needed_credits];
        }
        return ['html' => view("download-options.subscriptions", $data)->render(), 'credits' => $needed_credits];
    }

    public function update_stripe_payment_method(Request $request)
    {
        $this->validate($request, [
            'subscription_id' => ['required', Rule::exists('subscriptions', 'id')->whereIn('plan_type', ['monthly', 'annual'])->where('payment_method_id', PaymentMethod::STRIPE)->where('renewal', 1)->whereNull('deleted_at')],
            'pmethod' => ['required']
        ]);
        $subscription = Subscription::where('user_id', auth()->id())->findOrFail($request->subscription_id);

        $paymentMethod = \Stripe\PaymentMethod::retrieve($request->pmethod);
        $customer = auth()->user()->asStripeCustomer();
        $paymentMethod->attach([
            'customer' => $customer->id,
        ]);
        $customer->invoice_settings->default_payment_method = $paymentMethod->id;
        $customer->save();
        $stripe_subscription = \Stripe\Subscription::retrieve($subscription->subscription_id);
        $stripe_subscription->default_payment_method = $paymentMethod->id;
        $stripe_subscription->save();
        Log::channel('info')->info("Update subscription payment method ", ['id' => $subscription->id, 'subscription_id' => $subscription->subscription_id]);
        return ['status' => 1, 'message' => __('Payment method successfully updated')];
    }
}

