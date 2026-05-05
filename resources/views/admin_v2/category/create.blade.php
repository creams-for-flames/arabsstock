@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--tabs">
        <div class="kt-portlet__body">
            <form method="post" action="{{$store_url}}" enctype="multipart/form-data">
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
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Name')}} {{__('views.- Arabic Version')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('name_ar')}}" name="name_ar" class="form-control" placeholder="{{ trans('views.Name') }}">
                                                    <div class="invalid-feedback">{{$errors->first('name_ar')}}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Name')}} {{__('views.- English Version')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('name_en')}}" name="name_en" class="form-control" placeholder="{{ trans('views.Name') }}">
                                                    <div class="invalid-feedback">{{$errors->first('name_en')}}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Slug')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input type="text" value="{{old('slug')}}" name="slug" class="form-control" placeholder="{{ trans('views.Slug') }}">
                                                    <div class="invalid-feedback">{{$errors->first('slug')}}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Status')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="mode">
                                                        <option {{old('mode') === 'on' ? 'selected' : ''}} value="on">{{__('views.Active')}}</option>
                                                        <option {{old('mode') === 'off' ? 'selected' : ''}} value="off">{{__('views.Inactive')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('mode')}}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.cities_and_landmarks')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <select class="form-control" name="cities_and_landmarks">
                                                        <option {{old('cities_and_landmarks') === 'on' ? 'selected' : ''}} value="on">{{__('views.Active')}}</option>
                                                        <option {{old('cities_and_landmarks') === 'off' ? 'selected' : ''}} value="off">{{__('views.Inactive')}}</option>
                                                    </select>
                                                    <div class="invalid-feedback">{{$errors->first('cities_and_landmarks')}}</div>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.people')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select class="form-control" name="people">
                                                            <option {{old('people') == 1 ? 'selected' : ''}} value="on">{{__('views.Active')}}</option>
                                                            <option {{old('people') == 0 ? 'selected' : ''}} value="off">{{__('views.Inactive')}}</option>
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('people')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (isset($is_videos_site))
                                                <div class="form-group row">
                                                    <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.in_random_home_video')}}</label>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group validated">
                                                            <select class="form-control" name="in_random_home_video">
                                                                <option {{old('in_random_home_video') === '0' ? 'selected' : ''}} value="0">{{__('views.Inactive')}}</option>

                                                                <option {{old('in_random_home_video') === '1' ? 'selected' : ''}} value="1">{{__('views.Active')}}</option>
                                                            </select>
                                                            <div class="invalid-feedback">{{$errors->first('in_random_home_video')}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @elseif(isset($is_vectors_site))
                                                <div class="form-group row">
                                                    <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.in_random_home_vector')}}</label>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group validated">
                                                            <select class="form-control" name="in_random_home_vector">
                                                                <option {{old('in_random_home_vector') === '0' ? 'selected' : ''}} value="0">{{__('views.Inactive')}}</option>

                                                                <option {{old('in_random_home_vector') === '1' ? 'selected' : ''}} value="1">{{__('views.Active')}}</option>
                                                            </select>
                                                            <div class="invalid-feedback">{{$errors->first('in_random_home_vector')}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="form-group row">
                                                    <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.in_random_home_image')}}</label>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group validated">
                                                            <select class="form-control" name="in_random_home_image">
                                                                <option {{old('in_random_home_image') === '0' ? 'selected' : ''}} value="0">{{__('views.Inactive')}}</option>

                                                                <option {{old('in_random_home_image') === '1' ? 'selected' : ''}} value="1">{{__('views.Active')}}</option>
                                                            </select>
                                                            <div class="invalid-feedback">{{$errors->first('in_random_home_image')}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="form-group row">
                                                    <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.sort')}}</label>
                                                    <div class="col-lg-9 col-xl-6">
                                                        <div class="input-group validated">
                                                            <input type="number" value="{{old('sort')}}" name="sort" class="form-control" placeholder="{{ trans('views.sort') }}">
                                                            <div class="invalid-feedback">{{$errors->first('sort')}}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.Thumbnail')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="kt-avatar" id="kt_user_avatar_1">
                                                    <div class="kt-avatar__holder"></div>
                                                    <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                                        <i class="fa fa-pen"></i>
                                                        <input type="file" name="thumbnail" accept=".png, .jpg, .jpeg" >
                                                    </label>
                                                    <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                                </div>
                                                <span class="form-text text-muted">{{__('views.Allowed file types: png, jpg, jpeg.')}}</span>
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
<script>
KTUtil.ready(function() {
    new KTAvatar('kt_user_avatar_1')
});
</script>
@endpush
