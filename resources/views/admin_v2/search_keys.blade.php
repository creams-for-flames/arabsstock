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
                        url: '{{ $data_path }}',
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
            layout: {
                scroll: false,
                footer: false,
            },
            pagination: true,
            search: {
                input: $('#generalSearch'),
            },
            columns: [{
                field: 'key_word',
                title: '{{ __('Search Key') }}',
            }, {
                field: 'count',
                title: '{{ __('Search times') }}',
                textAlign: 'center',
                width: 120
            },
                {
                field: 'lang',
                title: '{{ __('misc.language') }}',
                textAlign: 'center',
                width: 120,
                template: function (r) {
                    return r.lang == 'ar' ? 'عربي' : 'English';
                },
            },
                {
                    field: 'created_at',
                    title: '{{ __('First Search') }}',
                    textAlign: 'center',
                    template: function (r) {
                        return moment(r.created_at).format('llll');
                    }
                },
                {
                    field: 'updated_at',
                    title: '{{ __('Last Search') }}',
                    textAlign: 'center',
                    template: function (r) {
                        return moment(r.updated_at).format('llll');
                    }
                }],
        });
        $('#kt_datatable').on('datatable-on-layout-updated', function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
@endpush
