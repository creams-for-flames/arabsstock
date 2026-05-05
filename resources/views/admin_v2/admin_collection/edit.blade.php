@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title"> تعديل تجميعة </h3>
                </div>
            </div>
            <!--begin::Form-->
            <form class="form-horizontal" method="post" action="{{{$update_url}}}" enctype="multipart/form-data">

                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}">
                <input type="hidden" name="id" value="{{{ $collection->id }}}">

                @include('errors.errors-forms')
                <div class="kt-portlet__body">
                    <div class="form-group row">
                        <div class="col-lg-10">
                            <label>عنوان التجميعة :</label>
                            <input type="text" value="{{$collection->title}}" name="title" class="form-control" placeholder="{{{ trans('admin.title') }}}">
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-lg-10">
                            <label>ايميل المستخدم :</label>
                            <input type="text" value="{{$user->email}}" name="email" class="form-control" placeholder="ايميل المستخدم">
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-lg-10">
                            <label>اسم المستخدم :</label>
                            <input type="text" value="{{$user->username}}" name="username" class="form-control" placeholder="اسم المستخدم">
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
                            <label>{{__('views.in_random_home_image')}}</label>

                            <select class="form-control" name="in_random_home">
                                <option @if( $collection->in_random_home == 1 ) selected @endif value="1">{{__('views.Active')}}</option>

                                <option @if( $collection->in_random_home == 0 ) selected @endif value="0">{{__('views.Inactive')}}</option>

                            </select>
                            <div class="invalid-feedback">{{$errors->first('in_random_home')}}</div>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-10">
                            <label>الوصف</label>
                            <textarea class="form-control" name="description" id="" cols="10" rows="10">{{$collection->description}}</textarea>

                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label>الحالة</label>
                            <div class="kt-radio-list">
                                <label class="kt-radio kt-radio--success">
                                    <input type="radio" name="status" value="1" @if( $collection->status == 1 ) checked @endif>
                                    <span></span> {{{ trans('admin.active') }}}
                                </label>
                            </div>
                            <label class="kt-radio kt-radio--success">
                                <input type="radio" name="status" value="0" @if( $collection->status == 0 ) checked @endif>
                                <span></span> {{{ trans('admin.disabled') }}}
                            </label>
                        </div>
                    </div>

                </div>

                @if($collection->images)
                    <div class="col-xl-6">

                        <!--begin::Portlet-->
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">
                                        صور التجميعة
                                    </h3>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div class="kt-section">
                                    <div class="kt-section__content">


                                        @if (!isset($is_videos_site))
                                            @foreach($collection->images as $imagesItem)

                                                <div id="image_con{{$imagesItem->id}}" style="float: right; padding-bottom: 5px; width: calc(100% / 5);">
                                                    <label onclick="deleteFromCollection({{$imagesItem->id}})" class="control_action" for="">X</label>
                                                    <a href="{{ $imagesItem->post_link }}" class="kt-media kt-media--circle">

                                                        <img src="{{asset('').'/'.$imagesItem->thumbnail}}" alt="image">
                                                    </a>
                                                </div>

                                            @endforeach
                                        @else
                                            @foreach($collection->videos as $item)

                                                <div id="image_con{{$item->id}}" style="float: right; padding-bottom: 5px; width: calc(100% / 5);">
                                                    <label onclick="deleteFromCollection({{$item->id}})" class="control_action" for="">X</label>
                                                    <a href="{{ $item->post_link }}" class="kt-media kt-media--circle">

                                                        <img src="{{asset('').'/'.$item->thumbnail}}" alt="image">
                                                    </a>
                                                </div>

                                            @endforeach
                                        @endif

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!--end::Portlet-->

                        <!--begin::Portlet-->


                        <!--end::Portlet-->
                    </div>

                @endif
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

@push('css')
    <style>
        .kt-media img {
            width: 100%;
            max-width: 250px;
            height: 250px;
        }

        .control_action {
            border: 1px solid #1dc9b7;
            width: 20px;
            height: 20px;
            font-size: 16px;
            text-align: center;
            font-weight: bolder;
            color: #fff;
            background-color: #1dc9b7;
            border-radius: 0;
            position: relative;
            display: block;
            top: 33px;
            right: 0;
            text-align: center;
            line-height: 1.2;
        }

        .control_action:hover {
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #179c8e;
            border: 1px solid #179c8e;
            cursor: pointer;
        }
    </style>
@endpush


@push('scripts')
    <script>

      function deleteFromCollection(id) {
        var url = '{{$destroy_url}}'.replace('0', id);
        ;
        swal.fire({
          title: "ازالة الصورة من المجموعة",
          text: "هل تريد حقا ازالة الصورة من المجموعة",
          type: "warning",
          showLoaderOnConfirm: true,
          showCancelButton: true,
          icon: "warning",
          buttons: true,
          dangerMode: true,
        }).then((willDelete) => {
          if (willDelete.value) {


            $.ajax({
              url: url,
              method: 'post',
              type: 'post',
              data: {
                _token: '{{csrf_token()}}',

              },
            })
              .done(function (data) {

                if (data.status == true) {
                  toastr.success('@lang('common.deleted')');
                  $('#image_con' + id).hide(500);

                } else if (data.status === 'cant_delete') {
                  toastr.warning('@lang('common.cant_deleted')');
                } else {
                  toastr.warning('@lang('common.not_deleted')');
                }

              }).fail(function () {
              toastr.error('@lang('common.something_wrong')');
            });
          } else {

          }
        });
      }
    </script>

@endpush

