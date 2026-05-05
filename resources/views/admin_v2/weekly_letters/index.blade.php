@extends('admin_v2.layout.app')
@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--mobile">
            <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="kt-datatable" id="kt_datatable"></div>
            </div>
        </div>
    </div>
@endsection
@push('css')
@endpush
@push('scripts')
    <script>
        var $states = [
            'primary',
            'success',
            'info',
            'warning',
            'danger',
        ];
        var datatable = $('#kt_datatable').KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        method: 'GET',
                        url: '{{ route('admin.weekly_letters.index') }}',
                        params: {
                            datatable: 1
                        },
                        map: function (raw) {
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
            storage : false, 
            layout: {
                scroll: false,
                footer: false,
            },
            pagination: true,
            search: {
                input: $('#generalSearch'),
            },
            columns: [{
                field: 'title',
                title: '{{ __('views.Title') }}',
            }, 
            {
                field: 'target',
                title: '{{ __('views.letter_target') }}',
            },
            {
                field: 'status_sent',
                title: '{{ __('views.sent') }}',
                textAlign: 'center',
                width:120
            },
                {
                    field: 'created_at',
                    title: '{{ __('views.Date of Creation') }}',
                    textAlign: 'center',
                    template: function (r) {
                        return moment(r.created_at).format('LLLL');
                    }
                },{
                    field: "Actions",
                    width: 80,
                    title: "{{__('views.Actions')}}",
                    sortable: false,
                    autoHide: false,
                    overflow: 'visible',
                    template: function(row) {

                        var show_link = '{{$show_link}}'.replace('/0', '/' + row.id);


                        return '\
                              <a href="'+show_link+'" class="kt-nav__link">\
                        <i class="kt-nav__link-icon flaticon2-eye"></i>\
                        <span class="kt-nav__link-text">{{__('views.Show')}}</span>\
                      </a>\
            ';
                    },
                }
                ],
        });
        $('#kt_datatable').on('datatable-on-layout-updated', function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
@endpush
