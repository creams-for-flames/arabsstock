@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title"> إضافة تجميعة </h3>
        </div>
    </div>
    <!--begin::Form-->
    <form class="kt-form kt-form--label-right" method="post" action="{{{$store_url}}}" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}">

        @include('errors.errors-forms')
        <div class="kt-portlet__body">
            <div class="form-group row">
                <div class="col-lg-10">
                    <label>عنوان التجميعة :</label>
                    <input type="text" value="{{{ old('title') }}}" name="title" class="form-control" placeholder="{{{ trans('admin.title') }}}">
                </div>

            </div>

            <div class="form-group row">
                <div class="col-lg-10">
                    <label>ايميل المستخدم :</label>
                    <input type="text" value="{{{ old('email') }}}" name="email" class="form-control" placeholder="ايميل المستخدم">
                </div>

            </div>
            <div class="form-group row">
                <div class="col-lg-10">
                    <label>اسم المستخدم :</label>
                    <input type="text" value="{{{ old('username') }}}" name="username" class="form-control" placeholder="اسم المستخدم">
                </div>

            </div>
            <div class="form-group row">
                <div class="col-lg-10">
                    <label>كلمة المرور :</label>
                    <input type="password" value="{{{ old('password') }}}" name="password" class="form-control" placeholder="كلمة المرور">
                </div>

            </div>
            <div class="form-group row">
                <div class="col-lg-10">
                    <label>تاكيد كلمة المرور :</label>
                    <input type="password" value="{{{ old('password_confirmation') }}}" name="password_confirmation" class="form-control" placeholder="تاكيد كلمة المرور">
                </div>

            </div>
            <div class="form-group row">
                <div class="col-lg-10">
                    <label >{{__('views.in_random_home_image')}}</label>

                    <select class="form-control" name="in_random_home">
                            <option {{old('in_random_home') === '0' ? 'selected' : ''}} value="0">{{__('views.Inactive')}}</option>

                            <option {{old('in_random_home') === '1' ? 'selected' : ''}} value="1">{{__('views.Active')}}</option>
                        </select>
                        <div class="invalid-feedback">{{$errors->first('in_random_home')}}</div>

                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-10">
                    <label>الوصف</label>
                    <textarea class="form-control" name="description" id="" cols="10" rows="10"></textarea>

                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <label>الحالة</label>
                    <div class="kt-radio-list">
                        <label class="kt-radio kt-radio--success">
                            <input type="radio" name="status" value="1" checked>
                            <span></span> {{{ trans('admin.active') }}}
                        </label>
                    </div>
                    <label class="kt-radio kt-radio--success">
                        <input type="radio" name="status" value="0">
                        <span></span> {{{ trans('admin.disabled') }}}
                    </label>
                </div>
            </div>

        </div>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <div class="row">
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-success">{{ trans('admin.save') }}</button>&nbsp;
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--end::Form-->
</div>

</div>
<!-- end:: Content -->
@endsection


