@extends('app')
<style>
    header .navbar-light .navbar-nav .nav-link{
        display: inherit;
    }
    header{
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.06);
    }
</style>
@section('title')
    {{trans('views.Payment Status')}} -
@endsection
@if(isset($subscription))
@section('cj')
    <script type="text/javascript">
        if (!window.cj) window.cj = {};
        cj.order = {
            enterpriseId: '1563613',
            pageType: 'conversionConfirmation',
            userId: '{{ auth()->id() }}',
            emailHash: '{{ hash('sha256', auth()->user()->email) }}',
            orderId: '{{ $subscription->order_id }}',
            actionTrackerId: '429409',
            currency: 'USD',
            amount: '{{ $subscription->amount }}',
            discount: 0,
            coupon: 0,
            cjeventOrder: '{{ \request()->cookie('cje') }}',
            items: [
                {
                    'unitPrice': '{{ $subscription->amount }}',
                    'itemId': '{{ \Illuminate\Support\Str::slug("{$subscription->plan->title_en} {$subscription->plan->id}") }}',
                    'quantity': '1',
                    'discount': '0'
                },
            ]
        };
    </script>
    <script type='text/javascript'>
        (function (a, b, c, d) {
            a = '{{ url('proxydirectory/tags/12363/tag.js') }}';
            b = document;
            c = 'script';
            d = b.createElement(c);
            d.src = a;
            d.type = 'text/java' + c;
            d.async = true;
            d.id = 'cjapitag';
            a = b.getElementsByTagName(c)[0];
            a.parentNode.insertBefore(d, a)
        })();
    </script>
@endsection
@endif
@section('content')
    <div class="container">
        <div class="row mt-70 mb-70">
            <!-- Col MD -->
            <div class="col-md-8 offset-md-2">
                <div class="icon-finished-looin text-center p-150">
                    @if($status)
                        <img src="{{ asset('img/ok.png') }}" class="sign-in mb-4 success-img">
                        <h3 class="mb-3">
                            {{trans('views.Payment Successful') }}
                        </h3>
                        <p>{{trans('views.Thank you, Your payment has been successfully processed') }} </p>
{{--                        <p>{{__('Activation of the subscription may take 3 minutes') }} </p>--}}
                        @if (request('redirect'))
                            <a href="{{ request('redirect') }}"
                               class="btn btn-primary btn-lg btn-block mt-4">
                                {{ trans('auth.Go-subscription') }}</a>
                        @endif
                        @if (session()->has('redirect_after_purchase'))
                            <button href="{{ session()->get('redirect_after_purchase') }}" id="redirect_button"
                                    class="btn btn-primary btn-lg btn-block mt-4 btn-download-noactive">
                                <i class="fal fa-circle-notch fa-spin mr-1"></i>
                                {{ __('Back to download the file') }}
                            </button>
                        @endif
                    @else
                        <img src="{{ asset('img/cancel.png') }}" class="sign-in mb-4 faild-img">
                        <h3 class="mb-3">
                            {{trans('views.Your Payment Failed') }}
                        </h3>
                        <p>{{trans('views.Your current payment has failed') }}</p>
                        @if (request('redirect'))
                            <a href="{{request('redirect')}}"
                               class="btn btn-primary btn-lg btn-block mt-4">
                                {{trans('misc.prices and packages')}}</a>
                        @endif
                    @endif
                </div>
            </div>
            <!-- /COL MD -->
        </div>
        <!-- row -->
        <!-- container wrap-ui -->
    </div>
@endsection
@push('javascript_navbar')
    @if (session()->has('redirect_after_purchase'))
        @php(session()->pull('redirect_after_purchase'))
        <script>
            setTimeout(function () {
                $('#redirect_button').attr('disabled', !1).removeClass('btn-download-noactive').find('i').remove();
                $('#redirect_button').on('click', function () {
                    window.location.href = $('#redirect_button').attr('href');
                })
            }, 3000)
        </script>
    @endif
@endpush
