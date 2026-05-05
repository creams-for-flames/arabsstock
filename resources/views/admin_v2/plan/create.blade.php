@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--tabs">
        <div class="kt-portlet__body">
            <form method="POST" action="{{$store_url}}">
                @csrf
                @method('post')
                <div class="tab-content">
                    <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                        <div class="kt-form kt-form--label-right">
                            <div class="kt-form__body">
                                <div class="kt-section kt-section--first">
                                    <div class="kt-section__body">


                                    



                                        @if ($errors->any())
                                        <div class="alert alert-solid-danger alert-bold fade show kt-margin-t-20 kt-margin-b-40" role="alert">
                                            <div class="alert-icon"><i class="fa fa-exclamation-triangle"></i></div>
                                            <div class="alert-text">{{__('views.Oops, something went wrong! Please check the errors below.')}}
                                            {!! implode('', $errors->all('<div>:message</div>')) !!}
                                            </div>
                                            <div class="alert-close">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true"><i class="la la-close"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Title')}} {{__('views.- Arabic Version')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('title_ar')}}" name="title_ar" class="form-control" placeholder="{{__('views.Title')}}">
                                                    <div class="invalid-feedback">{{$errors->first('title_ar')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Title')}} {{__('views.- English Version')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('title_en')}}" name="title_en" class="form-control" placeholder="{{__('views.Title')}}">
                                                    <div class="invalid-feedback">{{$errors->first('title_en')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Price')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('price')}}" name="price" class="form-control" placeholder="{{ trans('views.Price') }}">
                                                    <div class="invalid-feedback">{{$errors->first('price')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.DownloadsCount')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="number" value="{{old('downloads_count')}}" name="downloads_count" class="form-control" placeholder="{{ trans('views.DownloadsCount') }}">
                                                    <div class="invalid-feedback">{{$errors->first('downloads_count')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Type')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="type">
                                                        <option {{old('type') === 'package' ? 'selected' : ''}} value="package">{{__('views.package')}}</option>
                                                        <option {{old('type') === 'monthly' ? 'selected' : ''}} value="monthly">{{__('views.monthly')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('type')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Status')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="status">
                                                        <option {{old('status') === '1' ? 'selected' : ''}} value="1">{{__('views.Active')}}</option>
                                                        <option {{old('status') === '0' ? 'selected' : ''}} value="0">{{__('views.Inactive')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('status')}}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.free')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="free">
                                                        <option {{old('free') === '0' ? 'selected' : ''}} value="0">{{__('views.Inactive')}}</option>
                                                        <option {{old('free') === '1' ? 'selected' : ''}} value="1">{{__('views.Active')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('free')}}</div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>

                            <div class="kt-separator kt-separator--space-lg kt-separator--fit kt-separator--border-solid"></div>

                            <div class="kt-form__actions">
                                <div class="row">
                                    <div class="col-xl-3"></div>
                                    <div class="col-lg-9 col-xl-6">
                                        <button class="btn btn-label-brand btn-bold" type="submit">{{__('views.Save Changes')}}</button>
                                        <a class="btn btn-clean btn-bold" href="{{$index_url}}">{{__('views.Cancel')}}</a>
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
<!-- end:: Content -->
@endsection

@push('scripts')
@endpush
