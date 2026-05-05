@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <!--begin::Portlet-->
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <!--begin: Datatable -->
            <div class="kt-datatable" id="kt_user_plan_items_list_datatable"></div>
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
    datatable = $('#kt_user_plan_items_list_datatable').KTDatatable({
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
        serverSorting: false,
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
        field: 'images_id',
        title: '#',
        sortable: false,
        width: 40,
        textAlign: 'center',
      }, {
        field: 'user_id',
        title: '{{__('global.user-plans.fields.user')}}',
        template: function(row, index, datatable) {
          if(row.client)
            return row.client.name
        },
      },
      {
        field: 'imageshow',
        title: '',
        template: function(row, index, datatable) {
          // var image=row.image.thumbnail;
          if(row.image)
            var image_url='{{url('')}}'.replace('0', row.image.thumbnail);
          if(row.image)
            return '<img width="100"  src="'+image_url+'" alt="image">';

        },
      },

      {
        field: 'image',
        title: '{{__('global.payment-methods.fields.image')}}',
        template: function(row, index, datatable) {

          if(row.image)
            return '<a target="_blank" href="'+row.image.post_link+'">'+ row.image.title + '</a>';
        },
      },
      {
        field: 'ip',
        title: '{{__('global.ip')}}',

      },
      {
        field: 'plan_id',
        title: '{{__('global.user-plans.fields.plan')}}',
        template: function(row, index, datatable) {
          if(row.plan)
            return row.plan.title_en
        },
      },
       {
        field: 'date',
        title: '{{__('global.downloaded_at')}}',
        template: function(row, index, datatable) {
          return moment(row.date).format("YYYY/MM/DD HH:mm");
        },
      },
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
