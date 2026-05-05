@php
    $html_breadcrumbs = [
                'title' => __('views.Promocodes'),
                'subtitle' => __('views.New'),
            ];
@endphp
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
                                <form method="post" action="{{ route('admin.promocodes.store') }}">
                                    @csrf
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                                            <div class="kt-form kt-form--label-right">
                                                <div class="kt-form__body">
                                                    <div class="kt-section kt-section--first">
                                                        <div class="kt-section__body">
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
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">{{ __('admin.title_ar') }}</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <input type="text" value="{{ old('title_en') }}"
                                                                               name="title_ar" class="form-control"
                                                                               placeholder="{{ trans('admin.title_ar') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">{{ __('admin.title_en') }}</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <input type="text" value="{{ old('title_en') }}"
                                                                               name="title_en" class="form-control"
                                                                               placeholder="{{ trans('admin.title_en') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">الكود</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <input type="text" value="{{ old('code') }}"
                                                                               name="code" class="form-control"
                                                                               placeholder="مثال: eid2022">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">نوع
                                                                    الخصم</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <select name="type" id="type" class="form-control">
                                                                        <option
                                                                            value="percent" {{ old('type')=='percent'?'selected':'' }}>
                                                                            نسبة
                                                                        </option>
                                                                        <option
                                                                            value="amount" {{ old('type')=='amount'?'selected':'' }}>
                                                                            مبلغ محدد
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label id="value_label"
                                                                       class="col-xl-3 col-lg-3 col-form-label"
                                                                       for="value">النسبة</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <input type="number" class="form-control"
                                                                           name="value"
                                                                           value="{{old('value')}}" required min="0"/>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">عدد مرات
                                                                    الاستخدام</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <input type="number" min="1"
                                                                               value="{{ old('max_usage',1) }}"
                                                                               name="max_usage" class="form-control">
                                                                    </div>
                                                                    <span class="form-text text-muted">كم مرة يمكن للمشترك استخدام الكود</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">الحد الأقصى
                                                                    للمستفيدين</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <input type="number" class="form-control"
                                                                           name="max_users"
                                                                           value="0" min="0"/>
                                                                    <span class="form-text text-muted">لكم اشتراك يمكن الاستفادة من الكود</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">الحالة</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <select name="status" id="status"
                                                                            class="form-control">
                                                                        <option value="0">معطل</option>
                                                                        <option
                                                                            value="1" {{ old('status',1)?'selected':'' }}>
                                                                            فعال
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">تاريخ
                                                                    الانتهاء</label>
                                                                <div class="col-lg-9 col-xl-8">
                                                                    <div class="input-group">
                                                                        <input type="text" id="kt_datepicker_1" min="1"
                                                                               data-date-format="yyyy-mm-dd"
                                                                               value="{{ old('expired_at') }}"
                                                                               name="expired_at" class="form-control"
                                                                               required
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-xl-3 col-lg-3 col-form-label">الباقات</label>
                                                                <div class="col-lg-9 col-xl-8 ">
                                                                    <label
                                                                        class="kt-checkbox kt-checkbox--success">
                                                                        <input type="checkbox" id="checkAll"
                                                                               checked="">تحديد الكل
                                                                        <span></span>
                                                                    </label>
                                                                    <hr class="mt-2 mb-3">
                                                                    <div class="row plans">
                                                                        @foreach(\App\Models\Plan::where('status',1)->orderBy('type','desc')->orderBy('credits_count')->get() as $plan)
                                                                            <div class="col-lg-2">
                                                                                <label
                                                                                    class="kt-checkbox kt-checkbox--success">
                                                                                    <input type="checkbox"
                                                                                           name="plans[]"
                                                                                           value="{{ $plan->id }}"
                                                                                           checked=""> {!! $plan->title !!}
                                                                                    <span></span>
                                                                                </label>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
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
                                                            <a class="btn btn-clean btn-bold"
                                                               href="{{ route('admin.promocodes.index') }}">إلغاء</a>
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

@push('css')
@endpush
@push('scripts')
    <script>
        $('#type').on('change', function () {
            _val = $(this).val();
            if (_val == 'percent') {
                $('#value_label').text('النسبة')
            } else {
                $('#value_label').text('قيمة الخصم')
            }
        });
        $('#checkAll').on('change', function () {
            if ($('#checkAll').is(':checked')) {
                $('.plans input').attr('checked', !0)
            } else {
                $('.plans input').attr('checked', !1)
            }
        })
    </script>
@endpush
