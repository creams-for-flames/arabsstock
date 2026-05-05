@extends('app')
@section('title')
    @if(App::isLocale('en'))
        {{trans('misc.prices and packages').' - '}}
    @else
        {{trans('misc.prices and packages').' - '}}
    @endif
@endsection

@section('description_custom')
    {{  trans('misc.prices and packages').' - '}}
@endsection

@push('header')
    <link rel="stylesheet"
          href="{{ asset('css/bootstrap-select@1.13.14_dist_css_bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pricing.css') }}?v={{ config('app.assets.version') }}">
@endpush

@include('includes.searchbar')
@section('content')
    @php($type_translated=__('global.'.ucfirst(\Illuminate\Support\Str::plural('Images'))))
    <div>
        <div class="container-fluid">
            <div class="my-5">
                <h2>{{ __('Exclusive content for each project, appropriate plan according to budget') }}</h2>
                <p>{{ __('Enjoy access to thousands of photos,videos and vectors that are updated and added daily.') }}</p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                @if($on_demand_plan)
                    <div class="col-12 col-lg-6 col-xl-4 mb-4">
                        <div class="priceBlock" id="on_demand">
                            <h2 class="bold"> {{ __('On demand') }} </h2>
                            <p class="mt-2 mb-5 text-muted">
                                <span> {!! implode('<br>',explode(',',__('Download as many as you need, with one year download validity'))) !!} </span>
                            </p>
                            <div class="">
                                <div class="">
                                    <form action="{{ route('purchase') }}" method="get" class="p-3" style="padding-top: 0.5rem!important;">
                                        <input type="hidden" name="plan_id" value="{{ $on_demand_plan->id }}">
                                        <div class="row">
                                            <div class="col-lg-9">
                                                <div class="mt-4">
                                                    <input type="range" min="1" max="500" value="2" class="slider"
                                                           id="credits_count" name="credits_count">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 sldier-credits">
                                                <div class="d-flex justify-content-center">
                                                    <div class="mt-3">
                                                        <input type="number" min="1" class="form-control"
                                                               on-demand-credits-input
                                                               style="display: none;"
                                                               placeholder="0000" value="2">
                                                        <span on-demand-credits></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="summery"> {{ __('This package enables you to download a combination of files or a number of') }}
                                            : </p>
                                        <div class="all-choices">
                                            <div class="choice">
                                                <i class="far fa-camera mr-1"> </i>
                                                <span class="bold" plan-images-count> 0 </span>
                                                {{ __('global.image') }}
                                            </div>
                                            <div>
                                                {{ __('Or') }}
                                            </div>
                                            <div class="choice">
                                                <i class="far fa-video mr-1"> </i>
                                                <span class="bold" plan-videos-count> 0 </span>
                                                {{ __('global.video') }}
                                            </div>
                                            <div>
                                                {{ __('Or') }}
                                            </div>
                                            <div class="choice">
                                                <i class="far fa-tilde mr-1"> </i>
                                                <span class="bold" plan-vectors-count> 0 </span>
                                                {{ __('global.vector') }}
                                            </div>
                                            <div>
                                                {{ __('Or') }}
                                            </div>
                                            <div class="choice">
                                                {{ __('Mix of them') }}
                                            </div>
                                        </div>
                                        <h1 class="price-tag my-0 mt-4">
                                            <sup> $ </sup>
                                            <span data-on-demand-price>80</span>
                                        </h1>
                                        <div class="price-tag">
                                            <sub data-on-demand-credit-price> {{ $on_demand_plan->credit_price }}
                                                $ {{ __('per credit') }} </sub>
                                        </div>
                                        <div class="mt-5">
                                            <button @if(!auth()->check())
                                                        type="button" data-toggle="modal" data-target="#login"
                                                    @else
                                                        type="submit"
                                                    @endif
                                                    class="btn btn-lg btn-primary hover:btn-secondary btn-lg btn-block font-weight-bold subscribe_btn">
                                                {{ __('global.subscribe-now') }}
                                            </button>
                                        </div>
                                        <div class="my-3">
                                            <div class="price-tag text-left">
                                                <sub>
                                                    {{ __('The validity period of the credit is one year') }} </sub>
                                            </div>
                                            <div class="price-tag text-left">
                                                <a href="{{ url('page/terms-of-service') }}">
                                                    <sub class="color-primary">
                                                        {{ __('admin.terms_conditions') }}</sub>
                                                </a>
                                                <sub>-</sub>
                                                <a href="{{ url('page/license-agreement') }}"><sub
                                                        class="color-primary">
                                                        {{ __('global.license-agreement') }}</sub>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12 {{ $on_demand_plan?'col-lg-4':'col-lg-6 col-xl-5' }} mb-4">
                    <div class="priceBlock" id="packages">
                        <h2 class="bold"> {{ __('One-time packages') }} </h2>
                        <p class="mt-2 mb-5 text-muted">
                            <span> {!! implode('<br>',explode(',',__('Download as many as you need, with one year download validity'))) !!} </span>
                        </p>
                        <ul class="nav nav-tabs inner-tabs my-tabs p-0" role="tablist">
                            @foreach($package_plans as $k=>$group)
                                <li class="nav-item m-1" role="presentation">
                                    <a class="nav-link"
                                       id="package_{{$k}}-tab"
                                       data-toggle="tab"
                                       href="#package_{{$k}}" role="tab"
                                       aria-controls="package_{{$k}}"
                                    >
                                        <span> {{ $k }} {{ $k<=10?__('credits'):__('credit') }} </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content p-3">
                            @foreach($package_plans as $k=>$group)
                                <div class="tab-pane fade"
                                     id="package_{{$k}}" role="tabpanel"
                                     aria-labelledby="package_{{$k}}-tab">
                                    <form action="{{ route('purchase') }}" method="get">
                                        <p class="summery"> {{ __('This package enables you to download a combination of files or a number of') }}
                                            : </p>
                                        <div class="all-choices">
                                            <div class="choice">
                                                <i class="far fa-camera mr-1"> </i>
                                                <span class="bold" plan-images-count> 0 </span>
                                                {{ __('global.image') }}
                                            </div>
                                            <div>
                                                {{ __('Or') }}
                                            </div>
                                            <div class="choice">
                                                <i class="far fa-video mr-1"> </i>
                                                <span class="bold" plan-videos-count> 0 </span>
                                                {{ __('global.video') }}
                                            </div>
                                            <div>
                                                {{ __('Or') }}
                                            </div>
                                            <div class="choice">
                                                <i class="far fa-tilde mr-1"> </i>
                                                <span class="bold" plan-vectors-count> 0 </span>
                                                {{ __('global.vector') }}
                                            </div>
                                            <div>
                                                {{ __('Or') }}
                                            </div>
                                            <div class="choice">
                                                {{ __('Mix of them') }}
                                            </div>
                                        </div>
                                        <h1 class="price-tag my-0 mt-4">
                                            <sup> $ </sup>
                                            <span plan-price></span>
                                        </h1>
                                        <div class="price-tag">
                                            <sub plan-credit-price> 9.00$ لكل نقطة </sub>
                                        </div>
                                        <div class="mt-5">
                                            <select name="plan_id" style="display: none;">
                                                @foreach($group as $plan)
                                                    <option
                                                        value="{{ $plan->id }}"
                                                        data-price="{{ number_format($plan->price) }}"
                                                        data-credits="{{ $plan->credits_count }}"
                                                        data-credit-price="{{ number_format($plan->credit_price,2) }}$ {{ __('per credit') }}"
                                                        data-images-count="{{ floor($plan->credits_count/\App\Models\Image::standard_credits()) }}"
                                                        data-videos-count="{{ floor($plan->credits_count/\App\Models\Video::standard_credits()) }}"
                                                        data-vectors-count="{{ floor($plan->credits_count/\App\Models\Vector::standard_credits()) }}"
                                                    >
                                                        {!! __("global.license.{$plan->license}") !!}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button
                                                @if(!auth()->check())
                                                    type="button"
                                                data-toggle="modal"
                                                data-type="login"
                                                data-target="#login"
                                                @else
                                                    type="submit"
                                                @endif
                                                class="btn btn-lg btn-primary hover:btn-secondary btn-lg btn-block font-weight-bold subscribe_btn {{ !auth()->check()?'auth-link':'' }}"
                                            >
                                                {{ __('global.subscribe-now') }}
                                            </button>
                                        </div>
                                        <div class="my-3">
                                            <div class="price-tag text-left">
                                                <sub> {{ __('The validity period of the credit is one year') }} </sub>
                                            </div>
                                            <div class="price-tag text-left">
                                                <a href="{{ url('page/terms-of-service') }}">
                                                    <sub class="color-primary"> {{ __('admin.terms_conditions') }}</sub>
                                                </a>
                                                <sub>-</sub>
                                                <a href="{{ url('page/license-agreement') }}">
                                                    <sub
                                                        class="color-primary"> {{ __('global.license-agreement') }}</sub>
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 {{ $on_demand_plan?'col-lg-4':'col-lg-6 col-xl-5' }} mb-4">
                    <div class="priceBlock" id="subscriptions">
                        <h2 class="bold"> {{ __('Monthly subscription packages') }} </h2>
                        <p class="mt-2 mb-5 text-muted">
                            <span> {!! implode('<br>',explode(',',__('Renewable monthly subscription at a better value , with one month download validity'))) !!} </span>
                        </p>
                        @if($subscription_plans->count())
                            <ul class="nav nav-tabs inner-tabs my-tabs p-0" role="tablist">
                                @foreach($subscription_plans as $k=>$group)
                                    <li class="nav-item m-1" role="presentation">
                                        <a
                                            class="nav-link {{ $loop->index==0?'active':'' }}"
                                            id="subscription_{{$k}}-tab"
                                            data-toggle="tab"
                                            href="#subscription_{{$k}}" role="tab"
                                            aria-controls="subscription_{{$k}}"
                                            aria-selected="true"
                                        >
                                            <span> {{ $k }} {{ $k<=10?__('credits'):__('credit') }} </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="tab-content p-3">
                                @foreach($subscription_plans as $k=>$group)
                                    <div
                                        class="tab-pane fade"
                                        id="subscription_{{$k}}" role="tabpanel"
                                        aria-labelledby="subscription_{{$k}}-tab"
                                    >
                                        <form action="{{ route('purchase') }}" method="get">
                                            <p class="summery"> {{ __('This package enables you to download a combination of files or a number of') }}
                                                : </p>
                                            <div class="all-choices">
                                                <div class="choice">
                                                    <i class="far fa-camera mr-1"> </i>
                                                    <span class="bold" plan-images-count> 0 </span>
                                                    {{ __('global.image') }}
                                                </div>
                                                <div>
                                                    {{ __('Or') }}
                                                </div>
                                                <div class="choice">
                                                    <i class="far fa-video mr-1"> </i>
                                                    <span class="bold" plan-videos-count> 0 </span>
                                                    {{ __('global.video') }}
                                                </div>
                                                <div>
                                                    {{ __('Or') }}
                                                </div>
                                                <div class="choice">
                                                    <i class="far fa-tilde mr-1"> </i>
                                                    <span class="bold" plan-vectors-count> 0 </span>
                                                    {{ __('global.vector') }}
                                                </div>
                                                <div>
                                                    {{ __('Or') }}
                                                </div>
                                                <div class="choice">
                                                    {{ __('Mix of them') }}
                                                </div>
                                            </div>
                                            <h1 class="price-tag my-0 mt-4">
                                                <sup> $ </sup>
                                                <span plan-price>80</span>
                                            </h1>
                                            <div class="price-tag">
                                                <sub plan-credit-price> 9.00$ {{ __('per credit') }} </sub>
                                            </div>
                                            <div class="case2-grid mt-5">
                                                <select class="selectpicker form-control" name="plan_id">
                                                    @foreach($group as $plan)
                                                        <option
                                                            value="{{ $plan->id }}"
                                                            @if($plan->type=='monthly')
                                                                selected
                                                            @endif
                                                            data-price="{{ number_format($plan->price) }}"
                                                            data-credits="{{ $plan->credits_count }}"
                                                            data-credit-price="{{ number_format($plan->credit_price,2) }}$ {{ __('per credit') }}"
                                                            data-images-count="{{ floor($plan->credits_count/\App\Models\Image::standard_credits()) }}"
                                                            data-videos-count="{{ floor($plan->credits_count/\App\Models\Video::standard_credits()) }}"
                                                            data-vectors-count="{{ floor($plan->credits_count/\App\Models\Vector::standard_credits()) }}"
                                                            data-subscribe-text="{{ $plan->trial_days?__("Start free trial"):__('global.subscribe-now') }}"
                                                        >
                                                            {!! __("plans.plan_title.{$plan->type}.".($plan->can_cancel?'no_contract':'contract')) !!}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button
                                                    class="btn btn-lg btn-primary hover:btn-secondary btn-lg btn-block font-weight-bold subscribe_btn {{ !auth()->check()?'auth-link':'' }}"
                                                    @if(!auth()->check())
                                                        type="button"
                                                    data-toggle="modal"
                                                    data-type="login"
                                                    data-target="#login"
                                                    @else
                                                        type="submit"
                                                    @endif
                                                >
                                                    {{ __('global.subscribe-now') }}
                                                </button>
                                            </div>
                                            <div class="my-3">
                                                <div class="price-tag text-left">
                                                    <sub> {{ __('Autorenew, subscription can be canceled at any time') }} </sub>
                                                </div>
                                                <div class="price-tag text-left">
                                                    <a href="{{ url('page/terms-of-service') }}">
                                                        <sub
                                                            class="color-primary"> {{ __('admin.terms_conditions') }}</sub>
                                                    </a>
                                                    <sub>-</sub>
                                                    <a href="{{ url('page/license-agreement') }}">
                                                        <sub
                                                            class="color-primary"> {{ __('global.license-agreement') }}</sub>
                                                    </a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="summery">{{ __('You are already subscribed to all renewable packages') }} </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center mt-1">
                <div class="col-12 col-md-10">
                    <table class="plans-table table table-hover">
                        <thead>
                        <tr>
                            <th>{{ __('views.Type') }} / {{ __('misc.price') }}</th>
                            <th>{{ __('Standard') }}</th>
                            <th>{{ __('Enhanced') }}</th>
                            <th>{{ __('Exclusive') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td field-key='unique_code'>{{ ucfirst(__('global.Pictures')) }}</td>
                            <td field-key='created_at'>{{ \App\Models\Image::standard_credits() }} {{ __('credit') }}</td>
                            <td field-key='status'>{{ \App\Models\Image::enhanced_credits() }} {{ __('credit') }}</td>
                            <td field-key='status'>{{ \App\Models\Image::exclusive_credits() }} {{ __('credit') }}</td>
                        </tr>
                        <tr>
                            <td field-key='unique_code'>{{ ucfirst(__('global.the_video')) }}</td>
                            <td field-key='created_at'>{{ \App\Models\Video::standard_credits() }} {{ __('credit') }}</td>
                            <td field-key='status'>{{ \App\Models\Video::enhanced_credits() }} {{ __('credit') }}</td>
                            <td field-key='status'>{{ \App\Models\Video::exclusive_credits() }} {{ __('credit') }}</td>
                        </tr>
                        <tr>
                            <td field-key='unique_code'>{{ ucfirst(__('global.vectors')) }}</td>
                            <td field-key='created_at'>{{ \App\Models\Vector::standard_credits() }} {{ __('credit') }}</td>
                            <td field-key='status'>{{ \App\Models\Vector::enhanced_credits() }} {{ __('credit') }}</td>
                            <td field-key='status'>{{ \App\Models\Vector::exclusive_credits() }} {{ __('credit') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="business">
            <div class="container-fluid">
                <div class="row h-100 justify-content-center">
                    <div class="col-md-10">
                        <div class="row h-100">
                            <div class="col-12 col-lg-8 details text-center text-lg-left">
                                <h2 class="mt-0"> باقات الأعمال </h2>
                                <div>
                                    <p style="line-height: 27px;" class="pb-1"> محتـوى "عربستوك" بخدمـات وإمكانـات
                                        أكـبـر، خصيصـاً </p>
                                    <p style="line-height: 27px;"> لأصحاب الأعمال والشركات الكبيرة والجهات الحكومية </p>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 cta align-items-center align-items-lg-end mb-4">
                                <a href="https://arabsstock.com/ar/business">
                                    <button class="btn btn-lg btn-transparent hover:btn-secondary btn-lg">
                                        اطلب الآن باقات الأعمال
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layer"></div>
        </div>
        <div class="container-fluid">
            <div class="contact-title my-5">
                <i class="far fa-headset"></i>
                <h3>{{trans('global.Did_encounter_problem _subscription_payment_download_any_other_inquiries')}}</h3>
                <h4><a href="{{route('technical-support')}}" class="color-primary">{{trans('global.Contact_now')}}</a>
                </h4>
            </div>
        </div>
    </div>
@endsection
@push('javascript_navbar')
    <script src="{{ asset('js/bootstrap-select@1.13.14_dist_js_bootstrap-select.min.js') }}"></script>
    <script>
        $('.my-select').selectpicker();
        $('.priceBlock .tab-content .tab-pane select[name="plan_id"]').on('change', function () {
            var $this = $(this),
                $option = $this.find(':selected');
            $this.closest('.tab-pane').find('.all-choices > div').show();
            $this.closest('.tab-pane').find('.price-tag [plan-price]').text($option.attr('data-price'));
            $this.closest('.tab-pane').find('[plan-credit-price]').text($option.attr('data-credit-price'));
            $this.closest('.tab-pane').find('[plan-images-count]').text($option.attr('data-images-count'));

            if ($option.attr('data-videos-count') == 0) {
                $this.closest('.tab-pane').find('[plan-videos-count]').closest('.choice').addClass('disabled-selection')
            }
            $this.closest('.tab-pane').find('[plan-videos-count]').text($option.attr('data-videos-count'));
            $this.closest('.tab-pane').find('[plan-vectors-count]').text($option.attr('data-vectors-count'));
            $this.closest('.tab-pane').find('.subscribe_btn').text($option.attr('data-subscribe-text'));
        }).change();
        let $first = $('#packages > .my-tabs > .nav-item > .nav-link').first();
        $first.click()
        $($first.attr('href')).addClass('active show');
        $first = $('#subscriptions > .my-tabs > .nav-item > .nav-link').first();
        $first.click()
        $($first.attr('href')).addClass('active show');
        @if($on_demand_plan)
        $('#credits_count').on('change input', function (e) {
            @php($prices=\App\Models\Plan::where('type','package')->where('on_demand',0)->where('status',1)->where('hidden',0)->where('credits_count','>',1)->select('credit_price','credits_count')->orderBy('credits_count')->get()->pluck('credit_price','credits_count')->toArray())
                prices = {!! json_encode($prices,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!};
            prices[0] = 12;
            var $this = $(this),
                $credits_count = parseInt($this.val()),
                $max = parseInt($this.attr('max')),
                $credit_price = {{ $prices[min(array_keys($prices))] }};

            for (const credits in prices) {
                if (credits > $credits_count) {
                    break;
                }
                $credit_price = prices[credits];
            }
            if (!isNaN($credits_count)) {
                if (($credits_count / $max) > 0.95 && $credits_count < 10000) {
                    $this.attr('max', $max * 2);
                } else if ((($credits_count / $max) < 0.1) && $max > 10) {
                    $this.attr('max', $max / 2);
                }
                $('[on-demand-credits]').text($credits_count + ' {{ __('credit') }}');
                let image_credits = {{ \App\Models\Image::standard_credits() }};
                let video_credits = {{ \App\Models\Video::standard_credits() }};
                let vector_credits = {{ \App\Models\Vector::standard_credits() }};
                $('#on_demand .all-choices [plan-images-count]').text(Math.floor($credits_count / image_credits));
                $('#on_demand .all-choices [plan-videos-count]').text(Math.floor($credits_count / video_credits));
                $('#on_demand .all-choices [plan-vectors-count]').text(Math.floor($credits_count / vector_credits));
                $('#on_demand .all-choices .choice.disabled-selection').removeClass('disabled-selection');
                if ($credits_count < (image_credits + vector_credits)) {
                    $('#on_demand .all-choices .choice:last').addClass('disabled-selection')
                }
                if ($credits_count < video_credits) {
                    $('#on_demand .all-choices [plan-videos-count]').closest('.choice').addClass('disabled-selection');
                }
                $('[data-on-demand-price]').text(($credits_count * $credit_price).toFixed(2));
                $('[data-on-demand-credit-price]').text($credit_price + '$ {{ __('per credit') }}');
            }
        });
        $('#credits_count').trigger('change')
        $(document).on('click', '[on-demand-credits]', function () {
            var $this = $(this);
            $this.hide();
            $('[on-demand-credits-input]').show().focus()
        });
        $(document).on('keyup change', '[on-demand-credits-input]', function () {
            $('#credits_count').val($(this).val()).trigger('change');
        });
        $(document).on('focusout', '[on-demand-credits-input]', function () {
            $(this).hide();
            $('[on-demand-credits]').show()
        });
        @endif
    </script>
@endpush
