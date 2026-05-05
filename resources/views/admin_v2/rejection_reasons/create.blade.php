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
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Title')}} </label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('title')}}" name="title" class="form-control" placeholder="{{__('views.Title')}}">
                                                    <div class="invalid-feedback">{{$errors->first('title')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Description')}} {{__('views.- Arabic Version')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <textarea name="description_ar" id="description_ar" cols="30" rows="3" class="form-control" placeholder="{{__('views.Description')}}" >{{old('description_en')}}</textarea>
                                                    <div class="invalid-feedback">{{$errors->first('description_ar')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Description')}} {{__('views.- English Version')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <textarea name="description_en" id="description_en" cols="30" rows="3" class="form-control" placeholder="{{__('views.Description')}}" >{{old('description_en')}}</textarea>
                                                    <div class="invalid-feedback">{{$errors->first('description_en')}}</div>
                                                </div>
                                            </div>
                                        </div>


  
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Status')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="status">
                                                        <option {{old('status') === 'active' ? 'selected' : ''}} value="active">{{__('views.Active')}}</option>
                                                        <option {{old('status') === 'inactive' ? 'selected' : ''}} value="inactive">{{__('views.Inactive')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('status')}}</div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Type')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="type">
                                                        <option {{old('type') === 'temporarily' ? 'selected' : ''}} value="temporarily">{{__('admin.temporarily')}}</option>
                                                        <option {{old('type') === 'permanently' ? 'selected' : ''}} value="permanently">{{__('admin.permanently')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('type')}}</div>
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
