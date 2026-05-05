@extends('admin_v2.layout.app')

@section('content')
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <div class="col-md-12">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <form action="" method="get" class="row align-items-center">
                        <div class="col-md-8  order-xl-1">
                            <div class="row align-items-center">




                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المساهم</label>
                                        <select name="contributor_id"
                                                id="contributor_id" style="width: 100%;"
                                                data-placeholder="اختر مساهم"
                                        ></select>
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المشرف</label>
                                        <select name="user_id"
                                                id="user_id" style="width: 100%;"
                                                data-placeholder="اختر مشرف"
                                        >
                                            <option value=""></option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col kt-margin-b-20-tablet-and-mobile">
                                  <div class="form-group">
                                      <label>من تاريخ:</label>
                                      <input type="text" name="date_from" class="form-control" id="kt_datepicker_1" readonly="" placeholder="اختر التاريخ">
                                  </div>
                              </div>
                              <div class="col kt-margin-b-20-tablet-and-mobile">
                                  <div class="form-group">
                                      <label>إلى تاريخ:</label>
                                      <input type="text" name="date_to" class="form-control" id="kt_datepicker_2"
                                             readonly="" placeholder="اختر تاريخ ">
                                  </div>
                              </div>
                                  <div class="col-md-2 order-md-2 kt-align-right">

                                 </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="kt-datatable" id="kt_image_list_datatable"></div>
        </div>
    </div>
</div>
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
    datatable = $('#kt_image_list_datatable').KTDatatable({
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
          return '  <img src="{{cdn('')}}/' + row.thumbnail + '" width="110" class="img-thumbnail" />';
        },
      }, {
        field: 'user_id',
        title: '{{__('misc.uploaded_by')}}',
        template: function (row) {
          return row.user? row.user.username:'';
        },
      }, {
        field: 'likes_count',
        title: '{{__('misc.likes')}}',
        sortable: false,
      }, {
        field: 'downloads_count',
        title: '{{ trans('misc.downloads') }}',
        sortable: false,
        template: function (row) {
            var $total = row.downloads_count + row.old_downloads_count;
            return $total ? $total : '0';
        },
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
          return moment(row.date).format("YYYY/MM/DD HH:mm");
        },
      }]
    });
  }


    $('#kt_datepicker_2').on('change', function () {
        datatable.search($('#kt_datepicker_2').val(), 'date_to');
    });

    $('#kt_datepicker_1').on('change', function () {
        datatable.search($('#kt_datepicker_1').val(), 'date_from');
    });
    // search
    var search = function () {
        $('#contributor_id').on('change', function () {
            datatable.search($('#contributor_id').val(), 'contributor_id');
        });
        $('#user_id').on('change', function () {
            datatable.search($('#user_id').val(), 'user_id');
        });

    }

    // selection
    var selection = function () {
        // init form controls
        //$('#kt_form_status, #kt_form_type').selectpicker();

        // event handler on check and uncheck on records
        datatable.on('kt-datatable--on-check kt-datatable--on-uncheck kt-datatable--on-layout-updated', function (e) {
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
        init: function () {
            init();
            search();
            selection();
            arabsSubheaderAction();
            updateTotal();
        },
    };
}();

// On document ready
KTUtil.ready(function () {
  $('#kt_datepicker_2 ,#kt_datepicker_1').datepicker({
        autoclose: true, // Close the datepicker after selecting a date
        clearBtn: true, // Display the clear button

    });
    
    KTUserListDatatable.init();
    $('.select2_folder').select2();
    $('#contributor_id').select2({
        language: "ar",
        placeholder: '{{ __('views.Choose Users') }}',
        ajax: {
            url: '{{ route('admin.contributor.datatable') }}',
            dataType: 'json',
            method: 'post',
            delay: 250,
            data: function (params) {
                var query = {
                    query: {generalSearch: params.term},
                    pagination: {
                        page: 1,
                        perpage: 10,
                    }
                }
                return query;
            },
            processResults: function (data) {
                return {
                    results: $.map(data.data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
    $('#user_id').select2();
    $('#status').select2();
    $('#status').trigger('change');
});
</script>
@endpush
