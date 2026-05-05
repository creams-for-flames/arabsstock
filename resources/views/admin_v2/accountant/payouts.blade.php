@extends('admin_v2.layout.app')

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        سحوبات المساهمين
                    </h3>
                </div>
            </div>
            <form class="kt-form " action="{{route('admin.payout.export')}}" method="get">
                <div class="kt-portlet__body">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <div class="row align-items-center">
                        <div class="col-md-8 order-md-1">
                            <div class="row align-items-center">
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>من تاريخ:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_1"
                                               name="date_from" readonly="" placeholder="اختر التاريخ">
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>إلى تاريخ:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_2"
                                               name="date_to" readonly="" placeholder="اختر تاريخ ">
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المساهم</label>
                                        <select name="contributor_id"
                                                style="width: 100%;"
                                                data-placeholder="اختر مساهم"
                                        ></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 order-1 order-xl-2 kt-align-right">
                            <button type="submit" class="btn btn-default ">
                                <i class="la la-file-excel-o"></i> تصدير
                            </button>
                            <div
                                class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        احصائيات
                    </h3>
                </div>
            </div>
            <form class="kt-form " action="{{route('admin.contributors.statistics.export')}}" method="get">
                <div class="kt-portlet__body">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <div class="row align-items-center">
                        <div class="col-md-8 order-md-1">
                            <div class="row align-items-center">
                                <div class="col-md-2 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group pt-4">
                                        <label class="kt-checkbox kt-checkbox--success">
                                            <input type="checkbox" id="with_date" name="with_date" value="1"> تحديد تاريخ
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>إلى تاريخ:</label>
                                        <input type="text" class="form-control" id="kt_datepicker_3" disabled
                                               name="date_to" placeholder="اختر تاريخ " autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المساهم</label>
                                        <select name="contributor_id"
                                                style="width: 100%;"
                                                data-placeholder="اختر مساهم"
                                        ></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 order-1 order-xl-2 kt-align-right">
                            <button type="submit" class="btn btn-default ">
                                <i class="la la-file-excel-o"></i> تصدير
                            </button>
                            <div
                                class="kt-separator kt-separator--border-dashed kt-separator--space-custom d-xl-none"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
@endpush


@push('scripts')
    <script src="{{ asset('js/select2@4.1.0-rc.0_dist_js_select2.min.js') }}"></script>
    <script>
        $('[name="contributor_id"]').select2({
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
        $('#with_date').on('change', function () {
            if ($('#with_date').is(':checked')) {
                $('#kt_datepicker_3').attr('disabled', false);
            } else {
                $('#kt_datepicker_3').attr('disabled', true);
            }
        });
    </script>
    <style>
        .select2-ajax{
            width: 200px;
        }
    </style>
@endpush
