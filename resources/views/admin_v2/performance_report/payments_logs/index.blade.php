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

            <!--end: Search Form -->
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="col-md-12">
              <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="row align-items-center">
                    <div class="col-xl-8  order-xl-1">
                        <div class="row align-items-center">
                            <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                <div class="kt-input-icon kt-input-icon--left form-group">
                                  <label>ابحث :</label>
                                    <input type="text" class="form-control" placeholder="بحث..." id="generalSearch">
                                    <span class="kt-input-icon__icon kt-input-icon__icon--left kt-margin-t-15">
                                        <span><i class="la la-search"></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                <div class="form-group">

                                        <label>{{trans('views.From')}}:</label>


                                        <input type="text" class="form-control" id="kt_datepicker_1" data-date-format="yyyy-mm-dd" readonly="" placeholder="{{trans('views.From')}}">

                                </div>
                            </div>
                            <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                <div class="form-group">

                                        <label>{{trans('views.To')}}:</label>


                                        <input type="text" class="form-control" id="kt_datepicker_2" data-date-format="yyyy-mm-dd" readonly="" placeholder="{{trans('views.To')}} ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                field: 'user_id',
                title: '{{trans('views.Username')}}',
                template: function (row) {
                  if(row.user)
                  return row.user.name;
                 return '---';
                },
              },
              // {
              //   field: 'webhook_id',
              //   title: 'Webhook ID',
              // },
              {
                field: 'event_type',
                title: '{{trans('views.Event Type')}}',
                template: function (row) {
                  return row.event_type;
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
            // if ($('#kt_datepicker_2').val()) {
            datatable.search([$('#kt_datepicker_1').val(),null], 'date_range');
            // }
          });


          $('#kt_form_type').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'Type');
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

