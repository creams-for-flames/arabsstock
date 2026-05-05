@extends('admin_v2.layout.app')

@section('content')

<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
<!--Begin::Dashboard 1-->


    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    إحصائية التجميعة
                </h3>
            </div>

        </div>


        <div class="kt-form kt-form--label-align-right kt-margin-t-20 kt-margin-r-20 kt-margin-l-20" id="kt_datatable_group_action_form">
            <div class="row align-items-center">
                <div class="col-xl-12">
                    <div class="kt-form__group kt-form__group--inline">
                        <form id="formtest" class="form-horizontal" method="get" action="#">
                            <div class="row">


                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <input type="hidden" class="js-range-slider111" value=""/>
                                            <div class="form-group">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label class="col-md-4 control-label mrgintop">{{__('السعر من')}}</label>
                                                            <div class="col-md-8">
                                                                <div class="input-group">
                                                                    <div class="input-group-addon">
                                                                        <i class="fa fa-clock-o"></i></div>
                                                                    <input onkeyup=" if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" class="form-control pull-right"
                                                                           value="{{request('fromPrice')?request('fromPrice'):''}}"
                                                                           name="fromPrice" id="fromPrice">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label class="col-md-4 control-label mrgintop">{{__('السعر حتى')}}</label>
                                                            <div class="col-md-8">
                                                                <div class="input-group">
                                                                    <div class="input-group-addon">
                                                                        <i class="fa fa-clock-o"></i></div>
                                                                    <input onkeyup=" if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" class="form-control pull-right"
                                                                           value="{{request('toPrice')?request('toPrice'):''}}"
                                                                           name="toPrice" id="toPrice">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <input placeholder="السعر من" name="fromPrice2" type="hidden" class="js-input-from form-control"
                                                       value="0"/>
                                                <input placeholder="السعر حتى" name="toPrice2" type="hidden" class="js-input-to form-control" value="0"/>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 control-label mrgintop">{{__('من تاريخ')}}</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                                <input type="text" class="form-control pull-right"
                                                       value="{{request('from_date')?request('from_date'):''}}"
                                                       name="from_date" id="kt_datepicker_1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 control-label mrgintop">{{__('حتى تاريخ')}}</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                                                <input type="text" class="form-control pull-right"
                                                       value="{{request('to_date')?request('to_date'):''}}"
                                                       name="to_date" id="kt_datepicker_1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-12">


                                            <button class="btn btn-sm btn-brand btn-right" type="button" id="kt_datatable__all">
                                                {{__('بحث')}}
                                                <i class="fa fa-search"></i>
                                            </button>


                                            <a href="{{$index_url}}" type="submit"
                                               class="btn sbold btn-default  btn-left">{{__('الغاء')}}
                                                <i class="fa fa-refresh"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <div class="kt-form kt-form--label-align-right kt-margin-t-20 kt-margin-r-20 kt-margin-l-20" id="kt_datatable_group_action_form">
        <div class="kt-form__control">
            <div class="btn-toolbar">
                <label class="kt-font-bold kt-font-danger-">مجموع الدخل
                    <span id="kt_datatable_selected_number">{{$total_price}}</span>
                    ريال:</label>
            </div>
        </div>
        </div>
        <div class="kt-portlet__body kt-portlet__body--fit">

            <!--begin: Datatable -->
            <div class="kt-datatable" id="local_record_selection"></div>


            <div class="modal fade" id="kt_modal_fetch_id" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="kt-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-height="200">
                                <ul class="kt-datatable_selected_ids"></ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--end::Modal-->

            <!--begin::Modal-->
            <div class="modal fade" id="kt_modal_fetch_id_server" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="kt-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-height="200">
                                <ul class="kt-datatable_selected_ids"></ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--end: Datatable -->
        </div>
    </div>

<!--End::Dashboard 1-->
</div>
<!-- end:: Content -->

@endsection

@push('css')
    <style>
        .irs {
            position: relative;
            display: block;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .irs-line {
            position: relative;
            display: block;
            overflow: hidden;
            outline: none !important;
        }

        .irs-line-left, .irs-line-mid, .irs-line-right {
            position: absolute;
            display: block;
            top: 0;
        }

        .irs-line-left {
            left: 0;
            width: 11%;
        }

        .irs-line-mid {
            left: 9%;
            width: 82%;
        }

        .irs-line-right {
            right: 0;
            width: 11%;
        }

        .irs-bar {
            position: absolute;
            display: block;
            left: 0;
            width: 0;
        }

        .irs-bar-edge {
            position: absolute;
            display: block;
            top: 0;
            left: 0;
        }

        .irs-shadow {
            position: absolute;
            display: none;
            left: 0;
            width: 0;
        }

        .irs-slider {
            position: absolute;
            display: block;
            cursor: default;
            z-index: 1;
        }

        .irs-slider.single {

        }

        .irs-slider.from {

        }

        .irs-slider.to {

        }

        .irs-slider.type_last {
            z-index: 2;
        }

        .irs-min {
            position: absolute;
            display: block;
            left: 0;
            cursor: default;
        }

        .irs-max {
            position: absolute;
            display: block;
            right: 0;
            cursor: default;
        }

        .irs-from, .irs-to, .irs-single {
            position: absolute;
            display: block;
            top: 0;
            left: 0;
            cursor: default;
            white-space: nowrap;
        }

        .irs-grid {
            position: absolute;
            display: none;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 20px;
        }

        .irs-with-grid .irs-grid {
            display: block;
        }

        .irs-grid-pol {
            position: absolute;
            top: 0;
            left: 0;
            width: 1px;
            height: 8px;
            background: #000;
        }

        .irs-grid-pol.small {
            height: 4px;
        }

        .irs-grid-text {
            position: absolute;
            bottom: 0;
            left: 0;
            white-space: nowrap;
            text-align: center;
            font-size: 9px;
            line-height: 9px;
            padding: 0 3px;
            color: #000;
        }

        .irs-disable-mask {
            position: absolute;
            display: block;
            top: 0;
            left: -1%;
            width: 102%;
            height: 100%;
            cursor: default;
            background: rgba(0, 0, 0, 0.0);
            z-index: 2;
        }

        .lt-ie9 .irs-disable-mask {
            background: #000;
            filter: alpha(opacity=0);
            cursor: not-allowed;
        }

        .irs-disabled {
            opacity: 0.4;
        }

        .irs-hidden-input {
            position: absolute !important;
            display: block !important;
            top: 0 !important;
            left: 0 !important;
            width: 0 !important;
            height: 0 !important;
            font-size: 0 !important;
            line-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            outline: none !important;
            z-index: -9999 !important;
            background: none !important;
            border-style: solid !important;
            border-color: transparent !important;
        }

        /* Ion.RangeSlider, Simple Skin
        // css version 2.0.3
        // Â© Denis Ineshin, 2014    https://github.com/IonDen
        // Â© guybowden, 2014        https://github.com/guybowden
        // ===================================================================================================================*/

        /* =====================================================================================================================
        // Skin details */

        .irs {
            height: 55px;
        }

        .irs-with-grid {
            height: 75px;
        }

        .irs-line {
            height: 10px;
            top: 33px;
            background: #EEE;
            background: linear-gradient(to bottom, #DDD -50%, #FFF 150%); /* W3C */
            border: 1px solid #CCC;
            border-radius: 16px;
            -moz-border-radius: 16px;
        }

        .irs-line-left {
            height: 8px;
        }

        .irs-line-mid {
            height: 8px;
        }

        .irs-line-right {
            height: 8px;
        }

        .irs-bar {
            height: 10px;
            top: 33px;
            border-top: 1px solid #428bca;
            border-bottom: 1px solid #428bca;
            background: #428bca;
            background: linear-gradient(to top, rgba(66, 139, 202, 1) 0%, rgba(127, 195, 232, 1) 100%); /* W3C */
        }

        .irs-bar-edge {
            height: 10px;
            top: 33px;
            width: 14px;
            border: 1px solid #428bca;
            border-right: 0;
            background: #428bca;
            background: linear-gradient(to top, rgba(66, 139, 202, 1) 0%, rgba(127, 195, 232, 1) 100%); /* W3C */
            border-radius: 16px 0 0 16px;
            -moz-border-radius: 16px 0 0 16px;
        }

        .irs-shadow {
            height: 2px;
            top: 38px;
            background: #000;
            opacity: 0.3;
            border-radius: 5px;
            -moz-border-radius: 5px;
        }

        .lt-ie9 .irs-shadow {
            filter: alpha(opacity=30);
        }

        .irs-slider {
            top: 25px;
            width: 27px;
            height: 27px;
            border: 1px solid #AAA;
            background: #DDD;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(220, 220, 220, 1) 20%, rgba(255, 255, 255, 1) 100%); /* W3C */
            border-radius: 27px;
            -moz-border-radius: 27px;
            box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }

        .irs-slider.state_hover, .irs-slider:hover {
            background: #FFF;
        }

        .irs-min, .irs-max {
            color: #333;
            font-size: 12px;
            line-height: 1.333;
            text-shadow: none;
            top: 0;
            padding: 1px 5px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            -moz-border-radius: 3px;
        }

        .lt-ie9 .irs-min, .lt-ie9 .irs-max {
            background: #ccc;
        }

        .irs-from, .irs-to, .irs-single {
            color: #fff;
            font-size: 14px;
            line-height: 1.333;
            text-shadow: none;
            padding: 1px 5px;
            background: #428bca;
            border-radius: 3px;
            -moz-border-radius: 3px;
        }

        .lt-ie9 .irs-from, .lt-ie9 .irs-to, .lt-ie9 .irs-single {
            background: #999;
        }

        .irs-grid {
            height: 27px;
        }

        .irs-grid-pol {
            opacity: 0.5;
            background: #428bca;
        }

        .irs-grid-pol.small {
            background: #999;
        }

        .irs-grid-text {
            bottom: 5px;
            color: #99a4ac;
        }

        .irs-disabled {
        }
    </style>


@endpush


@push('scripts')

    <script>


      var csrf_token = '{{csrf_token()}}';
      $(document).ready(function () {


        "use strict";
// Class definition

        var KTDatatableRecordSelectionDemo = function () {
          // Private functions

          var options = {
            // datasource definition
            data: {
              type: 'remote',
              source: {
                read: {
                  url: '{{$datatable_url}}',
                  headers: {'X-CSRF-TOKEN': csrf_token},
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

            rows: {
              afterTemplate: function (element, data, rowNumber) {
                element.find('.actionDelete').on('click', function (event) {
                  var element = $(this);
                  var id = element.attr('data-url');
                  var form = $(element).parents('form');

                  element.blur();

                  swal({
                    title: "<?php echo e(trans('misc.delete_confirm'), false); ?>",
                    text: "<?php echo e(trans('misc.yes_confirm'), false); ?>",
                    type: "warning",
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                  }).then((willDelete) => {
                    if (willDelete) {


                      $.ajax({
                        url: id,
                        method: 'post',
                        type: 'post',
                        data: {
                          _token: '{{csrf_token()}}',

                        },
                      })
                        .done(function (data) {

                          if (data.status == true) {
                            toastr.success('@lang('deleted')');
                            element.parent().parent().parent().hide(500);

                          } else if (data.status === 'cant_delete') {
                            toastr.warning('@lang('cant_deleted')');
                          } else {
                            toastr.warning('@lang('not_deleted')');
                          }

                        }).fail(function () {
                        toastr.error('@lang('something_wrong')');
                      });

                    } else {

                    }
                  });

                });
              },
            },
            // columns definition
            columns: [
              {
                field: 'id',
                title: '#',
                sortable: true,
                width: 20,
                type: 'number',
                selector: {class: 'kt-checkbox--solid'},
                textAlign: 'center',
              },
              {
                field: 'thumbnail',
                title: '{{ trans('misc.thumbnail') }}',
                sortable: false,
                template: function (row) {


                  return '  <img src="{{asset('')}}' + row.thumbnail + '" width="50"/>';
                },
              },

              {
                field: 'user',
                title: '{{ trans('misc.user') }}',
                sortable: false,
                template: function (row) {
                  var url = '{{$edit_member_url}}'.replace('0', row.user_id);

                  return '<a href="' + url + '">ملف المستخدم</a>';
                },
              },


              {
                field: 'title',
                title: '{{ trans('admin.title') }}',
                sortable: false,
                template: function (row) {


                  link = row.post_link;

                  @if(app()->getLocale()=='ar')
                      return '<a href="' + link + '">' + row.title_ar + '</a>';
                  @else
                      return '<a href="' + link + '">' + row.title_ar + '</a>';
                  @endif


                },
              },

              {
                field: 'price',
                title: '{{ trans('misc.price') }}',
                sortable: false,
                template: function (row) {
                  var amount = row.price / row.downloads_count;
                  return amount.toFixed(2);

                },
              },

              {
                field: 'date',
                title: 'Create Date',
                sortable: false,
                type: 'date',
                format: 'MM/DD/YYYY',
              },
            ],


          };

          // basic demo
          var localSelectorDemo = function () {

            options.search = {
              input: $('#generalSearch'),
            };

            var datatable = $('#local_record_selection').KTDatatable(options);

            $('#kt_datatable__all').on('click', function () {

              var fillter = {};
              $('#formtest').serializeArray().filter(function (item) {
                return item.value;
              }).map(function (item) {
                fillter[item.name] = item.value;
                return item;
              });

              console.log(fillter);
              datatable.search(fillter, 'fillter');
            });

            $('#kt_form_type').on('change', function () {
              datatable.search($(this).val().toLowerCase(), 'Type');
            });

            $('#kt_form_status,#kt_form_type').selectpicker();

            datatable.on('kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated', function (e) {
              var checkedNodes = datatable.rows('.kt-datatable__row--active').nodes();
              var count = checkedNodes.length;
              $('#kt_datatable_selected_number').html(count);


              if (count > 0) {
                $('#kt_datatable_group_action_form').collapse('show');
              } else {
                $('#kt_datatable_group_action_form').collapse('hide');
              }
            });

            $('#kt_modal_fetch_id').on('show.bs.modal', function (e) {
              var ids = datatable.rows('.kt-datatable__row--active').nodes().find('.kt-checkbox--single > [type="checkbox"]').map(function (i, chk) {
                return $(chk).val();
              });

              console.log(ids);
              var c = document.createDocumentFragment();
              for (var i = 0; i < ids.length; i++) {
                var li = document.createElement('li');
                li.setAttribute('data-id', ids[i]);
                li.innerHTML = 'Selected record ID: ' + ids[i];
                c.appendChild(li);
              }
              $(e.target).find('.kt-datatable_selected_ids').append(c);
            })
              .on('hide.bs.modal', function (e) {
                $(e.target).find('.kt-datatable_selected_ids').empty();
              });

          };

          var serverSelectorDemo = function () {

            // enable extension
            options.extensions = {
              checkbox: {},
            };
            options.search = {
              input: $('#generalSearch1'),
            };

            var datatable = $('#server_record_selection').KTDatatable(options);

            $('#kt_form_status1').on('change', function () {
              datatable.search($(this).val().toLowerCase(), 'Status');
            });

            $('#kt_form_type1').on('change', function () {
              datatable.search($(this).val().toLowerCase(), 'Type');
            });

            $('#kt_form_status1,#kt_form_type1').selectpicker();

            datatable.on(
              'kt-datatable--on-click-checkbox kt-datatable--on-layout-updated',
              function (e) {
                // datatable.checkbox() access to extension methods
                var ids = datatable.checkbox().getSelectedId();
                var count = ids.length;
                $('#kt_datatable_selected_number1').html(count);
                if (count > 0) {
                  $('#kt_datatable_group_action_form1').collapse('show');
                } else {
                  $('#kt_datatable_group_action_form1').collapse('hide');
                }
              });

            $('#kt_modal_fetch_id_server').on('show.bs.modal', function (e) {
              var ids = datatable.checkbox().getSelectedId();
              var c = document.createDocumentFragment();
              for (var i = 0; i < ids.length; i++) {
                var li = document.createElement('li');
                li.setAttribute('data-id', ids[i]);
                li.innerHTML = 'Selected record ID: ' + ids[i];
                c.appendChild(li);
              }
              $(e.target).find('.kt-datatable_selected_ids').append(c);
            }).on('hide.bs.modal', function (e) {
              $(e.target).find('.kt-datatable_selected_ids').empty();
            });

          };

          return {
            // public functions
            init: function () {
              localSelectorDemo();
              //  serverSelectorDemo();
            },
          };
        }();

        jQuery(document).ready(function () {
          KTDatatableRecordSelectionDemo.init();
        });

      });


    </script>


@endpush
