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

                        <div class="col-md-8 order-md-1">
                            <div class="row align-items-center">

                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>من تاريخ:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_1" name="date_from" readonly="" placeholder="اختر التاريخ">
                                    </div>
                                </div>
                                <div class="col-md-4 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>إلى تاريخ:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_2" name="date_to" readonly="" placeholder="اختر تاريخ ">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 order-md-2 kt-align-right">
                          <button type="button" id="reset-date" class="btn btn-default ">
                              <i class="la la-reset"></i> Reset
                          </button>
                      </div>
                        <div class="col-md-2 order-md-2 kt-align-right">
                            <button type="submit" class="btn btn-success ">
                                <i class="la la-file-excel-o"></i> Export Excel
                            </button>
                        </div>

                    </div>
                <div class="row p-0 m-0">
                    <div class="col-md-12 p-0 m-0">
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                    </div>
                </div>
            </div>
          </div>
            <!--begin: Datatable -->
            <div class="kt-datatable" id="kt_image_pending_list_datatable"></div>
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
    datatable = $('#kt_image_pending_list_datatable').KTDatatable({
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
        field: @if(app()->getLocale() == 'ar') 'title_ar' @else 'title_en' @endif,
        width:150,
        title: '{{__('admin.title')}}',
      }, {
        field: 'thumbnail',
        title: '{{__('misc.thumbnail')}}',
        sortable: false,
        template: function (row) {
          return '  <img src="{{asset('')}}' + row.thumbnail + '" width="110" class="img-thumbnail"/>';
        },
      }, {
        field: 'user_id',
        title: '{{__('misc.uploaded_by')}}',
        template: function (row) {
          return row.user? row.user.username:'';
        },
      }, {
        field: 'likes',
        title: '{{__('misc.likes')}}',
        sortable: false,
        template: function(row, index, datatable) {
          if (row.likes.length > 0) {
            return ''+row.likes.length;
          } else {
            return '0';
          }
        },
      }, {
        field: 'downloads',
        title: '{{ trans('misc.downloads') }}',
        sortable: false,
        template: function (row) {
          if (row.downloads.length > 0) {
            return ''+row.downloads.length;
          } else {
            return '0';
          }
        },
      }, {
        field: 'admin_collection',
        title: '{{__('misc.admin_collection')}}',
        sortable: false,
        template: function(row) {
          if (row.admin_collection.length > 0) {
            return row.admin_collection[0].title;
          } else {
            return '0';
          }
        }
      }, {
        field: 'category',
        title: '{{__('misc.category')}}',
        sortable: false,
        template: function(row) {
          return row.category.map(function (c) {return c.name}).join(', ')
        }
      }, {
        field: 'date',
        title: '{{__('admin.date')}}',
        template: function(row, index, datatable) {
          return moment(row.deleted_at).format("YYYY/MM/DD HH:mm");
        },
      }, {
        field: 'status',
        title: '{{__('views.Status')}}',
        autoHide: false,
        width: 100,
        // callback function support for column rendering
        template: function(row) {
          var statuses = {
            pending: {
              'title': '{{__('admin.pending')}}',
              'class': ' btn-label-danger'
            },
            active: {
              'title': '{{__('admin.active')}}',
              'class': ' btn-label-success'
            },
          };
          return '<span class="btn btn-bold btn-sm btn-font-sm ' + statuses[row.status].class + '">' + statuses[row.status].title + '</span>';
        }
      },

      ]
    });
  }
/*  */
    $("#reset-date").click(function(e){
      $('#kt_datepicker_1').val("").datepicker("update");
      $('#kt_datepicker_2').val("").datepicker("update");
      return;
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



/*  */
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

