@php use App\Models\Subscription; @endphp
@extends('admin_v2.layout.app')

@section('toolbar')
    <span>آخر تحديث:  <strong>{{ \Illuminate\Support\Carbon::parse($last_update)->format('H:i d-m-Y') }}</strong></span>
    <a href="{{ route('admin.statistics.update') }}" class="btn btn-success">حدث الآن</a>
@endsection
@section('content')

    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::Dashboard 1-->
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
                    <div class="row">
                        <div class="col-12">
                            <!--begin:: Widgets/Activity-->
                            <div
                                class="kt-portlet kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay kt-portlet--skin-solid kt-portlet--height-fluid">
                                <div class="kt-portlet__head kt-portlet__head--noborder kt-portlet__space-x">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            الإحصائيات الشهرية لاضافة الصور
                                        </h3>
                                    </div>
                                </div>
                                <div class="kt-portlet__body kt-portlet__body--fit">
                                    <div class="kt-widget17">
                                        <div
                                            class="kt-widget17__visual kt-widget17__visual--chart kt-portlet-fit--top kt-portlet-fit--sides"
                                            style="background-color: #fd397a">
                                            <div class="kt-widget17__chart" style="height:320px;">
                                                <canvas id="kt_chart_activities"></canvas>
                                            </div>
                                        </div>
                                        <div class="kt-widget17__stats">
                                            <div class="kt-widget17__items">
                                                <div class="kt-widget17__item">
                                          <span class="kt-widget17__icon">
                                              <svg xmlns="http://www.w3.org/2000/svg"
                                                   xmlns:xlink="http://www.w3.org/1999/xlink"
                                                   width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                                   class="kt-svg-icon kt-svg-icon--brand">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <rect x="0" y="0" width="24" height="24"/>
                                                      <path
                                                          d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z"
                                                          fill="#000000"/>
                                                      <rect fill="#000000" opacity="0.3"
                                                            transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) "
                                                            x="16.3255682" y="2.94551858" width="3" height="18" rx="1"/>
                                                  </g>
                                              </svg>
                                          </span>
                                                    <span class="kt-widget17__subtitle">
                                              <a href="{{route('admin.images.index')}}">

                                                  <span class="kt-widget17__subtitle">
                                                      صور نشطة
                                                  </span>
                                              </a>

                                              <span class="kt-widget17__desc">
                                                  {{$imgesActive}}

                                              </span>


                                          </span>
                                                </div>
                                                <div class="kt-widget17__item">
                                          <span class="kt-widget17__icon">
                                              <svg xmlns="http://www.w3.org/2000/svg"
                                                   xmlns:xlink="http://www.w3.org/1999/xlink"
                                                   width="24px" height="24px" viewBox="0 0 24 24" version="1.1"
                                                   class="kt-svg-icon kt-svg-icon--success">
                                                  <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <polygon points="0 0 24 0 24 24 0 24"/>
                                                      <path
                                                          d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z"
                                                          fill="#000000" fill-rule="nonzero"/>
                                                      <path
                                                          d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z"
                                                          fill="#000000" opacity="0.3"/>
                                                  </g>
                                              </svg>
                                          </span>
                                                    <a href="{{route('admin.images.pending.index')}}">
                                              <span class="kt-widget17__subtitle">
                                                  صور غير نشطة
                                              </span>
                                                    </a>
                                                    <span class="kt-widget17__desc">
                                              {{$imgesnotActive}}
                                          </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Widgets/Activity-->
                        </div>
                        <div class="col-12">
                            <!--begin:: Widgets/Revenue Change-->
                            <div class="kt-portlet kt-portlet--height-fluid">
                                <div class="kt-widget14">
                                    <div class="kt-widget14__header">
                                        <h3 class="kt-widget14__title">
                                            الصور في التفريغ
                                        </h3>
                                        <span class="kt-widget14__desc">
                              الصور المفرغة
                            </span>
                                    </div>
                                    <div class="kt-widget14__content">
                                        <div class="kt-widget14__chart">
                                            <div id="kt_chart_removebg" style="height: 150px; width: 150px;"></div>
                                        </div>
                                        <div class="kt-widget14__legends">
                                            @foreach($removebg as $item)
                                                <div class="kt-widget14__legend">
                                                    <span class="kt-widget14__bullet kt-bg-fill-info"></span>
                                                    <span
                                                        class="kt-widget14__stats">{{ number_format((float)$item->data)}} {{__("views.images")}} {{__("views.$item->name")}}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Widgets/Revenue Change-->
                        </div>
                        <div class="col-12">
                            <div class="kt-portlet kt-portlet--height-fluid">
                                <div class="kt-widget14">
                                    <div class="kt-widget14__header">
                                        <h3 class="kt-widget14__title">
                                            تفاصيل الاشتراك المدفوع لتفريغ الصور
                                        </h3>
                                        <span class="kt-widget14__desc">
                     تفاصيل الاشتراك المدفوع  لتفريغ الصور
                    </span>
                                    </div>
                                    <div class="kt-widget14__legend">
                                        <span class="kt-widget14__bullet kt-bg-fill-info"></span>
                                        <span
                                            class="kt-widget14__stats">{{ $api_removebg && isset($api_removebg->data->attributes->credits)?number_format((float)$api_removebg->data->attributes->credits->total):0}} {{__("views.images")}} {{__("views.paid")}}</span>
                                        <br>
                                        <span
                                            class="kt-widget14__stats">{{ $api_removebg?number_format((float)$api_removebg->data->attributes->api->free_calls):0}} {{__("views.images")}} {{__("views.free")}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
                    <!--begin:: Widgets/Inbound Bandwidth-->
                    <div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
                        <div class="kt-portlet__head kt-portlet__space-x">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    إجمالي المبيعات
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body kt-portlet__body--fluid">
                            <div class="kt-widget20">
                                <div class="kt-widget20__content kt-portlet__space-x">
                                    <span class="kt-widget20__number kt-font-brand">{{$purchases_count}}+</span>
                                    <span class="kt-widget20__desc">مبيعات ناجحة</span>
                                </div>
                                <div class="kt-widget20__chart" style="height:130px;">
                                    <canvas id="kt_chart_bandwidth1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Inbound Bandwidth-->
                    <div class="kt-space-20"></div>
                    <!--begin:: Widgets/Outbound Bandwidth-->
                    <div class="kt-portlet kt-portlet--fit kt-portlet--head-noborder kt-portlet--height-fluid-half">
                        <div class="kt-portlet__head kt-portlet__space-x">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    الأعضاء المسجلين
                                </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__body kt-portlet__body--fluid">
                            <div class="kt-widget20">
                                <div class="kt-widget20__content kt-portlet__space-x">
                                    <span class="kt-widget20__number kt-font-danger">{{$userDashCount}}+</span>
                                    <span class="kt-widget20__desc">اعضاء مسجلين</span>
                                </div>
                                <div class="kt-widget20__chart" style="height:130px;">
                                    <canvas id="kt_chart_bandwidth2"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Outbound Bandwidth-->
                </div>
                <div class="col-lg-6 col-xl-4 order-lg-1 order-xl-1">
                    <!--begin:: Widgets/Latest Updates-->
                    <div class="kt-portlet kt-portlet--height-fluid- ">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title">
                                    تحديثات
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                            </div>
                        </div>
                        <!--full height portlet body-->
                        <div class="kt-portlet__body kt-portlet__body--fluid kt-portlet__body--fit">
                            <div class="kt-widget4 kt-widget4--sticky">
                                <div class="kt-widget4__items kt-portlet__space-x kt-margin-t-15">
                                    <div class="kt-widget4__item">
														<span class="kt-widget4__icon">
															<i class="fa fa-dollar-sign  kt-font-brand"></i>
														</span>
                                        <span class="kt-widget4__title">
                                            مدفوعات Stripe
                                        </span>
                                        <span
                                            class="kt-widget4__number kt-font-brand">{{ statistic('stripe_payments') }}</span>
                                    </div>
                                    <div class="kt-widget4__item">
														<span class="kt-widget4__icon">
															<i class="fa fa-dollar-sign  kt-font-success"></i>
														</span>
                                        <span class="kt-widget4__title">
                                            مدفوعات Stripe على عربستوك
                                        </span>
                                        <span
                                            class="kt-widget4__number kt-font-success">{{ statistic('local_stripe_payments') }}</span>
                                    </div>
                                    <div class="kt-widget4__item">
														<span class="kt-widget4__icon">
															<i class="fa fa-dollar-sign  kt-font-danger"></i>
														</span>
                                        <span class="kt-widget4__title">
                                            مدفوعات Paypal
                                        </span>
                                        <span
                                            class="kt-widget4__number kt-font-danger">{{ statistic('paypal_payments') }}</span>
                                    </div>
                                    <div class="kt-widget4__item">
														<span class="kt-widget4__icon">
															<i class="fa fa-dollar-sign kt-font-warning"></i>
														</span>
                                        <span class="kt-widget4__title">
                                            مدفوعات Paypal على عربستوك
                                        </span>
                                        <span
                                            class="kt-widget4__number kt-font-warning">{{ statistic('local_paypal_payments') }}</span>
                                    </div>
                                </div>
                                <div class="kt-widget4__chart kt-margin-t-15">
                                    <canvas id="kt_chart_latest_updates" style="height: 150px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-8 col-lg-12 order-lg-3 order-xl-1">
                    <!--begin:: Widgets/Best Sellers-->
                    <div class="kt-portlet kt-portlet--height-fluid">
                        <div class="kt-portlet__head">
                            <form class="form-horizontal row" method="get" action="{{route('admin.dashboard.index')}}">
                                <div class="kt-portlet__body col-md-4">
                                    <div class="form-group row">
                                        <label class="pr-0 pl-0 col-form-label col-lg-3 col-sm-12">التاريخ من</label>
                                        <div class="col-sm-9">
                                            <input @if(isset($_GET['from_date'])) value="{{$_GET['from_date']}}"
                                                   @endif name="from_date" type="text" class="form-control"
                                                   id="kt_datepicker_1" readonly placeholder="اختر تاريخ"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__body col-md-4">
                                    <div class="form-group row">
                                        <label class="pr-0 pl-0 col-form-label col-lg-3 col-sm-12">التاريخ الي</label>
                                        <div class="col-sm-9">
                                            <input @if(isset($_GET['to_date'])) value="{{$_GET['to_date']}}"
                                                   @endif name="to_date" type="text" class="form-control"
                                                   id="kt_datepicker_1" readonly placeholder="اختر تاريخ"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__body col-md-4">
                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3 col-sm-3">التصنيف</label>
                                        <div class="col-sm-9">
                                            <select class="form-control kt-select2" id="kt_select2_1"
                                                    name="category_id">
                                                <option value="">اختر تجميعة</option>
                                                @foreach($categories as $r)
                                                    <option
                                                        value="{{$r->id}}" {{ request('category_id')==$r->id?'selected':'' }}>{{$r->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-xl-9" style="margin-bottom: 10px;">
                                    <div class="dropdown dropdown-inline">
                                        <button type="submit" class="btn btn-success btn-icon-sm">
                                            فلترة
                                            <i class="flaticon2-search" style="font-size: 1rem;"></i>
                                        </button>
                                    </div>
                                    <div class="dropdown dropdown-inline">
                                        <a href="{{route('admin.dashboard.index')}}"
                                           class="btn btn-secondary btn-icon-sm">
                                            Back
                                            <i class="la la-long-arrow-left" style="font-size: 1rem;"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="kt-portlet__body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="kt_widget5_tab1_content" aria-expanded="true">
                                    <div class="kt-widget5">
                                        @foreach($top_downloading as $r)
                                            <div class="kt-widget5__item">
                                                <div class="kt-widget5__content">
                                                    <div class="kt-widget5__pic">
                                                        <img class="kt-widget7__img"
                                                             src="{{ url($r->thumbnail) }}" alt="">
                                                    </div>
                                                    <div class="kt-widget5__section">
                                                        <a href="{{ $r->post_link }}" target="_blank"
                                                           class="kt-widget5__title">
                                                            {{$r->title_ar}}
                                                        </a>
                                                        <div class="kt-widget5__info">
                                                            <span>تاريخ النشر:</span>
                                                            <span class="kt-font-info">{{$r->date}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="kt-widget5__content">
                                                    <div class="kt-widget5__stats">
                                                        <span class="kt-widget5__number">{{$r->downloads_count}}</span>
                                                        <span class="kt-widget5__sales">تحميل</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Best Sellers-->
                </div>
                <div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
                    <!--begin:: Widgets/Profit Share-->
                    <div class="kt-portlet kt-portlet--height-fluid">
                        <div class="kt-widget14">
                            <div class="kt-widget14__header">
                                <h3 class="kt-widget14__title">
                                    اكثر الصور مبيعا
                                </h3>
                                <span class="kt-widget14__desc">
                                توضيح الصور الاكثر مبيعا بالنسبة لتصنيف
                            </span>
                            </div>
                            <div class="kt-widget14__content">
                                <div class="kt-widget14__chart">
                                    <div class="kt-widget14__stat">{{$totalImagesPaying}}</div>
                                    <canvas id="kt_chart_profit_share" style="height: 140px; width: 140px;"></canvas>
                                </div>
                                <div class="kt-widget14__legends">
                                    @foreach($imagesCategoryPaying as $imagesCategoryPayingItem)
                                        @if($imagesCategoryPayingItem->images_count !=0)
                                            <div class="kt-widget14__legend">
                                                <span class="kt-widget14__bullet kt-bg-fill-info"></span>
                                                <span class="kt-widget14__stats">+{{ number_format((float)$imagesCategoryPayingItem->images_count/$totalImagesPaying*100, 2, '.', '') }}% {{$imagesCategoryPayingItem->name}}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Profit Share-->
                </div>
                <div class="col-xl-6 col-lg-6 order-lg-2 order-xl-1">
                    <!--begin:: Widgets/Revenue Change-->
                    <div class="kt-portlet kt-portlet--height-fluid">
                        <div class="kt-widget14">
                            <div class="kt-widget14__header">
                                <h3 class="kt-widget14__title">
                                    الصور في التصنيف
                                </h3>
                                <span class="kt-widget14__desc">
                                اكثر الصور عددا بالتصنيف
                            </span>
                            </div>
                            <div class="kt-widget14__content">
                                <div class="kt-widget14__chart">
                                    <div id="kt_chart_revenue_change" style="height: 150px; width: 150px;"></div>
                                </div>
                                <div class="kt-widget14__legends">
                                    @foreach($imagesCategory as $imagesCategoryItem)
                                        @if($imagesCategoryItem->images_count !=0)
                                            <div class="kt-widget14__legend">
                                                <span class="kt-widget14__bullet kt-bg-fill-info"></span>
                                                <span class="kt-widget14__stats">+{{ number_format((float)$imagesCategoryItem->images_count/$totalImages*100, 2, '.', '') }}% {{$imagesCategoryItem->name}}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end:: Widgets/Revenue Change-->
                </div>
            </div>
        </div><!-- /.content-wrapper -->
        <!--End::Dashboard 1-->
    </div>
    <!-- end:: Content -->

@endsection

@push('css')
    <link href="{{ asset('plugins/morris/morris.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css')}}" rel="stylesheet" type="text/css"/>
@endpush


@push('scripts')

    <!-- Morris -->
    <script src="{{ asset('plugins/morris/raphael-min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/morris/morris.min.js')}}" type="text/javascript"></script>

    <!-- knob -->
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}" type="text/javascript"></script>
    <script src="{{ asset('plugins/knob/jquery.knob.js')}}" type="text/javascript"></script>


    <script>
        var KTDashboard = function () {

            var activitiesChart = function () {

                if ($('#kt_chart_activities').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_activities").getContext("2d");

                var gradient = ctx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, Chart.helpers.color('#e14c86').alpha(1).rgbString());
                gradient.addColorStop(1, Chart.helpers.color('#e14c86').alpha(0.3).rgbString());

                var config = {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach($imges as $imgesItem)




                                @if($loop->last)
                                "{{$imgesItem->name}}"
                            @else
                                "{{$imgesItem->name}}",
                            @endif

                            @endforeach



                        ],
                        datasets: [{
                            label: "إضافة صور للمخزن",
                            backgroundColor: Chart.helpers.color('#e14c86').alpha(1).rgbString(),  //gradient
                            borderColor: '#e13a58',

                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('light'),
                            pointHoverBorderColor: Chart.helpers.color('#ffffff').alpha(0.1).rgbString(),

                            //fill: 'start',
                            data: [
                                @foreach($imges as $imgesItem)
                                    {{$imgesItem->images_count}},
                                @endforeach
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.0000001,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };


            var bandwidthChart1 = function () {
                if ($('#kt_chart_bandwidth1').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_bandwidth1").getContext("2d");

                var gradient = ctx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, Chart.helpers.color('#d1f1ec').alpha(1).rgbString());
                gradient.addColorStop(1, Chart.helpers.color('#d1f1ec').alpha(0.3).rgbString());

                var config = {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach($purchases as $r)




                                @if($loop->last)
                                "{{"{$r->year}-{$r->month}"}}"
                            @else
                                "{{"{$r->year}-{$r->month}"}}",
                            @endif

                            @endforeach



                        ],
                        datasets: [{
                            label: "عملية",
                            backgroundColor: gradient,
                            borderColor: KTApp.getStateColor('success'),

                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                            //fill: 'start',
                            data: [
                                @foreach($purchases as $r)
                                    @if($loop->last)
                                    {{$r->count}}
                                    @else
                                    {{$r->count}},
                                @endif
                                @endforeach
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.0000001,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };


            var bandwidthChart2 = function () {
                if ($('#kt_chart_bandwidth2').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_bandwidth2").getContext("2d");

                var gradient = ctx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, Chart.helpers.color('#ffefce').alpha(1).rgbString());
                gradient.addColorStop(1, Chart.helpers.color('#ffefce').alpha(0.3).rgbString());

                var config = {
                    type: 'line',
                    data: {
                        labels: [
                            @foreach($usersDash as $userItem)

                                @if($loop->last)
                                "{{$userItem->new_date}}"
                            @else
                                "{{$userItem->new_date}}",
                            @endif

                            @endforeach



                        ],
                        datasets: [{
                            label: "عضو",
                            backgroundColor: gradient,
                            borderColor: KTApp.getStateColor('warning'),
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            //fill: 'start',
                            data: [
                                @foreach($usersDash as $userItem)
                                    {{$userItem->count}},
                                @endforeach
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.0000001,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };


            var revenueChange = function () {
                if ($('#kt_chart_revenue_change').length == 0) {
                    return;
                }

                Morris.Donut({
                    element: 'kt_chart_revenue_change',
                    data: [


                            @foreach($imagesCategory as $imagesCategoryItem)
                            @if($imagesCategoryItem->images_count !=0)
                        {
                            label: '{{$imagesCategoryItem->name}}',
                            value: '{{$imagesCategoryItem->images_count}}',
                        },
                        @endif
                        @endforeach


                    ],
                    colors: [
                        KTApp.getStateColor('success'),
                        KTApp.getStateColor('danger'),
                        KTApp.getStateColor('brand'),
                        KTApp.getStateColor('warning'),
                    ],
                });
            };

            var chartRemoveBg = function () {
                if ($('#kt_chart_removebg').length == 0) {
                    return;
                }

                Morris.Donut({
                    element: 'kt_chart_removebg',
                    data: [


                            @foreach($removebg as $item)
                        {
                            label: '{{__("views.$item->name")}}',
                            value: '{{$item->data}}',
                        },
                        @endforeach


                    ],
                    colors: [
                        KTApp.getStateColor('success'),
                        KTApp.getStateColor('danger'),
                        KTApp.getStateColor('brand'),
                        KTApp.getStateColor('warning'),
                        KTApp.getStateColor('dark'),
                        KTApp.getStateColor('info'),
                        '#ffeb3b',

                    ],
                });
            };
            var profitShare = function () {
                if (!KTUtil.getByID('kt_chart_profit_share')) {
                    return;
                }

                var randomScalingFactor = function () {
                    return Math.round(Math.random() * 100);
                };

                var config = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [
                                @foreach($imagesCategoryPaying as $imagesCategoryPayingItem)
                                    @if($imagesCategoryPayingItem->images_count !=0)
                                    {{$imagesCategoryPayingItem->images_count}},
                                @endif
                                @endforeach
                            ],
                            backgroundColor: [
                                KTApp.getStateColor('success'),
                                KTApp.getStateColor('danger'),
                                KTApp.getStateColor('brand'),
                            ],
                        }],
                        labels: [
                            @foreach($imagesCategoryPaying as $imagesCategoryPayingItem)

                                @if($imagesCategoryPayingItem->images_count !=0)
                                '{{$imagesCategoryPayingItem->name}}',
                            @endif
                            @endforeach
                        ],
                    },
                    options: {
                        cutoutPercentage: 75,
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false,
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Technology',
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true,
                        },
                        tooltips: {
                            enabled: true,
                            intersect: false,
                            mode: 'nearest',
                            bodySpacing: 5,
                            yPadding: 10,
                            xPadding: 10,
                            caretPadding: 0,
                            displayColors: false,
                            backgroundColor: KTApp.getStateColor('brand'),
                            titleFontColor: '#ffffff',
                            cornerRadius: 4,
                            footerSpacing: 0,
                            titleSpacing: 0,
                        },
                    },
                };

                var ctx = KTUtil.getByID('kt_chart_profit_share').getContext('2d');
                var myDoughnut = new Chart(ctx, config);
            };


            // Sparkline Chart helper function
            var _initSparklineChart = function (src, data, color, border) {
                if (src.length == 0) {
                    return;
                }

                var config = {
                    type: 'line',
                    data: {
                        labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October"],
                        datasets: [{
                            label: "",
                            borderColor: color,
                            borderWidth: border,

                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                            fill: false,
                            data: data,
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            enabled: false,
                            intersect: false,
                            mode: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                            labels: {
                                usePointStyle: false,
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: true,
                        hover: {
                            mode: 'index',
                        },
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },

                        elements: {
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },

                        layout: {
                            padding: {
                                left: 0,
                                right: 10,
                                top: 5,
                                bottom: 0,
                            },
                        },
                    },
                };

                return new Chart(src, config);
            };

            // Daily Sales chart.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var dailySales = function () {
                var chartContainer = KTUtil.getByID('kt_chart_daily_sales');

                if (!chartContainer) {
                    return;
                }

                var chartData = {
                    labels: ["Label 1", "Label 2", "Label 3", "Label 4", "Label 5", "Label 6", "Label 7", "Label 8", "Label 9", "Label 10", "Label 11", "Label 12", "Label 13", "Label 14", "Label 15", "Label 16"],
                    datasets: [{
                        //label: 'Dataset 1',
                        backgroundColor: KTApp.getStateColor('success'),
                        data: [
                            15, 20, 25, 30, 25, 20, 15, 20, 25, 30, 25, 20, 15, 10, 15, 20,
                        ],
                    }, {
                        //label: 'Dataset 2',
                        backgroundColor: '#f3f3fb',
                        data: [
                            15, 20, 25, 30, 25, 20, 15, 20, 25, 30, 25, 20, 15, 10, 15, 20,
                        ],
                    }],
                };

                var chart = new Chart(chartContainer, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            intersect: false,
                            mode: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        barRadius: 4,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                stacked: true,
                            }],
                            yAxes: [{
                                display: false,
                                stacked: true,
                                gridLines: false,
                            }],
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 0,
                                bottom: 0,
                            },
                        },
                    },
                });
            };

            // Profit Share Chart.
            // Based on Chartjs plugin - http://www.chartjs.org/


            // Sales Stats.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var salesStats = function () {
                if (!KTUtil.getByID('kt_chart_sales_stats')) {
                    return;
                }

                var config = {
                    type: 'line',
                    data: {
                        labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December",
                            "January", "February", "March", "April",
                        ],
                        datasets: [{
                            label: "Sales count",
                            borderColor: KTApp.getStateColor('brand'),
                            borderWidth: 2,
                            //pointBackgroundColor: KTApp.getStateColor('brand'),
                            backgroundColor: KTApp.getStateColor('brand'),
                            pointBackgroundColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color(KTApp.getStateColor('danger')).alpha(0.2).rgbString(),
                            data: [
                                10, 20, 16,
                                18, 12, 40,
                                35, 30, 33,
                                34, 45, 40,
                                60, 55, 70,
                                65, 75, 62,
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            intersect: false,
                            mode: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                            labels: {
                                usePointStyle: false,
                            },
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        hover: {
                            mode: 'index',
                        },
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                            }],
                        },

                        elements: {
                            point: {
                                radius: 3,
                                borderWidth: 0,

                                hoverRadius: 8,
                                hoverBorderWidth: 2,
                            },
                        },
                    },
                };

                var chart = new Chart(KTUtil.getByID('kt_chart_sales_stats'), config);
            };

            // Sales By KTUtillication Stats.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var salesByApps = function () {
                // Init chart instances
                _initSparklineChart($('#kt_chart_sales_by_apps_1_1'), [10, 20, -5, 8, -20, -2, -4, 15, 5, 8], KTApp.getStateColor('success'), 2);
                _initSparklineChart($('#kt_chart_sales_by_apps_1_2'), [2, 16, 0, 12, 22, 5, -10, 5, 15, 2], KTApp.getStateColor('danger'), 2);
                _initSparklineChart($('#kt_chart_sales_by_apps_1_3'), [15, 5, -10, 5, 16, 22, 6, -6, -12, 5], KTApp.getStateColor('success'), 2);
                _initSparklineChart($('#kt_chart_sales_by_apps_1_4'), [8, 18, -12, 12, 22, -2, -14, 16, 18, 2], KTApp.getStateColor('warning'), 2);

                _initSparklineChart($('#kt_chart_sales_by_apps_2_1'), [10, 20, -5, 8, -20, -2, -4, 15, 5, 8], KTApp.getStateColor('danger'), 2);
                _initSparklineChart($('#kt_chart_sales_by_apps_2_2'), [2, 16, 0, 12, 22, 5, -10, 5, 15, 2], KTApp.getStateColor('dark'), 2);
                _initSparklineChart($('#kt_chart_sales_by_apps_2_3'), [15, 5, -10, 5, 16, 22, 6, -6, -12, 5], KTApp.getStateColor('brand'), 2);
                _initSparklineChart($('#kt_chart_sales_by_apps_2_4'), [8, 18, -12, 12, 22, -2, -14, 16, 18, 2], KTApp.getStateColor('info'), 2);
            };

            // Trends Stats.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var trendsStats = function () {
                if ($('#kt_chart_trends_stats').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_trends_stats").getContext("2d");

                var gradient = ctx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, Chart.helpers.color('#00c5dc').alpha(0.7).rgbString());
                gradient.addColorStop(1, Chart.helpers.color('#f2feff').alpha(0).rgbString());

                var config = {
                    type: 'line',
                    data: {
                        labels: [
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
                            "January", "February", "March", "April",
                        ],
                        datasets: [{
                            label: "Sales Stats",
                            backgroundColor: gradient, // Put the gradient here as a fill color
                            borderColor: '#0dc8de',

                            pointBackgroundColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.2).rgbString(),

                            //fill: 'start',
                            data: [
                                20, 10, 18, 15, 26, 18, 15, 22, 16, 12,
                                12, 13, 10, 18, 14, 24, 16, 12, 19, 21,
                                16, 14, 21, 21, 13, 15, 22, 24, 21, 11,
                                14, 19, 21, 17,
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            intersect: false,
                            mode: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        hover: {
                            mode: 'index',
                        },
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.19,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 5,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };

            // Trends Stats 2.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var trendsStats2 = function () {
                if ($('#kt_chart_trends_stats_2').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_trends_stats_2").getContext("2d");

                var config = {
                    type: 'line',
                    data: {
                        labels: [
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
                            "January", "February", "March", "April",
                        ],
                        datasets: [{
                            label: "Sales Stats",
                            backgroundColor: '#d2f5f9', // Put the gradient here as a fill color
                            borderColor: KTApp.getStateColor('brand'),

                            pointBackgroundColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#ffffff').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.2).rgbString(),

                            //fill: 'start',
                            data: [
                                20, 10, 18, 15, 32, 18, 15, 22, 8, 6,
                                12, 13, 10, 18, 14, 24, 16, 12, 19, 21,
                                16, 14, 24, 21, 13, 15, 27, 29, 21, 11,
                                14, 19, 21, 17,
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            intersect: false,
                            mode: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        hover: {
                            mode: 'index',
                        },
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.19,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 5,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };

            // Trends Stats.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var latestTrendsMap = function () {
                if ($('#kt_chart_latest_trends_map').length == 0) {
                    return;
                }

                try {
                    var map = new GMaps({
                        div: '#kt_chart_latest_trends_map',
                        lat: -12.043333,
                        lng: -77.028333,
                    });
                } catch (e) {
                    console.log(e);
                }
            };

            // Revenue Change.
            // Based on Morris plugin - http://morrisjs.github.io/morris.js/


            // Support Tickets Chart.
            // Based on Morris plugin - http://morrisjs.github.io/morris.js/
            var supportCases = function () {
                if ($('#kt_chart_support_tickets').length == 0) {
                    return;
                }

                Morris.Donut({
                    element: 'kt_chart_support_tickets',
                    data: [{
                        label: "Margins",
                        value: 20,
                    },
                        {
                            label: "Profit",
                            value: 70,
                        },
                        {
                            label: "Lost",
                            value: 10,
                        },
                    ],
                    labelColor: '#a7a7c2',
                    colors: [
                        KTApp.getStateColor('success'),
                        KTApp.getStateColor('brand'),
                        KTApp.getStateColor('danger'),
                    ],
                    //formatter: function (x) { return x + "%"}
                });
            };

            // Support Tickets Chart.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var supportRequests = function () {
                var container = KTUtil.getByID('kt_chart_support_requests');

                if (!container) {
                    return;
                }

                var randomScalingFactor = function () {
                    return Math.round(Math.random() * 100);
                };

                var config = {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [
                                35, 30, 35,
                            ],
                            backgroundColor: [
                                KTApp.getStateColor('success'),
                                KTApp.getStateColor('danger'),
                                KTApp.getStateColor('brand'),
                            ],
                        }],
                        labels: [
                            'Angular',
                            'CSS',
                            'HTML',
                        ],
                    },
                    options: {
                        cutoutPercentage: 75,
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false,
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Technology',
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true,
                        },
                        tooltips: {
                            enabled: true,
                            intersect: false,
                            mode: 'nearest',
                            bodySpacing: 5,
                            yPadding: 10,
                            xPadding: 10,
                            caretPadding: 0,
                            displayColors: false,
                            backgroundColor: KTApp.getStateColor('brand'),
                            titleFontColor: '#ffffff',
                            cornerRadius: 4,
                            footerSpacing: 0,
                            titleSpacing: 0,
                        },
                    },
                };

                var ctx = container.getContext('2d');
                var myDoughnut = new Chart(ctx, config);
            };

            // Activities Charts.
            // Based on Chartjs plugin - http://www.chartjs.org/


            // Bandwidth Charts 1.
            // Based on Chartjs plugin - http://www.chartjs.org/


            // Bandwidth Charts 2.
            // Based on Chartjs plugin - http://www.chartjs.org/


            // Bandwidth Charts 2.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var adWordsStat = function () {
                if ($('#kt_chart_adwords_stats').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_adwords_stats").getContext("2d");

                var gradient = ctx.createLinearGradient(0, 0, 0, 240);
                gradient.addColorStop(0, Chart.helpers.color('#ffefce').alpha(1).rgbString());
                gradient.addColorStop(1, Chart.helpers.color('#ffefce').alpha(0.3).rgbString());

                var config = {
                    type: 'line',
                    data: {
                        labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October"],
                        datasets: [{
                            label: "AdWord Clicks",
                            backgroundColor: KTApp.getStateColor('brand'),
                            borderColor: KTApp.getStateColor('brand'),

                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                            data: [
                                12, 16, 9, 18, 13, 12, 18, 12, 15, 17,
                            ],
                        }, {
                            label: "AdWords Views",

                            backgroundColor: KTApp.getStateColor('success'),
                            borderColor: KTApp.getStateColor('success'),

                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                            data: [
                                10, 14, 12, 16, 9, 11, 13, 9, 13, 15,
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                stacked: true,
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.0000001,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };

            // Bandwidth Charts 2.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var financeSummary = function () {
                if ($('#kt_chart_finance_summary').length == 0) {
                    return;
                }

                var ctx = document.getElementById("kt_chart_finance_summary").getContext("2d");

                var config = {
                    type: 'line',
                    data: {
                        labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October"],
                        datasets: [{
                            label: "AdWords Views",

                            backgroundColor: KTApp.getStateColor('success'),
                            borderColor: KTApp.getStateColor('success'),

                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('danger'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),
                            data: [
                                10, 14, 12, 16, 9, 11, 13, 9, 13, 15,
                            ],
                        }],
                    },
                    options: {
                        title: {
                            display: false,
                        },
                        tooltips: {
                            mode: 'nearest',
                            intersect: false,
                            position: 'nearest',
                            xPadding: 10,
                            yPadding: 10,
                            caretPadding: 10,
                        },
                        legend: {
                            display: false,
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            xAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Month',
                                },
                            }],
                            yAxes: [{
                                display: false,
                                gridLines: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value',
                                },
                                ticks: {
                                    beginAtZero: true,
                                },
                            }],
                        },
                        elements: {
                            line: {
                                tension: 0.0000001,
                            },
                            point: {
                                radius: 4,
                                borderWidth: 12,
                            },
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 0,
                            },
                        },
                    },
                };

                var chart = new Chart(ctx, config);
            };

            // Order Statistics.
            // Based on Chartjs plugin - http://www.chartjs.org/
            var orderStatistics = function () {
                var container = KTUtil.getByID('kt_chart_order_statistics');

                if (!container) {
                    return;
                }

                var MONTHS = ['1 Jan', '2 Jan', '3 Jan', '4 Jan', '5 Jan', '6 Jan', '7 Jan'];

                var color = Chart.helpers.color;
                var barChartData = {
                    labels: ['1 Jan', '2 Jan', '3 Jan', '4 Jan', '5 Jan', '6 Jan', '7 Jan'],
                    datasets: [
                        {
                            fill: true,
                            //borderWidth: 0,
                            backgroundColor: color(KTApp.getStateColor('brand')).alpha(0.6).rgbString(),
                            borderColor: color(KTApp.getStateColor('brand')).alpha(0).rgbString(),

                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('brand'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            data: [20, 30, 20, 40, 30, 60, 30],
                        },
                        {
                            fill: true,
                            //borderWidth: 0,
                            backgroundColor: color(KTApp.getStateColor('brand')).alpha(0.2).rgbString(),
                            borderColor: color(KTApp.getStateColor('brand')).alpha(0).rgbString(),

                            pointHoverRadius: 4,
                            pointHoverBorderWidth: 12,
                            pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                            pointHoverBackgroundColor: KTApp.getStateColor('brand'),
                            pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                            data: [15, 40, 15, 30, 40, 30, 50],
                        },
                    ],
                };

                var ctx = container.getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: barChartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: false,
                        scales: {
                            xAxes: [{
                                categoryPercentage: 0.35,
                                barPercentage: 0.70,
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: 'Month',
                                },
                                gridLines: false,
                                ticks: {
                                    display: true,
                                    beginAtZero: true,
                                    fontColor: KTApp.getBaseColor('shape', 3),
                                    fontSize: 13,
                                    padding: 10,
                                },
                            }],
                            yAxes: [{
                                categoryPercentage: 0.35,
                                barPercentage: 0.70,
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: 'Value',
                                },
                                gridLines: {
                                    color: KTApp.getBaseColor('shape', 2),
                                    drawBorder: false,
                                    offsetGridLines: false,
                                    drawTicks: false,
                                    borderDash: [3, 4],
                                    zeroLineWidth: 1,
                                    zeroLineColor: KTApp.getBaseColor('shape', 2),
                                    zeroLineBorderDash: [3, 4],
                                },
                                ticks: {
                                    max: 70,
                                    stepSize: 10,
                                    display: true,
                                    beginAtZero: true,
                                    fontColor: KTApp.getBaseColor('shape', 3),
                                    fontSize: 13,
                                    padding: 10,
                                },
                            }],
                        },
                        title: {
                            display: false,
                        },
                        hover: {
                            mode: 'index',
                        },
                        tooltips: {
                            enabled: true,
                            intersect: false,
                            mode: 'nearest',
                            bodySpacing: 5,
                            yPadding: 10,
                            xPadding: 10,
                            caretPadding: 0,
                            displayColors: false,
                            backgroundColor: KTApp.getStateColor('brand'),
                            titleFontColor: '#ffffff',
                            cornerRadius: 4,
                            footerSpacing: 0,
                            titleSpacing: 0,
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 5,
                                bottom: 5,
                            },
                        },
                    },
                });
            };

            // Quick Stat Charts
            var quickStats = function () {
                _initSparklineChart($('#kt_chart_quick_stats_1'), [10, 14, 18, 11, 9, 12, 14, 17, 18, 14], KTApp.getStateColor('brand'), 3);
                _initSparklineChart($('#kt_chart_quick_stats_2'), [11, 12, 18, 13, 11, 12, 15, 13, 19, 15], KTApp.getStateColor('danger'), 3);
                _initSparklineChart($('#kt_chart_quick_stats_3'), [12, 12, 18, 11, 15, 12, 13, 16, 11, 18], KTApp.getStateColor('success'), 3);
                _initSparklineChart($('#kt_chart_quick_stats_4'), [11, 9, 13, 18, 13, 15, 14, 13, 18, 15], KTApp.getStateColor('success'), 3);
            };

            // Calendar Init
            var calendarInit = function () {
                if ($('#kt_calendar').length === 0) {
                    return;
                }

                var todayDate = moment().startOf('day');
                var YM = todayDate.format('YYYY-MM');
                var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
                var TODAY = todayDate.format('YYYY-MM-DD');
                var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

                $('#kt_calendar').fullCalendar({
                    isRTL: KTUtil.isRTL(),
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'month,agendaWeek,agendaDay,listWeek',
                    },
                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    navLinks: true,
                    defaultDate: moment('2017-09-15'),
                    events: [
                        {
                            title: 'Meeting',
                            start: moment('2017-08-28'),
                            description: 'Lorem ipsum dolor sit incid idunt ut',
                            className: "fc-event-light fc-event-solid-warning",
                        },
                        {
                            title: 'Conference',
                            description: 'Lorem ipsum dolor incid idunt ut labore',
                            start: moment('2017-08-29T13:30:00'),
                            end: moment('2017-08-29T17:30:00'),
                            className: "fc-event-success",
                        },
                        {
                            title: 'Dinner',
                            start: moment('2017-08-30'),
                            description: 'Lorem ipsum dolor sit tempor incid',
                            className: "fc-event-light  fc-event-solid-danger",
                        },
                        {
                            title: 'All Day Event',
                            start: moment('2017-09-01'),
                            description: 'Lorem ipsum dolor sit incid idunt ut',
                            className: "fc-event-danger fc-event-solid-focus",
                        },
                        {
                            title: 'Reporting',
                            description: 'Lorem ipsum dolor incid idunt ut labore',
                            start: moment('2017-09-03T13:30:00'),
                            end: moment('2017-09-04T17:30:00'),
                            className: "fc-event-success",
                        },
                        {
                            title: 'Company Trip',
                            start: moment('2017-09-05'),
                            end: moment('2017-09-07'),
                            description: 'Lorem ipsum dolor sit tempor incid',
                            className: "fc-event-primary",
                        },
                        {
                            title: 'ICT Expo 2017 - Product Release',
                            start: moment('2017-09-09'),
                            description: 'Lorem ipsum dolor sit tempor inci',
                            className: "fc-event-light fc-event-solid-primary",
                        },
                        {
                            title: 'Dinner',
                            start: moment('2017-09-12'),
                            description: 'Lorem ipsum dolor sit amet, conse ctetur',
                        },
                        {
                            id: 999,
                            title: 'Repeating Event',
                            start: moment('2017-09-15T16:00:00'),
                            description: 'Lorem ipsum dolor sit ncididunt ut labore',
                            className: "fc-event-danger",
                        },
                        {
                            id: 1000,
                            title: 'Repeating Event',
                            description: 'Lorem ipsum dolor sit amet, labore',
                            start: moment('2017-09-18T19:00:00'),
                        },
                        {
                            title: 'Conference',
                            start: moment('2017-09-20T13:00:00'),
                            end: moment('2017-09-21T19:00:00'),
                            description: 'Lorem ipsum dolor eius mod tempor labore',
                            className: "fc-event-success",
                        },
                        {
                            title: 'Meeting',
                            start: moment('2017-09-11'),
                            description: 'Lorem ipsum dolor eiu idunt ut labore',
                        },
                        {
                            title: 'Lunch',
                            start: moment('2017-09-18'),
                            className: "fc-event-info fc-event-solid-success",
                            description: 'Lorem ipsum dolor sit amet, ut labore',
                        },
                        {
                            title: 'Meeting',
                            start: moment('2017-09-24'),
                            className: "fc-event-warning",
                            description: 'Lorem ipsum conse ctetur adipi scing',
                        },
                        {
                            title: 'Happy Hour',
                            start: moment('2017-09-24'),
                            className: "fc-event-light fc-event-solid-focus",
                            description: 'Lorem ipsum dolor sit amet, conse ctetur',
                        },
                        {
                            title: 'Dinner',
                            start: moment('2017-09-24'),
                            className: "fc-event-solid-focus fc-event-light",
                            description: 'Lorem ipsum dolor sit ctetur adipi scing',
                        },
                        {
                            title: 'Birthday Party',
                            start: moment('2017-09-24'),
                            className: "fc-event-primary",
                            description: 'Lorem ipsum dolor sit amet, scing',
                        },
                        {
                            title: 'Company Event',
                            start: moment('2017-09-24'),
                            className: "fc-event-danger",
                            description: 'Lorem ipsum dolor sit amet, scing',
                        },
                        {
                            title: 'Click for Google',
                            url: 'http://google.com/',
                            start: moment('2017-09-26'),
                            className: "fc-event-solid-info fc-event-light",
                            description: 'Lorem ipsum dolor sit amet, labore',
                        },
                    ],

                    eventRender: function (event, element) {
                        if (element.hasClass('fc-day-grid-event')) {
                            element.data('content', event.description);
                            element.data('placement', 'top');
                            KTApp.initPopover(element);
                        } else if (element.hasClass('fc-time-grid-event')) {
                            element.find('.fc-title').append('<div class="fc-description">' + event.description + '</div>');
                        } else if (element.find('.fc-list-item-title').lenght !== 0) {
                            element.find('.fc-list-item-title').append('<div class="fc-description">' + event.description + '</div>');
                        }
                    },
                });
            };

            // Earnings Sliders
            var earningsSlide = function () {
                var carousel1 = $('#kt_earnings_widget .kt-widget30__head .owl-carousel');
                var carousel2 = $('#kt_earnings_widget .kt-widget30__body .owl-carousel');

                carousel1.find('.carousel').each(function (index) {
                    $(this).attr('data-position', index);
                });

                carousel1.owlCarousel({
                    rtl: KTUtil.isRTL(),
                    center: true,
                    loop: true,
                    items: 2,
                });

                carousel2.owlCarousel({
                    rtl: KTUtil.isRTL(),
                    items: 1,
                    animateIn: 'fadeIn(100)',
                    loop: true,
                });

                $(document).on('click', '.carousel', function () {
                    var index = $(this).attr('data-position');
                    if (index) {
                        carousel1.trigger('to.owl.carousel', index);
                        carousel2.trigger('to.owl.carousel', index);
                    }
                });

                carousel1.on('changed.owl.carousel', function () {
                    var index = $(this).find('.owl-item.active.center').find('.carousel').attr('data-position');
                    if (index) {
                        carousel2.trigger('to.owl.carousel', index);
                    }
                });

                carousel2.on('changed.owl.carousel', function () {
                    var index = $(this).find('.owl-item.active.center').find('.carousel').attr('data-position');
                    if (index) {
                        carousel1.trigger('to.owl.carousel', index);
                    }
                });
            };

            return {
                // Init demos
                init: function () {
                    // init charts
                    dailySales();
                    profitShare();
                    salesStats();
                    salesByApps();
                    latestUpdates();
                    trendsStats();
                    trendsStats2();
                    latestTrendsMap();
                    revenueChange();
                    chartRemoveBg();
                    supportCases();
                    supportRequests();
                    activitiesChart();
                    bandwidthChart1();
                    bandwidthChart2();
                    adWordsStat();
                    financeSummary();
                    quickStats();
                    orderStatistics();

                    // datatables
                    //  datatableLatestOrders();

                    // calendar
                    calendarInit();

                    // earnings slide
                    earningsSlide();


                    // demo loading
                    var loading = new KTDialog({
                        'type': 'loader',
                        'placement': 'top center',
                        'message': locales.loading
                    });
                    loading.show();

                    setTimeout(function () {
                        loading.hide();
                    }, 3000);
                },
            };
        }();

        // Class initialization on page load
        jQuery(document).ready(function () {
            KTDashboard.init();
        });
        var latestUpdates = function () {
            if ($('#kt_chart_latest_updates').length == 0) {
                return;
            }

            var ctx = document.getElementById("kt_chart_latest_updates").getContext("2d");
            @php($payments=Subscription::where('status',1)->where('created_at','>=',now()->subMonth())->get()->groupBy(function ($payment) {
                return $payment->created_at->format('Y-m-d');
            })->map(function ($payments, $date) {
                return $payments->sum('amount');
            })->sortBy(function($r,$i){
            return $i;
            }))

            var config = {
                type: 'line',
                data: {
                    labels: @json($payments->keys()),
                    datasets: [{
                        label: "Sales Stats",
                        backgroundColor: KTApp.getStateColor('danger'), // Put the gradient here as a fill color
                        borderColor: KTApp.getStateColor('danger'),
                        pointBackgroundColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                        pointBorderColor: Chart.helpers.color('#000000').alpha(0).rgbString(),
                        pointHoverBackgroundColor: KTApp.getStateColor('success'),
                        pointHoverBorderColor: Chart.helpers.color('#000000').alpha(0.1).rgbString(),

                        //fill: 'start',
                        data: @json($payments->values())
                    }]
                },
                options: {
                    title: {
                        display: false,
                    },
                    tooltips: {
                        intersect: false,
                        mode: 'nearest',
                        xPadding: 10,
                        yPadding: 10,
                        caretPadding: 10
                    },
                    legend: {
                        display: false
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    hover: {
                        mode: 'index'
                    },
                    scales: {
                        xAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Month'
                            }
                        }],
                        yAxes: [{
                            display: false,
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: 'Value'
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    elements: {
                        line: {
                            tension: 0.0000001
                        },
                        point: {
                            radius: 4,
                            borderWidth: 12
                        }
                    }
                }
            };

            var chart = new Chart(ctx, config);
        }
        latestUpdates();
    </script>
@endpush
