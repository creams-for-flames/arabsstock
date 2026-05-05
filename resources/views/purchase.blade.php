@php
    //session()->forget('promocode');
    /**@var $promocode \App\Models\Promocode*/
        $user = auth()->user();
        $defaultPaymentMethod = $user->defaultPaymentMethod();
        $promocode = session()->has('promocode')?\App\Models\Promocode::active()->whereHas('plans',function ($q)use($plan){$q->where('plans.id',$plan->id);})->find(session()->get('promocode')):false;
        if ($promocode && ($promocode->max_usage <= $promocode->subscriptions()->where('user_id', auth()->id())->count())) {
            session()->forget('promocode');
            $promocode = null;
        }
        $amount=$plan->on_demand?\App\Models\Plan::onDemandCreditsPrice($plan->credits_count)*$plan->credits_count:$plan->price;
        if($promocode)
            $amount=$promocode->calculate_price($amount);
@endphp
@extends('app')
@push('header')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/purchase.css') }}?v={{ config('app.assets.version') }}">
@endpush
@section('title')
    {{__('Purchase')}} -
@endsection
@section('content')
    <div class="container">
        <form class="purchase pb-5 pt-5" method="post" id="purchase"
              action="{{ route('purchase',['plan_id'=>$plan_id
              ]) }}">
            @csrf
            <div class="row">
                <div class="col-md-7 col-lg-7">
                    <h2 class="font-weight-bold">
                        {{ __('Choose Payment Method') }}
                    </h2>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div id="payment_method">
                        <div class="card credit mb-3">
                            <div class="card-header mt-0 border-0" id="creditHeading">
                                <div class="form-check" data-toggle="collapse"
                                     data-target="#creditCard"
                                     aria-expanded="true" aria-controls="creditCard">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="credit_method"
                                           value="credit"
                                           checked
                                           style="margin-top: 9px;">
                                    <label class="form-check-label" for="credit_method">
                                        <h3 class="m-0 font-weight-bold">{{ __('Credit card') }}</h3>
                                    </label>
                                </div>
                            </div>
                            <div id="creditCard" class="collapse show" aria-labelledby="creditHeading"
                                 data-parent="#payment_method">
                                <div class="card-body mt-3">
                                    @if($defaultPaymentMethod)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   value="{{ $defaultPaymentMethod->id }}" name="pmethod"
                                                   id="defaultPaymentMethod" checked>
                                            <label class="form-check-label ml-3" for="defaultPaymentMethod">
                                                <div
                                                    class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-row align-items-center">
                                                        <img
                                                            src="{{ asset("img/cards/{$defaultPaymentMethod->card->brand}.svg") }}"
                                                            class="rounded" width="32">
                                                        <div class="d-flex flex-column ml-3">
                                                        <span
                                                            class="semi-bold">{{ ucfirst($defaultPaymentMethod->card->brand) }} <span>•••• {{ $defaultPaymentMethod->card->last4 }}</span></span>
                                                            <span
                                                                class="text-muted fs-12">Expires {{ $defaultPaymentMethod->card->exp_month }} {{ $defaultPaymentMethod->card->exp_year }} </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endif
                                    <div
                                        @if($defaultPaymentMethod)style="display: none;padding-top: 20px;border-top: 1px solid #d5d5d5;margin-top: 20px;"
                                        @endif id="creditCardDetails">
                                        <h4 class="font-weight-bold">{{ __('Card information') }}</h4>
                                        <div class="form-group pt-4 mb-4">
                                            <input type="text" class="form-control"
                                                   id="card-holder-name" name="card-holder-name" autocomplete="cc-name"
                                                   required>
                                            <label for="card-holder-name"
                                                   class="text m-0">{{ __('Name on Card') }}<span
                                                    class="text-danger fs-14"> * </span></label>
                                            <span class="line"></span>
                                        </div>
                                        <div class="form-group pt-3 mb-4">
                                            <label for="card-element"
                                                   class="text ">{{ __('Credit Card Number') }}<span
                                                    class="text-danger fs-14"> * </span></label>
                                            <div id="card-element"></div>
                                            <input type="hidden" id="pmethod" name="pmethod">
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group pt-3 mb-4">
                                                    <label for="card-expiry"
                                                           class="text ">{{ __('Expiration Date') }}<span
                                                            class="text-danger fs-14"> * </span></label>
                                                    <div id="card-expiry"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group pt-3 mb-4">
                                                    <label for="card-cvc"
                                                           class="text ">{{ __('CVC') }}<span
                                                            class="text-danger fs-14"> * </span></label>
                                                    <div id="card-cvc"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <button type="button"
                                                class="btn btn-download hover:btn-secondary large btn-lg btn-block mt-4"
                                                id="card-button"
                                                data-stripekey="{{ config('services.stripe.key') }}"
                                                data-local="{{ strtolower(app()->getLocale()) }}"
                                                data-planName="{{ __($plan->{'title_en'}) }}"
                                                data-planId="{{ $plan->id }}"
                                                data-price="{{ $amount*100 }}"
                                                data-purchaseUrl="{{ route('purchase') }}"
                                                data-failUrl="{!! route('payment.fail',['plan_id'=>$plan->id, 'redirect' => route('me.plans')]) !!}"
                                                data-statusUrl="{!! route('stripe.payment.status',[':subscription_id','plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}"
                                        >
                                            {{ __('Complete checkout') }}
                                        </button>
                                </div>
                            </div>
                        </div>
                        <div class="card paypal mb-3">
                            <div class="card-header mt-0 border-0" id="paypalHeading">
                                <div class="form-check"
                                     data-toggle="collapse" data-target="#paypalCard"
                                     aria-expanded="true" aria-controls="paypalCard"
                                >
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="paypal_method"
                                           value="paypal">
                                    <label class="form-check-label" for="paypal_method">
                                        <img src="{{ asset('img/paypal.svg') }}" alt="">
                                    </label>
                                </div>
                            </div>
                            <div id="paypalCard" class="collapse" aria-labelledby="paypalHeading"
                                 data-parent="#payment_method">
                                <div class="card-body mt-3">
                                    <div role="link" data-button="" data-funding-source="paypal"
                                         class="paypal-button"
                                         onclick="javascript:window.location.href='{!! route('purchase',['plan_id'=>$plan->id,'payment_method'=>'paypal']) !!}'"
                                    >
                                        <div class="paypal-button-label-container"><img
                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAxcHgiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAxMDEgMzIiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaW5ZTWluIG1lZXQiIHhtbG5zPSJodHRwOiYjeDJGOyYjeDJGO3d3dy53My5vcmcmI3gyRjsyMDAwJiN4MkY7c3ZnIj48cGF0aCBmaWxsPSIjMDAzMDg3IiBkPSJNIDEyLjIzNyAyLjggTCA0LjQzNyAyLjggQyAzLjkzNyAyLjggMy40MzcgMy4yIDMuMzM3IDMuNyBMIDAuMjM3IDIzLjcgQyAwLjEzNyAyNC4xIDAuNDM3IDI0LjQgMC44MzcgMjQuNCBMIDQuNTM3IDI0LjQgQyA1LjAzNyAyNC40IDUuNTM3IDI0IDUuNjM3IDIzLjUgTCA2LjQzNyAxOC4xIEMgNi41MzcgMTcuNiA2LjkzNyAxNy4yIDcuNTM3IDE3LjIgTCAxMC4wMzcgMTcuMiBDIDE1LjEzNyAxNy4yIDE4LjEzNyAxNC43IDE4LjkzNyA5LjggQyAxOS4yMzcgNy43IDE4LjkzNyA2IDE3LjkzNyA0LjggQyAxNi44MzcgMy41IDE0LjgzNyAyLjggMTIuMjM3IDIuOCBaIE0gMTMuMTM3IDEwLjEgQyAxMi43MzcgMTIuOSAxMC41MzcgMTIuOSA4LjUzNyAxMi45IEwgNy4zMzcgMTIuOSBMIDguMTM3IDcuNyBDIDguMTM3IDcuNCA4LjQzNyA3LjIgOC43MzcgNy4yIEwgOS4yMzcgNy4yIEMgMTAuNjM3IDcuMiAxMS45MzcgNy4yIDEyLjYzNyA4IEMgMTMuMTM3IDguNCAxMy4zMzcgOS4xIDEzLjEzNyAxMC4xIFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDAzMDg3IiBkPSJNIDM1LjQzNyAxMCBMIDMxLjczNyAxMCBDIDMxLjQzNyAxMCAzMS4xMzcgMTAuMiAzMS4xMzcgMTAuNSBMIDMwLjkzNyAxMS41IEwgMzAuNjM3IDExLjEgQyAyOS44MzcgOS45IDI4LjAzNyA5LjUgMjYuMjM3IDkuNSBDIDIyLjEzNyA5LjUgMTguNjM3IDEyLjYgMTcuOTM3IDE3IEMgMTcuNTM3IDE5LjIgMTguMDM3IDIxLjMgMTkuMzM3IDIyLjcgQyAyMC40MzcgMjQgMjIuMTM3IDI0LjYgMjQuMDM3IDI0LjYgQyAyNy4zMzcgMjQuNiAyOS4yMzcgMjIuNSAyOS4yMzcgMjIuNSBMIDI5LjAzNyAyMy41IEMgMjguOTM3IDIzLjkgMjkuMjM3IDI0LjMgMjkuNjM3IDI0LjMgTCAzMy4wMzcgMjQuMyBDIDMzLjUzNyAyNC4zIDM0LjAzNyAyMy45IDM0LjEzNyAyMy40IEwgMzYuMTM3IDEwLjYgQyAzNi4yMzcgMTAuNCAzNS44MzcgMTAgMzUuNDM3IDEwIFogTSAzMC4zMzcgMTcuMiBDIDI5LjkzNyAxOS4zIDI4LjMzNyAyMC44IDI2LjEzNyAyMC44IEMgMjUuMDM3IDIwLjggMjQuMjM3IDIwLjUgMjMuNjM3IDE5LjggQyAyMy4wMzcgMTkuMSAyMi44MzcgMTguMiAyMy4wMzcgMTcuMiBDIDIzLjMzNyAxNS4xIDI1LjEzNyAxMy42IDI3LjIzNyAxMy42IEMgMjguMzM3IDEzLjYgMjkuMTM3IDE0IDI5LjczNyAxNC42IEMgMzAuMjM3IDE1LjMgMzAuNDM3IDE2LjIgMzAuMzM3IDE3LjIgWiI+PC9wYXRoPjxwYXRoIGZpbGw9IiMwMDMwODciIGQ9Ik0gNTUuMzM3IDEwIEwgNTEuNjM3IDEwIEMgNTEuMjM3IDEwIDUwLjkzNyAxMC4yIDUwLjczNyAxMC41IEwgNDUuNTM3IDE4LjEgTCA0My4zMzcgMTAuOCBDIDQzLjIzNyAxMC4zIDQyLjczNyAxMCA0Mi4zMzcgMTAgTCAzOC42MzcgMTAgQyAzOC4yMzcgMTAgMzcuODM3IDEwLjQgMzguMDM3IDEwLjkgTCA0Mi4xMzcgMjMgTCAzOC4yMzcgMjguNCBDIDM3LjkzNyAyOC44IDM4LjIzNyAyOS40IDM4LjczNyAyOS40IEwgNDIuNDM3IDI5LjQgQyA0Mi44MzcgMjkuNCA0My4xMzcgMjkuMiA0My4zMzcgMjguOSBMIDU1LjgzNyAxMC45IEMgNTYuMTM3IDEwLjYgNTUuODM3IDEwIDU1LjMzNyAxMCBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA2Ny43MzcgMi44IEwgNTkuOTM3IDIuOCBDIDU5LjQzNyAyLjggNTguOTM3IDMuMiA1OC44MzcgMy43IEwgNTUuNzM3IDIzLjYgQyA1NS42MzcgMjQgNTUuOTM3IDI0LjMgNTYuMzM3IDI0LjMgTCA2MC4zMzcgMjQuMyBDIDYwLjczNyAyNC4zIDYxLjAzNyAyNCA2MS4wMzcgMjMuNyBMIDYxLjkzNyAxOCBDIDYyLjAzNyAxNy41IDYyLjQzNyAxNy4xIDYzLjAzNyAxNy4xIEwgNjUuNTM3IDE3LjEgQyA3MC42MzcgMTcuMSA3My42MzcgMTQuNiA3NC40MzcgOS43IEMgNzQuNzM3IDcuNiA3NC40MzcgNS45IDczLjQzNyA0LjcgQyA3Mi4yMzcgMy41IDcwLjMzNyAyLjggNjcuNzM3IDIuOCBaIE0gNjguNjM3IDEwLjEgQyA2OC4yMzcgMTIuOSA2Ni4wMzcgMTIuOSA2NC4wMzcgMTIuOSBMIDYyLjgzNyAxMi45IEwgNjMuNjM3IDcuNyBDIDYzLjYzNyA3LjQgNjMuOTM3IDcuMiA2NC4yMzcgNy4yIEwgNjQuNzM3IDcuMiBDIDY2LjEzNyA3LjIgNjcuNDM3IDcuMiA2OC4xMzcgOCBDIDY4LjYzNyA4LjQgNjguNzM3IDkuMSA2OC42MzcgMTAuMSBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA5MC45MzcgMTAgTCA4Ny4yMzcgMTAgQyA4Ni45MzcgMTAgODYuNjM3IDEwLjIgODYuNjM3IDEwLjUgTCA4Ni40MzcgMTEuNSBMIDg2LjEzNyAxMS4xIEMgODUuMzM3IDkuOSA4My41MzcgOS41IDgxLjczNyA5LjUgQyA3Ny42MzcgOS41IDc0LjEzNyAxMi42IDczLjQzNyAxNyBDIDczLjAzNyAxOS4yIDczLjUzNyAyMS4zIDc0LjgzNyAyMi43IEMgNzUuOTM3IDI0IDc3LjYzNyAyNC42IDc5LjUzNyAyNC42IEMgODIuODM3IDI0LjYgODQuNzM3IDIyLjUgODQuNzM3IDIyLjUgTCA4NC41MzcgMjMuNSBDIDg0LjQzNyAyMy45IDg0LjczNyAyNC4zIDg1LjEzNyAyNC4zIEwgODguNTM3IDI0LjMgQyA4OS4wMzcgMjQuMyA4OS41MzcgMjMuOSA4OS42MzcgMjMuNCBMIDkxLjYzNyAxMC42IEMgOTEuNjM3IDEwLjQgOTEuMzM3IDEwIDkwLjkzNyAxMCBaIE0gODUuNzM3IDE3LjIgQyA4NS4zMzcgMTkuMyA4My43MzcgMjAuOCA4MS41MzcgMjAuOCBDIDgwLjQzNyAyMC44IDc5LjYzNyAyMC41IDc5LjAzNyAxOS44IEMgNzguNDM3IDE5LjEgNzguMjM3IDE4LjIgNzguNDM3IDE3LjIgQyA3OC43MzcgMTUuMSA4MC41MzcgMTMuNiA4Mi42MzcgMTMuNiBDIDgzLjczNyAxMy42IDg0LjUzNyAxNCA4NS4xMzcgMTQuNiBDIDg1LjczNyAxNS4zIDg1LjkzNyAxNi4yIDg1LjczNyAxNy4yIFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDA5Y2RlIiBkPSJNIDk1LjMzNyAzLjMgTCA5Mi4xMzcgMjMuNiBDIDkyLjAzNyAyNCA5Mi4zMzcgMjQuMyA5Mi43MzcgMjQuMyBMIDk1LjkzNyAyNC4zIEMgOTYuNDM3IDI0LjMgOTYuOTM3IDIzLjkgOTcuMDM3IDIzLjQgTCAxMDAuMjM3IDMuNSBDIDEwMC4zMzcgMy4xIDEwMC4wMzcgMi44IDk5LjYzNyAyLjggTCA5Ni4wMzcgMi44IEMgOTUuNjM3IDIuOCA5NS40MzcgMyA5NS4zMzcgMy4zIFoiPjwvcGF0aD48L3N2Zz4"
                                                class="paypal-logo paypal-logo-paypal paypal-logo-color-blue"></div>
                                        <div class="paypal-button-spinner"></div>
                                    </div>
                                    {{--                                        <p class="text-center mt-1 text-muted">The safer, easier way to pay</p>--}}
                                </div>
                            </div>
                        </div>
                        <div class="card wallets mt-3 " style="display: none;">
                            <div class="card-header mt-0 border-0" id="walletsHeading">
                                <div class="form-check"
                                     data-toggle="collapse" data-target="#walletsCard"
                                     aria-expanded="true" aria-controls="walletsCard"
                                >
                                    <input class="form-check-input" type="radio" name="payment_method"
                                           id="wallets_method"
                                           value="wallets">
                                    <label class="form-check-label" for="wallets_method">
                                        <img class="mx-0 applayPay" src="{{ asset('img/ApplePay.svg') }}" alt="">
                                        <span class="mx-2">&</span>
                                        <img class="mx-0 googlePay" src="{{ asset('img/Google_Pay.svg') }}" alt="">
                                    </label>
                                </div>
                            </div>
                            <div id="walletsCard" class="collapse" aria-labelledby="walletsHeading"
                                 data-parent="#payment_method">
                                <div class="card-body mt-3">
                                    <div id="payment-request-button"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-lg-5">
                    <div class="sidebar">
                        <div class="sticky">
                            <h3 class="text-capitalize h2-title mb-3 pt-3">{{ __('Order summary') }}</h3>
                            <div class="card p-4">
                                <div class="details datels-Order ">
                                    <div class="row mb-3">
                                        <div class="col-md-9">
                                            <strong>
                                                {{ $plan->title }}
                                            </strong>
                                            @if($plan->type=='package')
                                                <p class="text-muted fs-11 mb-1">{{ __('Downloads expire within a year of purchase') }}</p>
                                                <p class="text-muted fs-11 mb-1">{{ __('One time purchase') }}</p>
                                            @else
                                                <p class="text-muted fs-11 mb-1">{{ __(':credits credits renewed every :frequency',['credits'=>$plan->credits_count,'frequency'=>__('month')]) }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <h5>
                                                ${{$amount}}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="row {{ $promocode?'':'d-none' }}" id="after_discount">
                                        <div class="col-md-10 col-lg-10">
                                            <strong>
                                                {{ __('Discount') }} @if($promocode)({{ $promocode->title }})@endif
                                            </strong>
                                        </div>
                                        <div class="col-md-2 col-lg-2">
                                            <h5 class="discount text-right">
                                                @if($promocode)
                                                    -${{ $amount }}
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                    <hr>
                                    @if(\App\Models\Promocode::active()->whereHas('plans',function ($q)use($plan){$q->where('plans.id',$plan->id);})->count())
                                        <a class="mb-0" data-toggle="collapse" href="#promocodeCollapse" role="button"
                                           aria-expanded="false"
                                           aria-controls="promocodeCollapse">{{ __('Do you have a coupon code?') }}</a>
                                        <div class="collapse" id="promocodeCollapse">
                                            <p id="promocode_msg" class="mb-1 color-primary"
                                               style="visibility: hidden;font-size: 11px;">1</p>
                                            <div class="form-group d-flex">
                                                <input type="text" class="form-control mr-2 "
                                                       placeholder="{{ __('Promocode') }}"
                                                       aria-describedby="check_promocode" id="promocode"
                                                       data-plan-id="{{ $plan->id }}"
                                                       data-plan-price="{{ number_format($plan->price) }}"
                                                       style="height: 30px;"
                                                       @if($promocode)value="{{ $promocode->code }}" readonly @endif >
                                                <div class="">
                                                    <span type="button"
                                                          class="color-primary pointer-primary {{ $promocode?'d-none':'' }}"
                                                          data-check-url="{{ route('check_promocode',$plan) }}"
                                                          data-apply-msg="{{ __('Please apply promocode first') }}"
                                                          id="check_promocode">{{ __('Apply') }}</span>
                                                    <span type="button"
                                                          class="color-primary pointer-primary {{ $promocode?'':'d-none' }}"
                                                          data-check-url="{{ route('check_promocode',$plan) }}"
                                                          data-apply-msg="{{ __('Please apply promocode first') }}"
                                                          id="delete_promocode"
                                                          data-url="{{ route('delete_promocode') }}">{{ __('Remove') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif
                                    <div class="row mb-3 ">
                                        <div class="col-md-9">
                                            <strong>
                                                @if($plan->trial_days)
                                                    {{ __('Amount due today') }}
                                                @else
                                                    {{ __('Amount due') }}
                                                @endif
                                            </strong>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <h5>
                                                ${{ $plan->trial_days ? 0 :$amount }}
                                            </h5>
                                        </div>
                                        <button type="button"
                                                class="btn btn-download hover:btn-secondary large btn-lg btn-block mt-5 {{$amount==0?'':'d-none'}}"
                                                id="complete_order"
                                        >
                                            {{ __('Complete order') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('javascript_navbar')
    <script src="{{ asset('js/sweetalert@2.1.2_dist_sweetalert.min.js') }}"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script
        src="{{ asset('js/purchase'.(app()->environment()=='local'?'':'.min').'.js') }}?v={{ config('app.assets.version') }}"
        type="text/javascript"></script>
    @if($amount==0)
        <script>
            $(document).ready(function () {
                $('#payment_method [data-toggle="collapse"]').attr('disabled', true);
                $('#creditCard').removeClass('show');
                $('#payment_method > .card').attr('disabled', true);
                $('#payment_method input').attr('readonly', true);
            })
        </script>
    @endif
@endpush
