@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <!--begin::Portlet-->
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__body kt-portlet__body--fit">
          <div class="col-md-12">
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
              <div class="row align-items-center">
                  <div class="col-xl-12  order-xl-1">
                      <div class="row align-items-center">
                          <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                              <div class="form-group">

                                      <label>من تاريخ:</label>


                                      <input type="text" class="form-control" id="kt_datepicker_1" readonly="" placeholder="اختر التاريخ">

                              </div>
                          </div>
                          <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                              <div class="form-group">
                                      <label>إلى تاريخ:</label>
                                      <input type="text" class="form-control" id="kt_datepicker_2" readonly="" placeholder="اختر تاريخ ">
                                  </div>
                              </div>
                          <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                              <div class="form-group">
                                  <label>الخطة:</label>
                                  <select class="form-control select2-input" id="plan_id"
                                          name="plan_id">
                                      <option></option>
                                      @foreach($plans as $r)
                                          <option
                                              value="{{$r->id}}" {{ request('plan_id')==$r->id?'selected':'' }}>{{$r->title_ar}}</option>
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                              <div class="form-group">
                                  <label>العميل:</label>
                                  <select name="user_id"
                                          id="user_id" style="width: 100%;"
                                          data-placeholder="اختر عميل"
                                  ></select>
                              </div>
                          </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
            <!--begin: Datatable -->
            <div class="kt-datatable" id="kt_user_plan_list_datatable"></div>
            <!--end: Datatable -->
        </div>
    </div>
    <!--end::Portlet-->
</div>
<!-- end:: Content -->
@endsection

@push('css')
@endpush


@push('scripts')
<script>
"use strict";
var KTUserListDatatable = function() {

  // variables
  var datatable;

  // init
  var init = function() {
    // init the datatables. Learn more: https://keenthemes.com/metronic/?page=docs&section=datatable
    datatable = $('#kt_user_plan_list_datatable').KTDatatable({
      // datasource definition
      data: {
        type: 'remote',
        source: {
          read: {
            url: '{{$index_url}}',
            params: {
              _token: '{{csrf_token()}}',
            }
          },
        },
        pageSize: 20, // display 20 records per page
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
saveState: {
        cookie: false,
        webstorage: false,
    }
      },

      // layout definition
      layout: {
        scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
        footer: false, // display/hide footer
      },

      // column sorting
      sortable: true,

      pagination: true,

      search: {
        input: $('#generalSearch'),
        delay: 400,
      },

      // columns definition
      columns: [{
        field: 'id',
        title: '#',
        sortable: false,
        width: 20,
        selector: {
          class: 'kt-checkbox--solid'
        },
        textAlign: 'center',
      }, {
        field: 'plan_id',
        title: '{{__('global.user-plans.fields.plan')}}',
        template: function(row, index, datatable) {
          return row.plan.title_ar
        },
      },
      {
        field: 'user_id',
        title: '{{__('global.user-plans.fields.user')}}',
        template: function(row, index, datatable) {
          if(row.user)
          return row.user.name;
          return "";
        },
      },
      {
                field: 'country_id',
                title: 'مكان الشراء',
                template: function (row) {
                  var address = '';
                  if(row.country)
                  address = row.country.name_ar;
                  if(row.city)
                   address += ' - '+row.city.name_ar;

                  return address;
                },
              },
       {
        field: 'starts_at',
        title: '{{__('global.user-plans.fields.date-start')}}',
        template: function(row, index, datatable) {
          return row.starts_at !== null?moment(row.starts_at).format("YYYY/MM/DD HH:mm"):'-';
        },
      }, {
        field: 'ends_at',
        title: '{{__('global.user-plans.fields.date-end')}}',
        template: function(row, index, datatable) {
          return row.ends_at !== null? moment(row.ends_at).format("YYYY/MM/DD HH:mm"):'-';
        },
      }, {
        field: 'days_remaining',
        title: '{{__('global.user-plans.fields.days-remaining')}}',
        sortable: false,
        template: function(row, index, datatable) {
          var date1 = '{{now()->format('Y-m-d H:i:s')}}';
          var date2 = moment(row.ends_at);
          return (date2.diff(date1, 'days')) <= 0 ? "0" :   (date2.diff(date1, 'days')) ;
        },
      }, {
        field: 'download_remaining',
        title: '{{__('global.user-plans.fields.download-remaining')}}',
        sortable: false,
      },
      {
        field: 'amount',
         title: '{{__('global.plan_price')}}',
         template: function(row, index, datatable) {
          return  '$ ' + row.amount;
        },
      },
      {
        field: 'price-per-photo',
         title: '{{__('global.user-plans.fields.price-per-photo')}}',
         template: function(row, index, datatable) {
          return  '$ ' +( row.amount / row.plan.downloads_count).toFixed(2);
        },
      },
      {
        field: 'status',
        title: '{{__('views.Status')}}',
        width: 100,
        // callback function support for column rendering
        template: function(row) {
          var statuses = {
            0: {
              'title': '{{__('views.Inactive')}}',
              'class': ' btn-label-danger'
            },
            1: {
              'title': '{{__('views.Active')}}',
              'class': ' btn-label-success'
            },
          };
          var status = 0
          var date1 = '{{now()->format('Y-m-d H:i:s')}}';
          var date2 = moment(row.ends_at);
          var days_remaining = date2.diff(date1, 'days');
          //console.log(row.id + ' '+ date1 + '  ' + days_remaining);
          if((row.download_remaining > 0) && (days_remaining > 0)) {
            status = 1
          }
          return '<span class="btn btn-bold btn-sm btn-font-sm ' + statuses[status].class + '">' + statuses[status].title + '</span>';
        }
      }, {
        field: "Actions",
        width: 80,
        title: "{{__('views.Actions')}}",
        sortable: false,
        autoHide: false,
        overflow: 'visible',
        template: function(row) {
          var show_url = '{{$show_url}}'.replace('/0','/' +row.id)
          return '\
              <div class="dropdown">\
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
                  <i class="flaticon-more-1"></i>\
                </a>\
                <div class="dropdown-menu dropdown-menu-right">\
                  <ul class="kt-nav">\
                    <li class="kt-nav__item">\
                      <a href="'+show_url+'" class="kt-nav__link">\
                        <i class="kt-nav__link-icon flaticon2-contract"></i>\
                        <span class="kt-nav__link-text">{{__('views.Show')}}</span>\
                      </a>\
                    </li>\
                  </ul>\
                </div>\
              </div>\
            ';
        },
      }]
    });
    $('#kt_datepicker_2').on('change', function () {
        if ($('#kt_datepicker_1').val()) {
          datatable.search([$('#kt_datepicker_1').val(),$('#kt_datepicker_2').val()], 'date_range');
        }
    });

      $('#kt_datepicker_1').on('change', function () {
          if ($('#kt_datepicker_2').val()) {
              datatable.search([$('#kt_datepicker_1').val(), $('#kt_datepicker_2').val()], 'date_range');
          }
      });
      $(document).on('change', '#plan_id', function () {
          datatable.search($('#plan_id').val(), 'plan_id');
      });
      $(document).on('change', '#user_id', function () {
          datatable.search($('#user_id').val(), 'user_id');
      });
  }

    // search
    var search = function () {
        $('#kt_form_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });
    }

    // selection
  var selection = function() {
    // init form controls
    //$('#kt_form_status, #kt_form_type').selectpicker();

    // event handler on check and uncheck on records
    datatable.on('kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated',  function(e) {
      var checkedNodes = datatable.rows('.kt-datatable__row--active').nodes(); // get selected records
      var count = checkedNodes.length; // selected records count

      $('#kt_subheader_group_selected_rows').html(count);

      if (count > 0) {
        $('#kt_subheader_search').addClass('kt-hidden');
        $('#kt_subheader_group_actions').removeClass('kt-hidden');
      } else {
        $('#kt_subheader_search').removeClass('kt-hidden');
        $('#kt_subheader_group_actions').addClass('kt-hidden');
      }
    });
  }

  // selected records delete
  var arabsSubheaderAction = function() {
    $('.arabs_subheader_action_button').on('click', function() {
      // fetch selected IDs
      var ids = datatable.rows('.kt-datatable__row--active').nodes().find('.kt-checkbox--single > [type="checkbox"]').map(function(i, chk) {
        return $(chk).val();
      }).toArray();

      if (ids.length > 0) {
        // learn more: https://sweetalert2.github.io/
        var message = $(this).data('action-confirm')
        swal.fire({
          text: message.replace("0", ids.length),
          type: "info",
        }).then(function(result) {
          if (result.value) {
            var action_url = $(this).data('action-url').replace('0', ids.join(','))
            post(action_url, {
              _token: document.querySelector('meta[name=csrf-token]').content,
            }, $(this).data('action-method'))
          }
        }.bind(this));
      }
    });

    $('.arabs_subheader_action_dropdown').on('click', "a", function() {
      var label = $(this).find(".kt-nav__link-text").text();
      var value = $(this).data('action-value');

      // fetch selected IDs
      var ids = datatable.rows('.kt-datatable__row--active').nodes().find('.kt-checkbox--single > [type="checkbox"]').map(function(i, chk) {
        return $(chk).val();
      }).toArray();

      if (ids.length > 0) {
        // learn more: https://sweetalert2.github.io/
        var message = $(this).data('action-confirm')
        swal.fire({
          html: message.replace("0", ids.length).replace("ttt", label),
          type: "info",
        }).then(function(result) {
          if (result.value) {
            var action_url = $(this).data('action-url').replace('0', ids.join(','))
            post(action_url, {
              ids: ids,
              status: value,
              _token: document.querySelector('meta[name=csrf-token]').content,
            }, $(this).data('action-method'))
          }
        }.bind(this));
      }
    });

  }

  var updateTotal = function() {
    datatable.on('kt-datatable--on-layout-updated', function () {
      $('#kt_subheader_total').html('{{__('views.:number Total', ['number' => 0])}}'.replace("0", datatable.getTotalRows()));
    });
  };

  return {
    // public functions
    init: function() {
      init();
      search();
      selection();
      arabsSubheaderAction();
      updateTotal();
    },
  };
}();

// On document ready
KTUtil.ready(function() {
  KTUserListDatatable.init();
    $('#user_id').select2({
        language: "ar",
        placeholder: '{{ __('views.Choose Users') }}',
        ajax: {
            url: '{{ route('admin.members.ajax') }}',
            dataType: 'json',
            method: 'get',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
});
</script>
@endpush
