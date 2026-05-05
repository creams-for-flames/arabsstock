@extends('includes.profile')
@push('header')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/purchase.css') }}?v={{ config('app.assets.version') }}">
@endpush
@section('profile_content')
    @php
        /**@var $user \App\Models\User*/
            $user=auth()->user();
            $defaultPaymentMethod=$user->defaultPaymentMethod();
    @endphp
    <div class="container plans">
        <div class="mb-4">
            <h3 class="mb-3">{{ trans('misc.plans') }}</h3>
            <p>  {{ trans('misc.Details-plans') }} </p>
            <ul class="nav mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                        class="mb-0 nav-link active"
                        id="user_subscriptions-tab"
                        data-toggle="pill"
                        data-target="#user_subscriptions"
                        type="button"
                        role="tab"
                        aria-controls="user_subscriptions"
                        aria-selected="true"
                    >
                        {{ __('My Subscriptions') }}
                    </button>
                </li>
                @if($active_team_subscriptions->count())
                    <li class="nav-item" role="presentation">
                        <button
                            class="mb-0 nav-link"
                            id="team_subscriptions-tab"
                            data-toggle="pill"
                            data-target="#team_subscriptions"
                            type="button"
                            role="tab"
                            aria-controls="team_subscriptions"
                            aria-selected="true"
                        >
                            {{ __('Team Subscriptions') }}
                        </button>
                    </li>
                @endif
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="user_subscriptions" role="tabpanel"
                     aria-labelledby="user_subscriptions-tab">
                    <div class="wrap-center center-block">
                        @if(($subscriptions->count() + $video_subscriptions->count() + $image_subscriptions->count() + $vector_subscriptions->count())==0)
                            <section class="border rounded p-4">
                                <p class="mb-0"> {{ __("You don't have any subscription") }} </p>
                                <small>
                                    <a href="{{ route('plans') }}"> {{ trans('misc.prices and packages') }} </a>
                                </small>
                            </section>
                        @endif
                        @if($subscriptions->count())
                            @foreach($subscriptions as $r)
                                @php($stripe_subscription=$r->payment_method_id==\App\Models\PaymentMethod::STRIPE && $r->subscription_id?\Stripe\Subscription::retrieve(['id'=>$r->subscription_id,'expand'=>['default_payment_method']]):null)
                                <section class="border rounded p-3 mb-3 subscription" data-id="{{ $r->id }}">
                                    <div class="mb-3">
                                        <h4>#{{ $r->id }} {!! $r->title !!}
                                            @if($r->status==\App\Models\Subscription::STATUS_PENDING)
                                                <small class="text-danger">({{trans('global.status.pending')}})</small>
                                            @elseif(in_array($r->status,[\App\Models\Subscription::STATUS_REFUND]))
                                                <small class="text-danger">({{trans('global.status.notactive')}}
                                                    )</small>
                                            @elseif($r->status==\App\Models\Subscription::STATUS_ACTIVE)
                                                @if($r->isActive())
                                                    <small class="text-success">({{trans('global.status.active')}}
                                                        )</small>
                                                @else
                                                    <small class="text-danger">({{trans('global.status.finished')}}
                                                        )</small>
                                                @endif
                                            @endif
                                        </h4>
                                        <small
                                            class="d-block"> {!! __("plans.plan_title.{$r->plan->type}.".($r->plan->can_cancel?'no_contract':'contract')) !!} </small>
                                    </div>
                                    @if($r->ends_at)
                                        <div class="mb-3">
                                            <h5> {{ __('Plan expires') }} </h5>
                                            <small> {{ format_date('d M Y - h:i a',$r->finished_at) }} </small>
                                        </div>
                                    @endif
                                    <div class="mb-2">
                                        <h5> {{ __('Credits left') }} </h5>
                                        <small> {{ $r->remaining_credits }} {{ __('credit') }} </small>
                                        @if($r->plan_type=='annual')
                                            <p><small>{{ __('Credits are automatically renewed every month') }}</small>
                                            </p>
                                        @endif
                                    </div>
                                    @if($r->plan_type!='package')
                                        <div>
                                            @if($r->renewal)
                                                @if($stripe_subscription)
                                                    @php($payment_method=$stripe_subscription->default_payment_method?:$defaultPaymentMethod)
                                                    <div
                                                        class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <img
                                                                src="{{ asset("img/cards/{$payment_method->card->brand}.svg") }}"
                                                                class="rounded" width="17">
                                                            <div class="d-flex flex-column ml-3">
                                                                <div
                                                                    class="my-1">
                                                                    <span
                                                                        style="direction: ltr">•••• {{ $payment_method->card->last4 }}</span>
                                                                    <a href="javascript:;"
                                                                       class="ml-2 text-muted"
                                                                       data-update-pmethod
                                                                       data-pmethod="{{ $payment_method->id }}"
                                                                       data-subscription-id="{{ $r->id }}"
                                                                       title="{{ __('Update Payment Method') }}"
                                                                    >
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             width="12"
                                                                             height="12" fill="currentColor"
                                                                             class="bi bi-pencil-fill"
                                                                             viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/>
                                                                        </svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <h5>
                                                    {{ __('Auto renewal') }}:
                                                    <span
                                                        class="text-success"> {{ $r->renewal?__('misc.active'):__('misc.stopped') }} </span>
                                                    {{--                                        <i class="far fa-question-circle ml-2" data-toggle="tooltip"--}}
                                                    {{--                                           data-placement="top"--}}
                                                    {{--                                           title="Tooltip on top"></i>--}}
                                                </h5>
                                                @if($r->status==\App\Models\Subscription::STATUS_ACTIVE)
                                                    <button data-cancel-url="{{route('subscribtion.cancel',$r->id)}}"
                                                            data-activate-url="{{route('subscribtion.activate',$r->id)}}"
                                                            class="btn rounded-radius btn-outline-secondary mt-2 suspend_subscription btn-sm"
                                                            data-ends_at="{{ format_date('d M, Y',$r->ends_at) }}">
                                                        {{ __('global.suspend_subscription') }}
                                                    </button>
                                                    @if($stripe_subscription && !$r->isActive() )
                                                        @php($latest_invoice=optional($stripe_subscription)->latest_invoice)
                                                        @if($invoice=$latest_invoice?\Stripe\Invoice::retrieve($latest_invoice):null)
                                                            @if($invoice && !$invoice->paid)
                                                                <button
                                                                    data-url="{{route('pay_invoice',$invoice->id)}}"
                                                                    class="btn rounded-radius btn-primary mt-2 pay_now btn-sm"
                                                                    data-price="{{ $invoice->amount_due }}"
                                                                    data-title="{!! $r->plan->description !!}"
                                                                >
                                                                    {{ __('Pay Now') }}
                                                                </button>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                <h5>
                                                    {{ __('Auto renewal') }}:
                                                    <span class="text-danger-"> {{ __('misc.stopped') }} </span>
                                                    {{--                                        <i class="far fa-question-circle ml-2" data-toggle="tooltip"--}}
                                                    {{--                                           data-placement="top"--}}
                                                    {{--                                           title="Tooltip on top"></i>--}}
                                                </h5>
                                                @if($r->status==\App\Models\Subscription::STATUS_ACTIVE)
                                                    <button
                                                        data-activate-url="{{route('subscribtion.activate',$r->id)}}"
                                                        data-cancel-url="{{route('subscribtion.cancel',$r->id)}}"
                                                        class="btn rounded-radius btn-primary mt-2 activate_subscription btn-sm">
                                                        {{ __('global.activate_subscription') }}
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </section>
                            @endforeach
                        @endif
                        @if($image_subscriptions->count())
                            <div class="mb-4">
                                <h3 class="mb-3">{{ __(':type Subscriptions',['type'=>__('global.Images')]) }}</h3>
                                @foreach($image_subscriptions as $r)
                                    <section class="border rounded p-3 mb-3">
                                        <div class="mb-3">
                                            <h4>#{{ $r->id }} {!! $r->plan->description !!} </h4>
                                            <small
                                                class="d-block"> {!! __("plans.plan_title.{$r->plan->type}.".($r->plan->can_cancel?'no_contract':'contract')) !!} </small>
                                        </div>
                                        <div class="mb-3">
                                            <h5> {{ __('Plan expires') }} </h5>
                                            <small> {{ format_date('d M Y - h:i a',$r->ends_at) }} </small>
                                        </div>
                                        <div class="mb-3">
                                            <h5> {{ __('global.user-plans.fields.download-remaining') }} </h5>
                                            <small> {{ $r->download_remaining }} {{ __('misc.downloads') }} </small>
                                        </div>
                                        @if($r->plan_type!='package')
                                            <div>
                                                @if($r->renewal)
                                                    <h5>
                                                        {{ __('Auto renewal') }}:
                                                        <span
                                                            class="text-success"> {{ $r->renewal?__('misc.active'):__('misc.stopped') }} </span>
                                                    </h5>
                                                    <a href="{{route('subscribtion_image.cancel',$r->id)}}"
                                                       class="btn rounded-radius btn-outline-secondary mt-2">
                                                        {{ __('global.suspend_subscription') }}
                                                    </a>
                                                @else
                                                    <h5>
                                                        {{ __('Auto renewal') }}:
                                                        <span class="text-danger"> {{ __('misc.stopped') }} </span>
                                                    </h5>
                                                @endif
                                            </div>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        @endif
                        @if($video_subscriptions->count())
                            <div class="mb-4">
                                <h3 class="mb-3">{{ __(':type Subscriptions',['type'=>__('global.Videos')]) }}</h3>
                                @foreach($video_subscriptions as $r)
                                    <section class="border rounded p-3 mb-3">
                                        <div class="mb-3">
                                            <h4>#{{ $r->id }} {!! $r->plan->description !!} </h4>
                                            <small
                                                class="d-block"> {!! __("plans.plan_title.{$r->plan->type}.".($r->plan->can_cancel?'no_contract':'contract')) !!} </small>
                                        </div>
                                        <div class="mb-3">
                                            <h5> {{ __('Plan expires') }} </h5>
                                            <small> {{ format_date('d M Y - h:i a',$r->ends_at) }} </small>
                                        </div>
                                        <div class="mb-3">
                                            <h5> {{ __('global.user-plans.fields.download-remaining') }} </h5>
                                            <small> {{ $r->download_remaining }} {{ __('misc.downloads') }} </small>
                                        </div>
                                        @if($r->plan_type!='package')
                                            <div>
                                                @if($r->renewal)
                                                    <h5>
                                                        {{ __('Auto renewal') }}:
                                                        <span
                                                            class="text-success"> {{ $r->renewal?__('misc.active'):__('misc.stopped') }} </span>
                                                    </h5>
                                                    <a href="{{route('subscribtions_video.cancel',$r->id)}}"
                                                       class="btn rounded-radius btn-outline-secondary mt-2">
                                                        {{ __('global.suspend_subscription') }}
                                                    </a>
                                                @else
                                                    <h5>
                                                        {{ __('Auto renewal') }}:
                                                        <span class="text-danger"> {{ __('misc.stopped') }} </span>
                                                    </h5>
                                                @endif
                                            </div>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        @endif
                        @if($vector_subscriptions->count())
                            <div class="mb-4">
                                <h3 class="mb-3">{{ __(':type Subscriptions',['type'=>__('global.Vectors')]) }}</h3>
                                @foreach($vector_subscriptions as $r)
                                    <section class="border rounded p-3 mb-3">
                                        <div class="mb-3">
                                            <h4>#{{ $r->id }} {!! $r->plan->description !!} </h4>
                                            <small
                                                class="d-block"> {!! __("plans.plan_title.{$r->plan->type}.".($r->plan->can_cancel?'no_contract':'contract')) !!} </small>
                                        </div>
                                        <div class="mb-3">
                                            <h5> {{ __('Plan expires') }} </h5>
                                            <small> {{ format_date('d M Y - h:i a',$r->ends_at) }} </small>
                                        </div>
                                        <div class="mb-3">
                                            <h5> {{ __('global.user-plans.fields.download-remaining') }} </h5>
                                            <small> {{ $r->download_remaining }} {{ __('misc.downloads') }} </small>
                                        </div>
                                        @if($r->plan_type!='package')
                                            <div>
                                                @if($r->renewal)
                                                    <h5>
                                                        {{ __('Auto renewal') }}:
                                                        <span
                                                            class="text-success"> {{ $r->renewal?__('misc.active'):__('misc.stopped') }} </span>
                                                    </h5>
                                                    <a href="{{route('subscribtions_vector.cancel',$r->id)}}"
                                                       class="btn rounded-radius btn-outline-secondary mt-2">
                                                        {{ __('global.suspend_subscription') }}
                                                    </a>
                                                @else
                                                    <h5>
                                                        {{ __('Auto renewal') }}:
                                                        <span class="text-danger"> {{ __('misc.stopped') }} </span>
                                                    </h5>
                                                @endif
                                            </div>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade " id="team_subscriptions" role="tabpanel"
                     aria-labelledby="team_subscriptions-tab">
                    <div class="wrap-center center-block">
                        @if($active_team_subscriptions->count())
                            @foreach($active_team_subscriptions as $r)
                                <section class="border rounded p-3 mb-3 subscription">
                                    <div class="mb-3">
                                        <h4>#{{ $r->id }} {!! $r->plan->description !!}
                                            - {{ $r->plan->members_limit }} {{ __('misc.members') }}
                                            @if($r->status==\App\Models\Subscription::STATUS_PENDING)
                                                <small class="text-danger">({{trans('global.status.pending')}})</small>
                                            @elseif(in_array($r->status,[\App\Models\Subscription::STATUS_REFUND]))
                                                <small class="text-danger">({{trans('global.status.notactive')}}
                                                    )</small>
                                            @elseif($r->status==\App\Models\Subscription::STATUS_ACTIVE)
                                                @if($r->isActive())
                                                    <small class="text-success">({{trans('global.status.active')}}
                                                        )</small>
                                                @else
                                                    <small class="text-danger">({{trans('global.status.finished')}}
                                                        )</small>
                                                @endif
                                            @endif
                                        </h4>
                                        <small
                                            class="d-block"> {!! __("plans.plan_title.{$r->plan->type}.".($r->plan->can_cancel?'no_contract':'contract')) !!} </small>
                                    </div>
                                    @if($r->ends_at)
                                        <div class="mb-3">
                                            <h5> {{ __('Plan expires') }} </h5>
                                            <small> {{ format_date('d M Y - h:i a',$r->finished_at) }} </small>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <h5> {{ __('Credits left') }} </h5>
                                        <small> {{ $r->remaining_credits }} {{ __('credit') }} </small>
                                        @if($r->plan_type=='annual')
                                            <p><small>{{ __('Credits are automatically renewed every month') }}</small>
                                            </p>
                                        @endif
                                    </div>
                                    <a href="{{ route('team.subscription_credits',$r->id) }}" data-fancybox
                                       data-type="iframe" data-preload="false" class="text-info"
                                       style="text-decoration: underline!important;">{{ __('Credits distribution') }}</a>
                                </section>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('javascript_navbar')
    <div class="modal fade" id="payNowModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Pay the subscription invoice') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form class="purchase" method="post" id="purchase"
                          action="{{ route('pay_invoice',0) }}">
                        @csrf
                        <input type="hidden" id="pmethod" name="pmethod">
                        <div id="payment_method">
                            <div class="card mb-2 p-0 border-0" style="display: none;">
                                <div class="card-body border-0">
                                    <div id="payment-request-button"></div>
                                </div>
                            </div>
                            <div class="Divider mb-2" style="display: none;">
                                <hr>
                                <p class="Divider-Text Text">{{ __('Or pay another way') }}</p></div>
                            <div class="card credit mb-3 p-0 border-0">
                                <div id="creditCard" class="creditCard">
                                    <div class="card-body border-0">
                                        <div id="payment-request-button"></div>
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
                                                       id="card-holder-name" name="card-holder-name"
                                                       autocomplete="cc-name"
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
                                                data-planName="plan title"
                                                data-planId="0"
                                                data-price="0"
                                                data-purchaseUrl="{{ route('pay_invoice',0) }}"
                                                data-failUrl="{{ route('me.plans') }}"
                                                data-statusUrl="{!! route('me.plans') !!}"
                                        >
                                            {{ __('Complete the payment process') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('views.Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateStripePaymentMethod">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Update Payment Method') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form class="purchase" method="post">
                        @csrf
                        <input type="hidden" name="subscription_id" value="0">
                        <div>
                            <div class="payment_methods">
                                @foreach($user->paymentMethods() as $pmethod)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input mt-3 scale-check" type="radio"
                                               name="pmethod"
                                               id="pmethod_{{ $pmethod->id }}"
                                               value="{{ $pmethod->id }}">
                                        <label class="form-check-label ml-3" for="pmethod_{{ $pmethod->id }}">
                                            <div
                                                class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex flex-row align-items-center">
                                                    <img
                                                        src="{{ asset("img/cards/{$pmethod->card->brand}.svg") }}"
                                                        class="rounded" width="32">
                                                    <div class="d-flex flex-column ml-3">
                                                        <span
                                                            class="semi-bold">{{ ucfirst($pmethod->card->brand) }} <span>•••• {{ $pmethod->card->last4 }}</span></span>
                                                        <span
                                                            class="text-muted fs-12">Expires {{ $pmethod->card->exp_month }} {{ $pmethod->card->exp_year }} </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input mt-3 scale-check" type="radio"
                                       name="pmethod"
                                       id="new_pmethod" value="new">
                                <label class="form-check-label ml-3 pt-2" for="new_pmethod">
                                    <div class="ml-3 mt-1">
                                        {{ __('Add a new card') }}
                                    </div>
                                </label>
                            </div>
                            <div id="create_new_pmethod" class="card credit mb-3 p-0 border-0" style="display: none;">
                                <div class="creditCard">
                                    <div class="card-body border-0">
                                        <div
                                        >
                                            <h4 class="font-weight-bold">{{ __('Card information') }}</h4>
                                            <div class="form-group pt-3 mb-4">
                                                <label for="card-element2"
                                                       class="text ">{{ __('Credit Card Number') }}<span
                                                        class="text-danger fs-14"> * </span></label>
                                                <div id="card-element2"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group pt-3 mb-4">
                                                        <label for="card-expiry2"
                                                               class="text ">{{ __('Expiration Date') }}<span
                                                                class="text-danger fs-14"> * </span></label>
                                                        <div id="card-expiry2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group pt-3 mb-4">
                                                        <label for="card-cvc2"
                                                               class="text ">{{ __('CVC') }}<span
                                                                class="text-danger fs-14"> * </span></label>
                                                        <div id="card-cvc2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save_pmethod">{{ __('views.Save') }}</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('views.Close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/sweetalert@2.1.2_dist_sweetalert.min.js') }}"></script>

    <script src="https://js.stripe.com/v3/"></script>
    <script
        src="{{ asset('js/purchase'.(app()->environment()=='local'?'':'.min').'.js') }}?v={{ config('app.assets.version') }}"
        type="text/javascript"></script>
    <script src="{{ asset('js/fancyapps_ui@4.0_dist_fancybox.umd.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fancyapps_ui@4.0_dist_fancybox.css') }}"/>
    <style>
        .swal-title{
            font-size: 20px;
            color: #000;
        }
        .swal-text{font-size: 14px;color: #9b9b9b}
    </style>
    <script>
        $(document).on('click', '.suspend_subscription', function (e) {
            e.preventDefault();
            var $this = $(this);
            swal({
                title: "{!! __('Are you sure you want to turn auto renewal off?') !!}",
                text: "{!! __("Disabling auto renewal means your plan will expire on :time") !!}".replace(':time', $this.attr('data-ends_at')),
                buttons: ['{{ __('global.Cancel') }}', "{{ __('global.Sure') }}"],
                dangerMode: true,
            })
                .then((confirm) => {
                    if (confirm) {
                        $.ajax({
                            type: "POST",
                            url: $this.attr('data-cancel-url'),
                            data: {_token: CSRF_TOKEN},
                            success: function ($response) {
                                $this.removeClass('btn-outline-secondary suspend_subscription').addClass('btn-primary activate_subscription').text('{{ __('global.activate_subscription') }}');
                                $this.prev().find('span').text('{{ __('misc.stopped') }}')
                                update_subscription_data($this.closest('section.subscription[data-id]').attr('data-id'))
                            },
                            dataType: 'JSON'
                        });
                    }
                });
        });
        $(document).on('click', '.activate_subscription', function (e) {
            e.preventDefault();
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: $this.attr('data-activate-url'),
                data: {_token: CSRF_TOKEN},
                success: function ($response) {
                    if ($response.status) {
                        $this.removeClass('btn-primary activate_subscription').addClass('btn-outline-secondary suspend_subscription')
                            .text('{{ __('global.suspend_subscription') }}');
                        $this.prev().find('span').text('{{ __('misc.active') }}');
                        update_subscription_data($this.closest('section.subscription[data-id]').attr('data-id'))
                    } else {
                        notify($response.message, 'danger')
                    }
                },
                dataType: 'JSON'
            });
        });
        $(document).on('click', '.pay_now', function (e) {
            e.preventDefault();
            var $this = $(this);
            paymentRequest.update({
                country: 'US',
                currency: 'usd',
                total: {
                    label: $this.attr('data-title'),
                    amount: parseInt($this.attr('data-price')),
                },
            });
            $('#payNowModal #card-button').attr('data-purchaseUrl', $this.attr('data-url'))
            $('#payNowModal form').attr('action', $this.attr('data-url'))
            $('#payNowModal').modal('show');
        });
        $(document).on('click', '[data-update-pmethod]', function (e) {
            e.preventDefault();
            var $this = $(this);
            if (!window.cardElement._destroyed) {
                window.cardElement.destroy();
                window.cardExpiry.destroy();
                window.cardCvc.destroy();
            }
            cardElement = stripeElements.create('cardNumber', {
                style: elementStyles,
            });

            cardElement.mount('#card-element2');

            cardExpiry = stripeElements.create('cardExpiry', {
                style: elementStyles,
            });
            cardExpiry.mount('#card-expiry2');

            cardCvc = stripeElements.create('cardCvc', {
                style: elementStyles,
            });
            cardCvc.mount('#card-cvc2');

            $('#updateStripePaymentMethod').modal('show');
            $('#updateStripePaymentMethod input[name="subscription_id"]').val($this.attr('data-subscription-id'));
            $('#updateStripePaymentMethod input[name="pmethod"][value="' + $this.attr('data-pmethod') + '"]').prop("checked", true).trigger('change');
        });
        $(document).on('change', '#updateStripePaymentMethod input[name="pmethod"]', function () {
            var pmethod = $('#updateStripePaymentMethod input[name="pmethod"]:checked').val()
            if (pmethod == 'new') {
                $('#create_new_pmethod').fadeIn();
            } else {
                $('#create_new_pmethod').fadeOut();
            }
        });
        $('#save_pmethod').on('click', async function () {
            $(document.body).css({'cursor': 'wait'});
            $('#updateStripePaymentMethod').modal('hide');
            var subscription_id = $('#updateStripePaymentMethod input[name="subscription_id"]').val();
            if ($('#updateStripePaymentMethod input[name="pmethod"]:checked').val() == 'new') {
                var {paymentMethod, error} = await stripe.createPaymentMethod(
                    'card', cardElement, {
                        billing_details: {name: cardHolderName.value}
                    }
                );
                if (error) {
                    // Display "error.message" to the user...
                    notify(error.message, 'danger')
                    stop_waiting();
                    return;
                }

                $('.payment_methods').append(`<div class="form-check mb-2">
                                        <input class="form-check-input mt-3 scale-check" type="radio"
                                               name="pmethod"
                                               id="pmethod_{pmethod}"
                                               value="{pmethod}">
                                        <label class="form-check-label ml-3" for="pmethod_{pmethod}">
                                            <div
                                                class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex flex-row align-items-center">
                                                    <img
                                                        src="{{ asset("img/cards") }}/{brand}.svg"
                                                        class="rounded" width="32">
                                                    <div class="d-flex flex-column ml-3">
                                                        <span
                                                            class="semi-bold">{brand} <span>•••• {last4}</span></span>
                                                        <span
                                                            class="text-muted fs-12">Expires {exp_month} {exp_year} </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>`.replaceAll('{pmethod}', paymentMethod.id).replaceAll('{brand}', paymentMethod.card.brand)
                    .replaceAll('{last4}', paymentMethod.card.last4).replaceAll('{exp_month}', paymentMethod.card.exp_month).replaceAll('{exp_year}', paymentMethod.card.exp_year))
                $('#updateStripePaymentMethod input[name="pmethod"][value="' + paymentMethod.id + '"]').prop("checked", true).trigger('change');
                $('[data-subscription-id="' + subscription_id + '"]').attr('data-pmethod', paymentMethod.id);
            }

            $.ajax({
                type: "POST",
                url: '{{ route('subscribtion.update_stripe_payment_method') }}',
                data: $('#updateStripePaymentMethod form').serialize(),
                success: function ($response) {
                    update_subscription_data(subscription_id)
                    stop_waiting();
                    notify($response.message, 'success');
                },
                dataType: 'JSON'
            });
        });
        function update_subscription_data(id) {
            $.ajax({
                type: "GET",
                url: '{{ route('me.plans') }}',
                success: function ($response) {
                    $('section[data-id="' + id + '"]').html($($response).find('section[data-id="' + id + '"]').html());
                },
                dataType: 'HTML'
            });
        }
    </script>
@endpush
