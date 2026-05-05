@extends('admin_v2.layout.app')

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        تحميلات فليكس
                    </h3>
                </div>
            </div>
            <form class="kt-form " action="{{route('admin.downloads.export')}}" method="get">
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
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المستخدم</label>
                                        <select name="user_id"
                                                style="width: 100%;"
                                                data-placeholder="اختر مستخدم"
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
                        تحميلات الصور
                    </h3>
                </div>
            </div>
            <form class="kt-form " action="{{route('admin.user_plans.items.export_downloads')}}" method="get">
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
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المستخدم</label>
                                        <select name="user_id"
                                                style="width: 100%;"
                                                data-placeholder="اختر مستخدم"
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
                        تحميلات الفيديو
                    </h3>
                </div>
            </div>
            <form class="kt-form " action="{{route('admin.videos.user_plans.items.export_downloads')}}" method="get">
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
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المستخدم</label>
                                        <select name="user_id"
                                                style="width: 100%;"
                                                data-placeholder="اختر مستخدم"
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
                        تحميلات الفيكتور
                    </h3>
                </div>
            </div>
            <form class="kt-form " action="{{route('admin.vector.user_plans.items.export_downloads')}}" method="get">
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
                                <div class="col-md-3 kt-margin-b-20-tablet-and-mobile">
                                    <div class="form-group">
                                        <label>المستخدم</label>
                                        <select name="user_id"
                                                style="width: 100%;"
                                                data-placeholder="اختر مستخدم"
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
        $('[name="user_id"]').select2({
            language: "ar",
            placeholder: '{{ __('views.Choose Users') }}',
            ajax: {
                url: '{{ route('admin.members.ajax') }}',
                dataType: 'json',
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
    </script>
    <style>
        .select2-ajax{
            width: 200px;
        }
    </style>
@endpush
