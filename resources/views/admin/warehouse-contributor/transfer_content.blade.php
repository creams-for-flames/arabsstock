@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::App-->
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <!--Begin:: App Aside Mobile Toggle-->
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <!--End:: App Aside Mobile Toggle-->
            <!--Begin:: App Content-->
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content" style="margin-left:0;">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet kt-portlet--tabs">
                            <div class="kt-portlet__body">
                                <form method="post" action="{{ route('admin.transfer_content') }}">
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                                            <div class="kt-form kt-form--label-right">
                                                <div class="kt-form__body">
                                                    <div class="kt-section kt-section--first">
                                                        <div class="kt-section__body">
                                                            <div class="row">
                                                                <div class="col-lg-8 mx-auto pt-3 pb-4">
                                                                    <div class="alert alert-warning" role="alert">
                                                                        <strong class="pr-1">تنبيه </strong>
                                                                        سيتم نقل المحتويات التابعة لعربستوك فقط
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($errors->any())
                                                                <div
                                                                    class="alert alert-solid-danger alert-bold fade show kt-margin-t-20 kt-margin-b-40"
                                                                    role="alert">
                                                                    <div class="alert-icon"><i
                                                                            class="fa fa-exclamation-triangle"></i>
                                                                    </div>
                                                                    <div
                                                                        class="alert-text">{{__('views.Oops, something went wrong! Please check the errors below.')}}
                                                                        {!! implode('', $errors->all('<div>:message</div>')) !!}
                                                                    </div>
                                                                    <div class="alert-close">
                                                                        <button type="button" class="close"
                                                                                data-dismiss="alert" aria-label="Close">
                                                                            <span aria-hidden="true"><i
                                                                                    class="la la-close"></i></span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="form-group row ">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">المساهم المراد تحويل الملكية له</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <select name="contributor_id"
                                                                                id="contributor_id" style="width: 100%;"
                                                                                data-placeholder="اختر مساهم"
                                                                        ></select>
                                                                        <div
                                                                            class="invalid-feedback">{{$errors->first('receivers')}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">روابط المحتويات</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <textarea name="contents" class="form-control text-left" cols="30"
                                                                              dir="ltr"
                                                                              rows="10">{{ old('contents') }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="kt-separator kt-separator--space-lg kt-separator--fit kt-separator--border-solid"></div>
                                                <div class="kt-form__actions">
                                                    <div class="row">
                                                        <div class="col-xl-3"></div>
                                                        <div class="col-lg-9 col-xl-8">
                                                            <button class="btn btn-label-brand btn-bold" type="submit">
                                                                حفظ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End::App-->
    </div>
    <!-- end:: Content -->
@endsection
@push('scripts')
    <script>
        $('#contributor_id').select2({
            language: "ar",
            placeholder: '{{ __('views.Choose Users') }}',
            ajax: {
                url: '{{ route('admin.contributors.ajax') }}',
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
    </script>
@endpush
