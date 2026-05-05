@extends('admin_v2.layout.app')

@section('content')
<input type="hidden" name="is_request" id="is_request" value="{{$is_request}}">
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <!--begin::Portlet-->
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <!--begin: Datatable -->
            <div class="kt-datatable" id="kt_member_list_datatable"></div>
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
    datatable = $('#kt_member_list_datatable').KTDatatable({
      // datasource definition
      data: {
        type: 'remote',
        source: {
          read: {
            url: '{{$index_url}}',
            params: {
              _token: '{{csrf_token()}}',
              is_request: $("#is_request").val(),
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
      columns: [
      //   {
      //   field: 'id',
      //   title: '#',
      //   sortable: false,
      //   width: 20,
      //   // selector: {
      //   //   class: 'kt-checkbox--solid'
      //   // },
      //   textAlign: 'center',
      // },
      {
        field: 'id',
        title: '#',
        sortable: false,
        width: 20,
        textAlign: 'center',
      },
      {
        field: 'id',
        title: '#',
        sortable: false,
        width: 20,
        textAlign: 'center',
      },
      {
        field: 'contributor.name',
        title: '{{__('views.Name')}}',
        sortable: false,
      },
      {
        field: 'email',
        title: '{{__('views.email')}}',
      },
       {
        field: 'created_at',
        title: '{{__('views.Date of Creation')}}',
        template: function(row, index, datatable) {
          return moment(row.created_at).format("YYYY/MM/DD HH:mm");
        },
      },
      {
        field: 'contributor.current_profit',
        title: '{{__('views.current_profit')}}',
        sortable: false,
      },
      {
        field: 'value_withdraw',
        title: '{{__('views.withdraw_value')}}',
      },
      {
        field: 'status_desc_payout',
        title: 'حالة تحويل الرصيد',
        width: 100,
        // callback function support for column rendering
        template: function(row) {
          var status_desc_payout = {
            'WAITING':{
              'title': '{{trans('paypal.WAITING')}}',
              'class': ' btn-label-info'
            },
            'PROCESSING': {
              'title': '{{trans('paypal.PROCESSING')}}',
              'class': ' btn-label-warning'
            },
            'STOPPED': {
              'title': '{{trans('paypal.STOPPED')}}',
              'class': ' btn-label-danger'
            },
            'UNCLAIMED': {
              'title': '{{trans('paypal.UNCLAIMED')}}',
              'class': ' btn-label-warning'
            },
            'SUCCESS': {
              'title': '{{trans('paypal.SUCCESS')}}',
              'class': ' btn-label-success'
            },
          };
          return '<span class="btn btn-bold btn-sm btn-font-sm ' + _.get(status_desc_payout, [row.status_desc_payout, 'class'], '') + '">' + _.get(status_desc_payout, [row.status_desc_payout, 'title'], '') + '</span>';
        }
      },
      {
        field: "Actions",
        width: 80,
        title: "{{__('views.Actions')}}",
        sortable: false,
        autoHide: false,
        overflow: 'visible',
        template: function(row) {
          var edit_url = '{{$edit_url}}'.replace('0',row.id)
          var delete_url = '{{$destroy_url}}'.replace('0',row.id)
          return '\
              <div class="dropdown">\
                <a href="javascript:;" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="dropdown">\
                  <i class="flaticon-more-1"></i>\
                </a>\
                <div class="dropdown-menu dropdown-menu-right">\
                  <ul class="kt-nav">\
                    <li class="kt-nav__item">\
                      <a href="'+edit_url+'" class="kt-nav__link">\
                        <i class="kt-nav__link-icon flaticon2-contract"></i>\
                        <span class="kt-nav__link-text">{{__('views._payout')}}</span>\
                      </a>\
                    </li>\
                    <li class="kt-nav__item">\
                      <a data-to="'+delete_url+'" href="#" data-confirm="{{__('views.Are you sure?')}}" data-csrf="{{csrf_token()}}" data-method="delete" class="kt-nav__link">\
                        <i class="kt-nav__link-icon flaticon2-trash"></i>\
                        <span class="kt-nav__link-text">{{__('views.Cancel')}}</span>\
                      </a>\
                    </li>\
                  </ul>\
                </div>\
              </div>\
            ';
        },
      }
      ]
    });
  }

  // search
  var search = function() {
    $('#kt_form_status').on('change', function() {
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
});
</script>
@endpush
