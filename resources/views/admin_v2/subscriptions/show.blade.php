<html lang="{{app()->getLocale()}}" direction="rtl" dir="rtl" style="direction: rtl">
<!-- begin::Head -->
<head>
    <base href="/">
    <meta charset="utf-8"/>
    <title>Dashboard</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
    <link href="https://fonts.googleapis.com/css?family=Cairo&display=swap&subset=arabic" rel="stylesheet">
    <link href="{{ asset('admin_assets/plugins/global/plugins.bundle.rtl.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('admin_assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        html, body{
            font-family: 'Cairo', Poppins, Helvetica, sans-serif;
        }
        /* fix bug for kt-datatable__pager-size for rtl */
        .bootstrap-select.bs-container{
            left: 0;
            right: unset;
        }
        /* fix bug for kt-datatable__pager-size for rtl */
        select.form-control{
            padding-bottom: 4px;
        }
        ul.notification-list{
            overflow-y: auto !important;
            height: 300px !important;
        }
    </style>
    <link rel="shortcut icon" href="admin_assets/media/logos/favicon.png"/>
</head>
<body>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            تفاصيل الاشتراك
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body">
                    <div class="kt-section">
                        <div class="kt-section__content">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <td>#</td>
                                    <td>
                                        {{ $subscription->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>المستخدم</td>
                                    <td>
                                        {{ $subscription->user->name }} ({{ $subscription->user->email }})
                                    </td>
                                </tr>
                                <tr>
                                    <td>الباقة</td>
                                    <td>{{ $subscription->title }}</td>
                                </tr>
                                <tr>
                                    <td>سعر الباقة</td>
                                    <td>{{ intval($subscription->plan_price) }}$</td>
                                </tr>
                                @if($subscription->promocode)
                                    <tr>
                                        <td>خصم ({{ $subscription->promocode->title }})</td>
                                        <td>{{ $subscription->plan->price-$subscription->amount }}$-</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>المبلغ المدفوع</td>
                                    <td>{{ number_format($subscription->amount) }}$</td>
                                </tr>
                                <tr>
                                    <td>سعر النقطة</td>
                                    <td>{{ $subscription->credit_price }}$</td>
                                </tr>
                                <tr>
                                    <td>بدأ في</td>
                                    <td>{{ $subscription->starts_at?\Carbon\Carbon::parse($subscription->starts_at)->diffForHumans()." ({$subscription->starts_at})":'-' }}</td>
                                </tr>
                                <tr>
                                    <td>ينتهي في</td>
                                    <td>
                                        @if($subscription->plan_type=='annual')
                                            {{ \Carbon\Carbon::parse($subscription->starts_at)->addYear()->format('Y-m-d H:i:s') }}
                                        @else
                                            {{ $subscription->ends_at?\Carbon\Carbon::parse($subscription->ends_at)->diffForHumans()." ({$subscription->ends_at})":'-' }}
                                        @endif
                                    </td>
                                </tr>
                                @php($status=[
                                       0=>['title'=>__('global.status.pending'),'class'=>' text-danger',],
                                       1=>['title'=>__('global.status.active'),'class'=>' text-success',],
                                       2=>['title'=>__('global.status.notactive'),'class'=>' text-danger',],
                                       3=>['title'=>__('Refunded'),'class'=>' text-danger',],
                                       'finished'=>['title'=>__('global.status.finished'),'class'=>' text-warning',],
                                       ])
                                <tr>
                                    <td>الحالة</td>
                                    <td>
                                        @if($subscription->remaining_credits==0 && \Carbon\Carbon::parse($subscription->ends_at)->gt(now()))
                                            <span class="text-danger">انتهى</span>
                                        @else
                                            <span class="{{ $status[$subscription->status]['class'] }}">{{ $status[$subscription->status]['title'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($subscription->plan_type!='package')
                                    <tr>
                                        <td>التجديد التلقائي</td>
                                        <td>{!! $subscription->renewal?'<span class="text-success">فعال</span>':'<span class="text-danger">متوقف</span>' !!}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>النقاط الكلية</td>
                                    <td>{{ $subscription->plan->credits_count }}</td>
                                </tr>
                                <tr>
                                    <td>النقاط المتبقية</td>
                                    <td>{{ $subscription->remaining_credits }}</td>
                                </tr>
                                <tr>
                                    <td>بوابة الدفع</td>
                                    <td>{{ $subscription->payment_method->title_ar }}</td>
                                </tr>
                                <tr>
                                    <td>الفاتورة</td>
                                    <td>@if($subscription->invoice_file)<a
                                            target="_blank"
                                            href="{{ url($subscription->invoice_file) }}">عرض</a>
                                        @else - @endif</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            @if(!$subscription->renewals->count())
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                استخدامات الاشتراك(التحميلات)
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__content">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><small>رقم التحميل</small></th>
                                        <th class="text-center"><small>المحتوى</small></th>
                                        <th class="text-center"><small>المستخدم</small></th>
                                        <th class="text-center"><small>الرخصة</small></th>
                                        <th class="text-center"><small>النقاط المستخدمة</small></th>
                                        <th class="text-center"><small>وقت التحميل</small></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($subscription->downloads as $download)
                                        <tr>
                                            <td>{{ $download->id }}</td>
                                            <td class="text-center"><a href="{{ $download->entity->postLink }}"
                                                                       target="_blank">{{ $download->entity->title }}</a>
                                            </td>
                                            <td class="text-center">{{ $download->user->name }}</td>
                                            <td class="text-center">{{ $download->license_type }}</td>
                                            <td class="text-center">{{ $download->pivot->credits }}</td>
                                            <td class="text-center">{{ $download->pivot->created_at }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" class="text-center">مجموع النقاط المستخدمة</td>
                                        <td class="text-center">{{ $subscription->downloads->sum('pivot.credits') }}</td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="kt-portlet kt-portlet--mobile">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                تجديدات الاشتراك
                            </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-section">
                            <div class="kt-section__content">
                                <div class="accordion" id="accordionExample1">
                                    @foreach($subscription->renewals as $renwal)
                                        <div class="card">
                                            <div class="card-header" id="headingOne">
                                                <div class="card-title" data-toggle="collapse"
                                                     data-target="#collapse-{{ $renwal->id }}" aria-expanded="true"
                                                     aria-controls="collapse-{{ $renwal->id }}">
                                                    {{ $renwal->created_at->subMonth()->format('Y-m-d').' - '.$renwal->created_at->format('Y-m-d') }}
                                                </div>
                                            </div>
                                            <div id="collapse-{{ $renwal->id }}"
                                                 class="collapse {{ $loop->index==0?'show':'' }}"
                                                 aria-labelledby="headingOne" data-parent="#accordionExample1">
                                                <div class="card-body">
                                                    <table class="table table-striped">
                                                        <thead>
                                                        <tr>
                                                            <th>وقت التجديد</th>
                                                            <th class="text-center">النقاط المستخدمة</th>
                                                            <th class="text-center">النقاط المنتهية</th>
                                                            <th class="text-center">التحميلات</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>{{ $renwal->created_at }}</td>
                                                            <td class="text-center">{{ $subscription->plan->credits_count-$renwal->expired_credits }}</td>
                                                            <td class="text-center">{{ $renwal->expired_credits }}</td>
                                                            <td class="text-center">{{ $renwal->downloads_count }}</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    @if($renwal->downloads_count)
                                                        <table class="table table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th><small>رقم التحميل</small></th>
                                                                <th class="text-center"><small>المحتوى</small></th>
                                                                <th class="text-center"><small>المستخدم</small></th>
                                                                <th class="text-center"><small>الرخصة</small></th>
                                                                <th class="text-center"><small>النقاط المستخدمة</small>
                                                                </th>
                                                                <th class="text-center"><small>وقت التحميل</small></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($subscription->downloads()->where('download_subscription.created_at','>=',$renwal->created_at->subMonth())->where('download_subscription.created_at','<=',$renwal->created_at)->get() as $download)
                                                                <tr>
                                                                    <td>{{ $download->id }}</td>
                                                                    <td class="text-center"><a
                                                                            href="{{ $download->entity->postLink }}"
                                                                            target="_blank">{{ $download->entity->title }}</a>
                                                                    </td>
                                                                    <td class="text-center">{{ $download->user->name }}</td>
                                                                    <td class="text-center">{{ $download->license_type }}</td>
                                                                    <td class="text-center">{{ $download->pivot->credits }}</td>
                                                                    <td class="text-center">{{ $download->pivot->created_at }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="{{ asset('js/bootstrap_4.4.1_js_bootstrap.min.js') }}"></script>
</body>
</html>
