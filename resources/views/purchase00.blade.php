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
                                    <h4 class="font-weight-bold">{{ __('Card information') }}</h4>
                                    <div class="form-group pt-4 mb-4">
                                        <input type="text" class="form-control"
                                               id="card-holder-name" name="card-holder-name" autocomplete="cc-name"
                                               required>
                                        <label for="card-holder-name"
                                               class="text m-0">{{ __('Name on Card') }}<span class="text-danger fs-14"> * </span></label>
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
                                    <button type="button"
                                            class="btn btn-download hover:btn-secondary large btn-lg btn-block mt-4"
                                            id="card-button">
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
{{--                                    <div role="link" data-button="" data-funding-source="paypal"--}}
{{--                                         class="paypal-button"--}}
{{--                                    >--}}
{{--                                        <div class="paypal-button-label-container"><img--}}
{{--                                                src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAxcHgiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAxMDEgMzIiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaW5ZTWluIG1lZXQiIHhtbG5zPSJodHRwOiYjeDJGOyYjeDJGO3d3dy53My5vcmcmI3gyRjsyMDAwJiN4MkY7c3ZnIj48cGF0aCBmaWxsPSIjMDAzMDg3IiBkPSJNIDEyLjIzNyAyLjggTCA0LjQzNyAyLjggQyAzLjkzNyAyLjggMy40MzcgMy4yIDMuMzM3IDMuNyBMIDAuMjM3IDIzLjcgQyAwLjEzNyAyNC4xIDAuNDM3IDI0LjQgMC44MzcgMjQuNCBMIDQuNTM3IDI0LjQgQyA1LjAzNyAyNC40IDUuNTM3IDI0IDUuNjM3IDIzLjUgTCA2LjQzNyAxOC4xIEMgNi41MzcgMTcuNiA2LjkzNyAxNy4yIDcuNTM3IDE3LjIgTCAxMC4wMzcgMTcuMiBDIDE1LjEzNyAxNy4yIDE4LjEzNyAxNC43IDE4LjkzNyA5LjggQyAxOS4yMzcgNy43IDE4LjkzNyA2IDE3LjkzNyA0LjggQyAxNi44MzcgMy41IDE0LjgzNyAyLjggMTIuMjM3IDIuOCBaIE0gMTMuMTM3IDEwLjEgQyAxMi43MzcgMTIuOSAxMC41MzcgMTIuOSA4LjUzNyAxMi45IEwgNy4zMzcgMTIuOSBMIDguMTM3IDcuNyBDIDguMTM3IDcuNCA4LjQzNyA3LjIgOC43MzcgNy4yIEwgOS4yMzcgNy4yIEMgMTAuNjM3IDcuMiAxMS45MzcgNy4yIDEyLjYzNyA4IEMgMTMuMTM3IDguNCAxMy4zMzcgOS4xIDEzLjEzNyAxMC4xIFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDAzMDg3IiBkPSJNIDM1LjQzNyAxMCBMIDMxLjczNyAxMCBDIDMxLjQzNyAxMCAzMS4xMzcgMTAuMiAzMS4xMzcgMTAuNSBMIDMwLjkzNyAxMS41IEwgMzAuNjM3IDExLjEgQyAyOS44MzcgOS45IDI4LjAzNyA5LjUgMjYuMjM3IDkuNSBDIDIyLjEzNyA5LjUgMTguNjM3IDEyLjYgMTcuOTM3IDE3IEMgMTcuNTM3IDE5LjIgMTguMDM3IDIxLjMgMTkuMzM3IDIyLjcgQyAyMC40MzcgMjQgMjIuMTM3IDI0LjYgMjQuMDM3IDI0LjYgQyAyNy4zMzcgMjQuNiAyOS4yMzcgMjIuNSAyOS4yMzcgMjIuNSBMIDI5LjAzNyAyMy41IEMgMjguOTM3IDIzLjkgMjkuMjM3IDI0LjMgMjkuNjM3IDI0LjMgTCAzMy4wMzcgMjQuMyBDIDMzLjUzNyAyNC4zIDM0LjAzNyAyMy45IDM0LjEzNyAyMy40IEwgMzYuMTM3IDEwLjYgQyAzNi4yMzcgMTAuNCAzNS44MzcgMTAgMzUuNDM3IDEwIFogTSAzMC4zMzcgMTcuMiBDIDI5LjkzNyAxOS4zIDI4LjMzNyAyMC44IDI2LjEzNyAyMC44IEMgMjUuMDM3IDIwLjggMjQuMjM3IDIwLjUgMjMuNjM3IDE5LjggQyAyMy4wMzcgMTkuMSAyMi44MzcgMTguMiAyMy4wMzcgMTcuMiBDIDIzLjMzNyAxNS4xIDI1LjEzNyAxMy42IDI3LjIzNyAxMy42IEMgMjguMzM3IDEzLjYgMjkuMTM3IDE0IDI5LjczNyAxNC42IEMgMzAuMjM3IDE1LjMgMzAuNDM3IDE2LjIgMzAuMzM3IDE3LjIgWiI+PC9wYXRoPjxwYXRoIGZpbGw9IiMwMDMwODciIGQ9Ik0gNTUuMzM3IDEwIEwgNTEuNjM3IDEwIEMgNTEuMjM3IDEwIDUwLjkzNyAxMC4yIDUwLjczNyAxMC41IEwgNDUuNTM3IDE4LjEgTCA0My4zMzcgMTAuOCBDIDQzLjIzNyAxMC4zIDQyLjczNyAxMCA0Mi4zMzcgMTAgTCAzOC42MzcgMTAgQyAzOC4yMzcgMTAgMzcuODM3IDEwLjQgMzguMDM3IDEwLjkgTCA0Mi4xMzcgMjMgTCAzOC4yMzcgMjguNCBDIDM3LjkzNyAyOC44IDM4LjIzNyAyOS40IDM4LjczNyAyOS40IEwgNDIuNDM3IDI5LjQgQyA0Mi44MzcgMjkuNCA0My4xMzcgMjkuMiA0My4zMzcgMjguOSBMIDU1LjgzNyAxMC45IEMgNTYuMTM3IDEwLjYgNTUuODM3IDEwIDU1LjMzNyAxMCBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA2Ny43MzcgMi44IEwgNTkuOTM3IDIuOCBDIDU5LjQzNyAyLjggNTguOTM3IDMuMiA1OC44MzcgMy43IEwgNTUuNzM3IDIzLjYgQyA1NS42MzcgMjQgNTUuOTM3IDI0LjMgNTYuMzM3IDI0LjMgTCA2MC4zMzcgMjQuMyBDIDYwLjczNyAyNC4zIDYxLjAzNyAyNCA2MS4wMzcgMjMuNyBMIDYxLjkzNyAxOCBDIDYyLjAzNyAxNy41IDYyLjQzNyAxNy4xIDYzLjAzNyAxNy4xIEwgNjUuNTM3IDE3LjEgQyA3MC42MzcgMTcuMSA3My42MzcgMTQuNiA3NC40MzcgOS43IEMgNzQuNzM3IDcuNiA3NC40MzcgNS45IDczLjQzNyA0LjcgQyA3Mi4yMzcgMy41IDcwLjMzNyAyLjggNjcuNzM3IDIuOCBaIE0gNjguNjM3IDEwLjEgQyA2OC4yMzcgMTIuOSA2Ni4wMzcgMTIuOSA2NC4wMzcgMTIuOSBMIDYyLjgzNyAxMi45IEwgNjMuNjM3IDcuNyBDIDYzLjYzNyA3LjQgNjMuOTM3IDcuMiA2NC4yMzcgNy4yIEwgNjQuNzM3IDcuMiBDIDY2LjEzNyA3LjIgNjcuNDM3IDcuMiA2OC4xMzcgOCBDIDY4LjYzNyA4LjQgNjguNzM3IDkuMSA2OC42MzcgMTAuMSBaIj48L3BhdGg+PHBhdGggZmlsbD0iIzAwOWNkZSIgZD0iTSA5MC45MzcgMTAgTCA4Ny4yMzcgMTAgQyA4Ni45MzcgMTAgODYuNjM3IDEwLjIgODYuNjM3IDEwLjUgTCA4Ni40MzcgMTEuNSBMIDg2LjEzNyAxMS4xIEMgODUuMzM3IDkuOSA4My41MzcgOS41IDgxLjczNyA5LjUgQyA3Ny42MzcgOS41IDc0LjEzNyAxMi42IDczLjQzNyAxNyBDIDczLjAzNyAxOS4yIDczLjUzNyAyMS4zIDc0LjgzNyAyMi43IEMgNzUuOTM3IDI0IDc3LjYzNyAyNC42IDc5LjUzNyAyNC42IEMgODIuODM3IDI0LjYgODQuNzM3IDIyLjUgODQuNzM3IDIyLjUgTCA4NC41MzcgMjMuNSBDIDg0LjQzNyAyMy45IDg0LjczNyAyNC4zIDg1LjEzNyAyNC4zIEwgODguNTM3IDI0LjMgQyA4OS4wMzcgMjQuMyA4OS41MzcgMjMuOSA4OS42MzcgMjMuNCBMIDkxLjYzNyAxMC42IEMgOTEuNjM3IDEwLjQgOTEuMzM3IDEwIDkwLjkzNyAxMCBaIE0gODUuNzM3IDE3LjIgQyA4NS4zMzcgMTkuMyA4My43MzcgMjAuOCA4MS41MzcgMjAuOCBDIDgwLjQzNyAyMC44IDc5LjYzNyAyMC41IDc5LjAzNyAxOS44IEMgNzguNDM3IDE5LjEgNzguMjM3IDE4LjIgNzguNDM3IDE3LjIgQyA3OC43MzcgMTUuMSA4MC41MzcgMTMuNiA4Mi42MzcgMTMuNiBDIDgzLjczNyAxMy42IDg0LjUzNyAxNCA4NS4xMzcgMTQuNiBDIDg1LjczNyAxNS4zIDg1LjkzNyAxNi4yIDg1LjczNyAxNy4yIFoiPjwvcGF0aD48cGF0aCBmaWxsPSIjMDA5Y2RlIiBkPSJNIDk1LjMzNyAzLjMgTCA5Mi4xMzcgMjMuNiBDIDkyLjAzNyAyNCA5Mi4zMzcgMjQuMyA5Mi43MzcgMjQuMyBMIDk1LjkzNyAyNC4zIEMgOTYuNDM3IDI0LjMgOTYuOTM3IDIzLjkgOTcuMDM3IDIzLjQgTCAxMDAuMjM3IDMuNSBDIDEwMC4zMzcgMy4xIDEwMC4wMzcgMi44IDk5LjYzNyAyLjggTCA5Ni4wMzcgMi44IEMgOTUuNjM3IDIuOCA5NS40MzcgMyA5NS4zMzcgMy4zIFoiPjwvcGF0aD48L3N2Zz4"--}}
{{--                                                class="paypal-logo paypal-logo-paypal paypal-logo-color-blue"></div>--}}
{{--                                        <div class="paypal-button-spinner"></div>--}}
{{--                                    </div>--}}

                                    <div id='paypal-button-container'></div>


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
                                                ${{$plan->price}}
                                            </h5>
                                        </div>
                                    </div>
                                    @php($promocode=session()->has('promocode')?\App\Models\Promocode::active()->whereHas('plans',function ($q)use($plan){$q->where('plans.id',$plan->id);})->find(session()->get('promocode')):false)
                                    <div class="row {{ $promocode?'':'d-none' }}" id="after_discount">
                                        <div class="col-md-10 col-lg-10">
                                            <strong>
                                                {{ __('Discount') }} @if($promocode)({{ $promocode->title }})@endif
                                            </strong>
                                        </div>
                                        <div class="col-md-2 col-lg-2">
                                            <h5 class="total text-right">
                                                ${{$promocode?$promocode->calculate_price($plan->price):$plan->price}}
                                            </h5>
                                        </div>
                                    </div>
                                    <hr>
                                    @if(!$promocode && \App\Models\Promocode::active()->whereHas('plans',function ($q)use($plan){$q->where('plans.id',$plan->id);})->count())
                                        <a class="mb-0" data-toggle="collapse" href="#promocodeCollapse" role="button"
                                           aria-expanded="false"
                                           aria-controls="promocodeCollapse">{{ __('Do you have a coupon code?') }}</a>
                                        <div class="collapse" id="promocodeCollapse">
                                            <p id="promocode_msg" class="mb-1 color-primary"
                                               style="visibility: hidden;font-size: 11px;">1</p>
                                            <div class="form-group d-flex">
                                                <input type="text" class="form-control mr-2"
                                                       placeholder="{{ __('Promocode') }}"
                                                       aria-describedby="check_promocode" id="promocode"
                                                       style="height: 30px;">
                                                <div class="">
                                                    <span type="button" class="color-primary pointer-primary"
                                                          id="check_promocode">{{ __('Apply') }}</span>
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
                                                ${{ $plan->trial_days ? 0 :$plan->price }}
                                            </h5>
                                        </div>
                                    </div>
                                    {{--                                <p>{{ __("Secure checkout. For your convenience Arabsstock will store your encrypted payment information for future orders. Manage your payment information in your Account Details.") }}</p>--}}
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
    <script>
        function stop_waiting() {
            button.removeAttribute('disabled');
            $(document.body).css({'cursor': 'auto'});
        }
    </script>
    <script src="{{ asset('js/jquery.payform.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.js') }}"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $("input, textarea").on('focusout change submit blur', function () {
            if (!$(this).val()) {
                $(this).removeClass('not-empty');
            } else {
                $(this).addClass('not-empty');
            }
        });

        $('#billing_address_country').select2({width: '100%'});
        $('[data-toggle="collapse"]').on('click', function () {
            var $this = $(this), $target = $this.attr('data-target');
            $this.find('input[name="payment_method"]').prop("checked", true);
            if ($target == '#paypalCard') {
                $('#creditCard input').prop('disabled', true);
            } else {
                $('#creditCard input').prop('disabled', false);
            }
            if ($target == '#walletsCard' || $target == '#paypalCard') {
                button.setAttribute('disabled', true);
            } else {
                stop_waiting();
            }
        });
    </script>
    <script>
        const form = document.getElementById('purchase');
        const button = document.getElementById('card-button');
        const stripe = Stripe('{{ env("STRIPE_KEY") }}', {
            locale: '{{ strtolower(app()->getLocale()) }}'
        });
        document.addEventListener('DOMContentLoaded', async () => {
            init_payment_buttons({{ ($promocode?$promocode->calculate_price($plan->price):$plan->price)*100 }});
        });

        function init_payment_buttons(price) {
            var paymentRequest = stripe.paymentRequest({
                country: 'US',
                currency: 'usd',
                total: {
                    label: '{{ __($plan->{'title_en'}) }}',
                    amount: price,
                },
                requestPayerName: true,
                requestPayerEmail: true,
            });
            const elements = stripe.elements();
            const prButton = elements.create('paymentRequestButton', {
                paymentRequest: paymentRequest,
            });
            paymentRequest.canMakePayment().then(function (result) {
                if (result) {
                    if (result.applePay == false) {
                        $('.purchase .card.wallets .form-check img.applayPay').remove();
                        $('.purchase .card.wallets .form-check span').remove();
                    }
                    if (result.googlePay == false) {
                        $('.purchase .card.wallets .form-check img.googlePay').remove();
                        $('.purchase .card.wallets .form-check span').remove();
                    }
                    $('.wallets').slideDown()
                    prButton.mount('#payment-request-button');
                } else {
                    document.getElementById('payment-request-button').style.display = 'none';
                }
            });
            paymentRequest.on('paymentmethod', async (e) => {
                const {error: backendError, clientSecret, subscriptionId} = await fetch(
                    '{{ route('purchase') }}',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            _token: '{{ csrf_token() }}',
                            payment_method: 'card',
                            pmethod: e.paymentMethod.id,
                            plan_id: '{{ $plan_id }}'
                        }),
                    }
                ).then((r) => r.json());
                if (backendError) {
                    e.complete('fail');
                    return;
                }
                @if($plan->trial_days)
                let {error, setupIntent} = await stripe.confirmCardSetup(
                    clientSecret,
                    {
                        payment_method: e.paymentMethod.id,
                    },
                    {
                        handleActions: false,
                    }
                );
                if (error) {
                    stop_waiting();
                    swal(error.message);
                    return;
                }
                if (setupIntent.status === 'requires_action') {
                    let {error, setupIntent} = await stripe.confirmCardSetup(
                        clientSecret
                    );
                    if (error) {
                        window.location.href = '{!! route('payment.fail',['plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}';
                        return;
                    }
                }
                @if($plan->type=='package')
                    window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, setupIntent.id);
                @else
                    window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, subscriptionId);
                @endif
                @else
                let {error, paymentIntent} = await stripe.confirmCardPayment(
                    clientSecret,
                    {
                        payment_method: e.paymentMethod.id,
                    },
                    {
                        handleActions: false,
                    }
                );
                if (error) {
                    stop_waiting()
                    swal(error.message)
                }
                if (paymentIntent.status === 'requires_action') {
                    let {error, paymentIntent} = await stripe.confirmCardPayment(
                        clientSecret
                    );
                    if (error) {
                        window.location.href = '{!! route('payment.fail',['plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}';
                        return;
                    }
                }
                @if($plan->type=='package')
                    window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, paymentIntent.id);
                @else
                    window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, subscriptionId);
                @endif
                @endif
            });
        }


        const stripeElements = stripe.elements({
            fonts: [
                {
                    cssSrc: 'https://fonts.googleapis.com/css?family=Cairo&display=swap&subset=arabic',
                },
            ],
            locale: '{{ strtolower(app()->getLocale()) }}'
        });
        // const cardElement = stripeElements.create('card');
        const elementStyles = {
            base: {
                fontFamily: 'Cairo, sans-serif',
                fontSize: '14px',
                '::placeholder': {
                    color: '#cecece',
                },
            },
        };
        var cardElement = stripeElements.create('cardNumber', {
            style: elementStyles,
        });

        cardElement.mount('#card-element');
        const cardHolderName = document.getElementById('card-holder-name');


        var cardExpiry = stripeElements.create('cardExpiry', {
            style: elementStyles,
        });
        cardExpiry.mount('#card-expiry');

        var cardCvc = stripeElements.create('cardCvc', {
            style: elementStyles,
        });
        cardCvc.mount('#card-cvc');


        button.addEventListener('click', async (e) => {
            e.preventDefault();
            if ($('#promocode').length && $('#promocode').val() != '' && !$('#promocode').attr('readonly')) {
                swal('{{ __('Please apply promocode first') }}')
                return false;
            }
            button.setAttribute('disabled', !0);
            $(document.body).css({'cursor': 'wait'});
            if ($('input[name="payment_method"]:checked').val() == 'paypal') {
                form.submit();
                return false;
            } else {
                if (!cardHolderName.value) {
                    cardHolderName.focus();
                    stop_waiting();
                    return false;
                }
                const {paymentMethod, error} = await stripe.createPaymentMethod(
                    'card', cardElement, {
                        billing_details: {name: cardHolderName.value}
                    }
                );
                if (error) {
                    // Display "error.message" to the user...
                    swal(error.message);
                    stop_waiting();
                    return;
                } else {
                    const {error: backendError, clientSecret, subscriptionId} = await fetch(
                        '{{ route('purchase') }}',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                _token: '{{ csrf_token() }}',
                                payment_method: 'card',
                                pmethod: paymentMethod.id,
                                plan_id: '{{ $plan_id }}'
                            }),
                        }
                    ).then((r) => r.json());
                    if (backendError) {
                        swal(backendError)
                        stop_waiting();
                        return;
                    }
                    @if($plan->trial_days)
                    let {error, setupIntent} = await stripe.confirmCardSetup(
                        clientSecret,
                        {
                            payment_method: paymentMethod.id,
                        },
                        {
                            handleActions: false,
                        }
                    );
                    if (error) {
                        stop_waiting()
                        swal(error.message)
                    }
                    if (setupIntent.status === 'requires_action') {
                        let {error, setupIntent} = await stripe.confirmCardSetup(
                            clientSecret
                        );
                        if (error) {
                            window.location.href = '{!! route('payment.fail',['plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}';
                            return;
                        }
                    }
                    @if($plan->type=='package')
                        window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, setupIntent.id);
                    @else
                        window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, subscriptionId);
                    @endif
                    @else
                    let {error, paymentIntent} = await stripe.confirmCardPayment(
                        clientSecret,
                        {
                            payment_method: paymentMethod.id,
                        },
                        {
                            handleActions: false,
                        }
                    );
                    if (error) {
                        stop_waiting()
                        swal(error.message)
                    }
                    if (paymentIntent.status === 'requires_action') {
                        let {error, paymentIntent} = await stripe.confirmCardPayment(
                            clientSecret
                        );
                        if (error) {
                            window.location.href = '{!! route('payment.fail',['plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}';
                            return;
                        }
                    }
                    @if($plan->type=='package')
                        window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, paymentIntent.id);
                    @else
                        window.location.href = '{!! route('stripe.payment.status',[0,'plan_id'=>$plan_id, 'redirect' => route('me.plans')]) !!}'.replace(0, subscriptionId);
                    @endif
                    @endif
                }

            }
        });
        $('.paypal-button').on('click', function () {
            window.location.href = '{!! route('purchase',['plan_id'=>$plan->id,'payment_method'=>'paypal']) !!}';
        });
    </script>
    @if(\App\Models\Promocode::active()->whereHas('plans',function ($q)use($plan){$q->where('plans.id',$plan->id);})->count())
        <script>
            $('#check_promocode').on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    _promocode = $('#promocode');
                if (_promocode.val()) {
                    $this.append('<div class="btn-loader"></div>')
                    $.ajax({
                        type: "POST",
                        url: '{{ route('check_promocode',$plan) }}',
                        data: {_token: '{{ csrf_token() }}', promocode: _promocode.val(), plan_id: '{{ $plan->id }}'},
                        success: function (data) {
                            if (data.status == 1) {
                                $('#promocode_msg').removeClass('text-danger').addClass('color-primary').text(data.msg).css('visibility', 'visible')
                                $('#after_discount').removeClass('d-none');
                                $('#after_discount .total').text('$' + data.promocode.calculated);
                                init_payment_buttons(data.promocode.calculated * 100);
                                $('#promocode').attr('readonly', true)
                                $('#check_promocode').attr('disabled', true).hide()
                            } else {
                                $('#promocode_msg').removeClass('color-primary').addClass('text-danger').text(data.msg).css('visibility', 'visible')
                            }
                        },
                        complete() {
                        },
                        error(http) {
                            $('#promocode_msg').removeClass('color-primary').addClass('text-danger').text(http.responseJSON.message).css('visibility', 'visible')
                        },
                        dataType: 'json'
                    });
                } else {
                    _promocode.addClass('text-danger');
                    _promocode.focus();
                }
            });
        </script>
    @endif
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}&disable-funding=credit,card&vault=true&commit=false"></script>
    <!-- Set up a container element for the button -->
    <div id="paypal-button-container"></div>
    <script>
        paypal.Buttons({
            style: {shape: "pill"},
            createBillingAgreement: function () {
                return paypalCheckoutInstance.createPayment({
                    flow: 'vault'
                    // your other createPayment options here
                });
            },

            onApprove: function (data, actions) {
                // some logic here before tokenization happens below
                return paypalCheckoutInstance.tokenizePayment(data).then(function (payload) {
                    // Submit payload.nonce to your server
                });
            },

            onCancel: function () {
                // handle case where user cancels
            },

            onError: function (err) {
                // handle case where error occurs
            }
        }).render('#paypal-button-container');
    </script>

{{--    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_SANDBOX_CLIENT_ID') }}&intent=authorize"></script>--}}
{{--    <script>--}}
{{--        paypal.Buttons({--}}
{{--            createOrder: function () {--}}
{{--                return paypalCheckoutInstance.createPayment({--}}
{{--                    // Your createPayment options--}}
{{--                });--}}
{{--            }--}}
{{--            // Other configuration--}}
{{--        }).render('#paypal-button-container');--}}

{{--    </script>--}}
@endpush
