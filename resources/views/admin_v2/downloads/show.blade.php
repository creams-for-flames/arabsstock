@extends('admin_v2.layout.app')

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                تفاصيل التحميل
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__content">
                                <table class="table table-striped">
                                    <tbody>
                                    <tr>
                                        <td class="col-3">المحتوى</td>
                                        <td class="col-9"><a href="{{ $download->entity->postLink }}"
                                                             target="_blank">{{ $download->entity->title }}</a></td>
                                    </tr>
                                    <tr>
                                        <td>المستخدم</td>
                                        <td>{{ $download->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>IP</td>
                                        <td>{{ $download->ip }}</td>
                                    </tr>
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>{{ $download->date }}</td>
                                    </tr>
                                    <tr>
                                        <td>النقاط</td>
                                        <td>{{ $download->credits }}</td>
                                    </tr>
                                    <tr>
                                        <td>النقاط الاضافية</td>
                                        <td>@if($download->additional_credits)
                                                {{ $download->additional_credits }} <span class="text-warning">({{ __("plans.additional_credits_reasons.{$download->additional_credits_reason}") }})</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>الرخصة</td>
                                        <td>{{ __(ucfirst($download->license_type)." license") }}</td>
                                    </tr>
                                    <tr>
                                        <td>سعر المحتوى</td>
                                        <td>{{ number_format($download->unit_price,2) }}$</td>
                                    </tr>
                                    <tr>
                                        <td>نسبة المساهم</td>
                                        <td>{{ $download->purchase? "{$download->purchase->profit_ratio} %":'-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>القيمة المخصصة للمساهم</td>
                                        <td>{{ $download->purchase? "{$download->purchase->profit_value}$":'-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>ربح عربستوك</td>
                                        <td>{{ number_format($download->purchase?($download->unit_price-$download->purchase->profit_value):$download->unit_price,2) }}$</td>
                                    </tr>
                                    @if($download->user)
                                        <tr>
                                            <td>{{ $download->entity->contributor_file?'المساهم':'المشرف' }}</td>
                                            @if($download->entity->contributor_file)
                                                <td>{{ $download->entity->user->name }}</td>
                                            @else
                                                <td>{{ optional(\App\Models\User::find($download->entity->user_id))->name?:'-' }}</td>
                                            @endif
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                الاشتراكات المستخدمة
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__content">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th scope="col">رقم الاشتراك</th>
                                        <th scope="col" class="text-center">الباقة</th>
                                        <th scope="col" class="text-center">النقاط المستخدمة</th>
                                        <th scope="col" class="text-center">السعر</th>
                                        <th scope="col" class="text-center">وقت الخصم</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($download->subscriptions()->has('plan')->get() as $r)
                                        <tr>
                                            <td><a href="{{ route('admin.subscriptions.show',$r->id) }}" data-fancybox
                                                   data-type="iframe" data-preload="false">{{ $r->id }}</a></td>
                                            <td class="text-center">{{ $r->title }}</td>
                                            <td class="text-center">{{ $r->pivot->credits }}</td>
                                            <td class="text-center">{{ $r->pivot->credits*$r->credit_price }}$</td>
                                            <td class="text-center">{{ $r->pivot->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
@endpush
@push('scripts')
    <script>
        $(document).ready(function (){
            $('[data-fancybox]').attr('data-height', document.body.scrollHeight);
        });
    </script>
    <script src="{{ asset('js/fancyapps_ui@4.0_dist_fancybox.umd.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fancyapps_ui@4.0_dist_fancybox.css') }}" />
@endpush
