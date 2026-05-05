@extends('admin_v2.layout.app')
@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    المبيعات
                </h3>
            </div>

        </div>
        <div class="kt-portlet__body">
            <br><br>
            <div class="col-lg-12 col-xl-4=12 order-lg-1 order-xl-1">

                <!--begin:: Widgets/Activity-->
                <div class=" kt-portlet--fit kt-portlet--head-lg kt-portlet--head-overlay kt-portlet--skin-solid kt-portlet--height-fluid">
                    <div class="kt-widget17">

                        <div class="kt-widget17__stats display-intial">
                            <div class="kt-widget17__items">
                                <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--brand">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#000000"></path>
                                                <rect fill="#000000" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) " x="16.3255682" y="2.94551858" width="3" height="18" rx="1"></rect>
                                            </g>
                                        </svg> </span>
                                    <span class="kt-widget17__subtitle">

                                        @if(!isset($title)) تحميل الصور @elseif($title == 'video') تحميل الفيديوهات @endif
                                    </span>
                                    <span class="kt-widget17__desc">
                                        {{$image_download}} @if(!isset($title)) صورة @elseif($title == 'video') فيديو @endif
                                    </span>
                                </div>
                                <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--success">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero"></path>
                                                <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3"></path>
                                            </g>
                                        </svg> </span>
                                    <span class="kt-widget17__subtitle">
                                        المبلغ المحصل
                                    </span>
                                    <span class="kt-widget17__desc">
                                        {{$total_amount}} $
                                    </span>
                                </div>
                            </div>
                            <div class="kt-widget17__items">
                                <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--warning">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M12.7037037,14 L15.6666667,10 L13.4444444,10 L13.4444444,6 L9,12 L11.2222222,12 L11.2222222,14 L6,14 C5.44771525,14 5,13.5522847 5,13 L5,3 C5,2.44771525 5.44771525,2 6,2 L18,2 C18.5522847,2 19,2.44771525 19,3 L19,13 C19,13.5522847 18.5522847,14 18,14 L12.7037037,14 Z" fill="#000000" opacity="0.3"></path>
                                                <path d="M9.80428954,10.9142091 L9,12 L11.2222222,12 L11.2222222,16 L15.6666667,10 L15.4615385,10 L20.2072547,6.57253826 C20.4311176,6.4108595 20.7436609,6.46126971 20.9053396,6.68513259 C20.9668779,6.77033951 21,6.87277228 21,6.97787787 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,6.97787787 C3,6.70173549 3.22385763,6.47787787 3.5,6.47787787 C3.60510559,6.47787787 3.70753836,6.51099993 3.79274528,6.57253826 L9.80428954,10.9142091 Z" fill="#000000"></path>
                                            </g>
                                        </svg>
                                        </span>
                                    <span class="kt-widget17__subtitle">
                                        الحزم المشترية
                                    </span>
                                    <span class="kt-widget17__desc">
                                        {{$toaol_subscription}} حزم
                                    </span>
                                </div>
                                {{-- <div class="kt-widget17__item">
                                    <span class="kt-widget17__icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--danger">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"></rect>
                                                <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3"></path>
                                                <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000"></path>
                                            </g>
                                        </svg> </span>
                                    <span class="kt-widget17__subtitle">
                                        الصور المشترية
                                    </span>
                                    <span class="kt-widget17__desc">
                                        34 صورة
                                    </span>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!--end:: Widgets/Activity-->
            </div>
            <!--begin: Search Form -->
            <!--end: Search Form -->
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="col-md-12">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <form action="{{ $export_url }}" method="get">
                        <div class="row align-items-center">
                            <div class="col-md-8 order-md-1">
                                <div class="row align-items-center">
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="kt-input-icon kt-input-icon--left form-group">
                                            <label>ابحث :</label>
                                            <input type="text" class="form-control" placeholder="بحث..."
                                                   id="generalSearch" name="q">
                                            <span class="kt-input-icon__icon kt-input-icon__icon--left kt-margin-t-15">
                                        <span><i class="la la-search"></i></span>
                                    </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>من تاريخ:</label>
                                            <input type="text" class="form-control" id="kt_datepicker_1" readonly=""
                                                   placeholder="اختر التاريخ" name="date_range[]">
                                        </div>
                                    </div>
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>إلى تاريخ:</label>
                                            <input type="text" class="form-control" id="kt_datepicker_2" readonly="" placeholder="اختر تاريخ ">
                                        </div>
                                    </div>
                                    <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                        <div class="form-group">
                                            <label>بوابة الدفع</label>
                                            <select name="payment_method" id="payment_method" class="form-control">
                                                <option value="0">الكل</option>
                                                <option value="{{ \App\Models\PaymentMethod::PAYPAL }}">Paypal</option>
                                                <option value="{{ \App\Models\PaymentMethod::STRIPE }}">Stripe</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 order-1 order-xl-2 kt-align-right">
                                <button type="submit" class="btn btn-default ">
                                    <i class="la la-file-excel-o"></i> Export
                                </button>
                                <div
                                    class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--begin: Datatable -->
        <div class="kt-datatable" id="kt_performance_report_payment_list"></div>
    </div>
</div>
<!-- end:: Content -->
@endsection
@push('css')
@endpush
@push('scripts')
    <script>
      var csrf_token = '{{csrf_token()}}';
      var KTDatatableRemoteAjaxDemo = function () {
        // Private functions

        // basic demo
        var demo = function () {

          var datatable = $('#kt_performance_report_payment_list').KTDatatable({
            // datasource definition
            data: {
              type: 'remote',
              source: {
                read: {
                  url: '{{$index_url}}',
                  headers: {'X-CSRF-TOKEN': csrf_token},
                  // sample custom headers
                  // headers: {'x-my-custom-header': 'some value', 'x-test-header': 'the value'},
                  map: function (raw) {
                    // sample data mapping
                    var dataSet = raw;
                    if (typeof raw.data !== 'undefined') {
                      dataSet = raw.data;
                    }
                    return dataSet;
                  },
                },
              },
              pageSize: 20,
              serverPaging: true,
              serverFiltering: true,
              serverSorting: true,
            },

            // layout definition
            layout: {
              scroll: false,
              footer: false,
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
              input: $('#generalSearch'),
            },

            // columns definition
            columns: [
              {
                field: 'id',
                title: '#',
                sortable: 'asc',
                width: 30,
                type: 'number',
                selector: false,
                textAlign: 'center',
              },
              {
                field: 'payment_id',
                title: '{{ __('Invoice Number') }}',
                template: function (row) {
                  if(row.payment_id!=null){
                    return row.payment_id;
                  }else{
                    return row.subscription_id;
                  }
                },
              },
              {
                field: 'plan_id',
                title: '{{ __('global.plan') }}',
                template: function (row) {
                  if(row.plan!=null){
                    return row.plan.title;
                  }else{
                    return ;
                  }
                },
              },
              {
                field: 'price',
                title: '{{ __('admin.price') }}',
                template: function (row) {
                  if(row.plan!=null){
                    return row.plan.price;
                  }else{
                    return ;
                  }
                },
              },
              {
                field: 'user_id',
                title: '{{ __('global.user-plans.fields.user') }}',
                template: function (row) {
                if(row.user)
                return row.user.name;
                return '---';
                },
              },
              {
                field: 'country_id',
                title: '{{ __('global.user-plans.fields.place') }}',
                template: function (row) {
                if(row.city)
                return row.country?row.country.name_ar:'' + ' - '+row.city.name_ar;
                  return '---';
              },
              },

              {
                field: 'created_at',
                title: '{{ trans('admin.date') }}',
                serverSorting: false,
                sortable: false,
                type: 'date',
                format: 'MM/DD/YYYY',
              },
            ],

          });


          $('#kt_datepicker_2').on('change', function () {
            if ($('#kt_datepicker_1').val()) {
              datatable.search([$('#kt_datepicker_1').val(),$('#kt_datepicker_2').val()], 'date_range');
            }
          });

          $('#kt_datepicker_1').on('change', function () {
            if ($('#kt_datepicker_2').val()) {
              datatable.search([$('#kt_datepicker_1').val(),$('#kt_datepicker_2').val()], 'date_range');
            }
          });


          $('#kt_form_type').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'Type');
          });

          $('#payment_method').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'payment_method');
          });

          $('#kt_form_status,#kt_form_type').selectpicker();

        };

        return {
          // public functions
          init: function () {
            demo();
          },
        };
      }();

      jQuery(document).ready(function () {
        KTDatatableRemoteAjaxDemo.init();
      });
    </script>
@endpush

