@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-portlet kt-portlet--tabs">
            <div class="kt-portlet__body">
                <form method="post" action="{{$update_url}}" enctype="multipart/form-data">
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
                                                    <div class="alert-icon"><i class="fa fa-exclamation-triangle"></i>
                                                    </div>
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
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.tags_ar_images_in_home')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select multiple name="tags_images_ar[]" id="tags_images_ar" class="form-control">
                                                            @if($image_ar_tags)
                                                                @foreach($image_ar_tags as $key=>$value)
                                                                    <option selected value="{{$value}}">{{$value}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('tags_images_ar')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.tags_en_images_in_home')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select multiple name="tags_images_en[]" id="tags_images_en" class="form-control">
                                                            @if($image_en_tags)
                                                                @foreach($image_en_tags as $key=>$value)
                                                                    <option selected value="{{$value}}">{{$value}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('tags_images_en')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.tags_ar_videos_in_home')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select multiple name="tags_videos_ar[]" id="tags_videos_ar" class="form-control">
                                                            @if($video_ar_tags)
                                                                @foreach($video_ar_tags as $key=>$value)
                                                                    <option selected value="{{$value}}">{{$value}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('tags_videos_ar')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.tags_en_videos_in_home')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select multiple name="tags_videos_en[]" id="tags_videos_en" class="form-control">
                                                            @if($video_en_tags)
                                                                @foreach($video_en_tags as $key=>$value)
                                                                    <option selected value="{{$value}}">{{$value}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('tags_videos_en')}}</div>
                                                    </div>
                                                </div>
                                            </div>



                                              <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.tags_ar_vectors_in_home')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select multiple name="tags_vectors_ar[]" id="tags_vectors_ar" class="form-control">
                                                            @if($vector_ar_tags)
                                                                @foreach($vector_ar_tags as $key=>$value)
                                                                    <option selected value="{{$value}}">{{$value}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('tags_vectors_ar')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.tags_en_vectors_in_home')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <select multiple name="tags_vectors_en[]" id="tags_vectors_en" class="form-control">
                                                            @if($vector_en_tags)
                                                                @foreach($vector_en_tags as $key=>$value)
                                                                    <option selected value="{{$value}}">{{$value}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="invalid-feedback">{{$errors->first('tags_vectors_en')}}</div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.4KPrice')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <input onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" value="{{$video_tags->four_k_price}}" name="four_k_price" class="form-control" placeholder="{{__('views.4KPrice')}}">

                                                        <div class="invalid-feedback">{{$errors->first('four_k_price')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.FHDPrice')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <input onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" value="{{$video_tags->fhd_price}}" name="fhd_price" class="form-control" placeholder="{{__('views.FHDPrice')}}">

                                                        <div class="invalid-feedback">{{$errors->first('fhd_price')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.HDPrice')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <input onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" value="{{$video_tags->hd_price}}" name="hd_price" class="form-control" placeholder="{{__('views.HDPrice')}}">

                                                        <div class="invalid-feedback">{{$errors->first('hd_price')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-xl-3 col-lg-3 col-form-label">{{__('views.SDPrice')}}</label>
                                                <div class="col-lg-9 col-xl-6">
                                                    <div class="input-group validated">
                                                        <input onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" value="{{$video_tags->sd_price}}" name="sd_price" class="form-control" placeholder="{{__('views.SDPrice')}}">

                                                        <div class="invalid-feedback">{{$errors->first('sd_price')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                              <label class="col-xl-3 col-lg-3 col-form-label">{{__('misc.profit_ratio')}}</label>
                                              <div class="col-lg-9 col-xl-6">
                                                  <div class="input-group validated">
                                                      <input onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" value="{{$image_profit_ratio}}" name="profit_ratio" class="form-control" placeholder="{{__('misc.profit_ratio')}}">

                                                      <div class="invalid-feedback">{{$errors->first('profit_ratio')}}</div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label">{{__('misc.minimum_payout')}}</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="input-group validated">
                                                    <input onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/[^0-9.]/g,'')" type="text" value="{{$image_tags->minimum_payout}}" name="minimum_payout" class="form-control" placeholder="{{__('misc.profit_ratio')}}">

                                                    <div class="invalid-feedback">{{$errors->first('minimum_payout')}}</div>
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
    <script>
      $(document).ready(function () {

        // $('.select2').select2();
        $("#tags_images_ar").select2({

          width: "100%",
          language: "ar",
          minimumInputLength: 3,
          dir: "rtl",
          ajax: {
            url: "{{$select2_tags_images_ar_url}}",
            dataType: 'json',
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page,
              };
            },

            processResults: function (data, params) {
              console.log(data);
              params.page = params.page || 1;

              return {
                results: $.map(data.data, function (item) {

                  return {
                    text: item.tag,
                    id: item.tag,
                  };
                }),
                pagination: {
                  more: (params.page * 30) < data.total,
                },
              };
            },
            cache: true,
          },
          escapeMarkup: function (markup) {
            return markup;
          }, // let our custom formatter work
        });
        $("#tags_images_en").select2({

          width: "100%",
          language: "ar",
          minimumInputLength: 3,
          dir: "rtl",
          ajax: {
            url: "{{$select2_tags_images_en_url}}",
            dataType: 'json',
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page,
              };
            },

            processResults: function (data, params) {
              console.log(data);
              params.page = params.page || 1;

              return {
                results: $.map(data.data, function (item) {

                  return {
                    text: item.tag,
                    id: item.tag,
                  };
                }),
                pagination: {
                  more: (params.page * 30) < data.total,
                },
              };
            },
            cache: true,
          },
          escapeMarkup: function (markup) {
            return markup;
          }, // let our custom formatter work
        });

        $("#tags_videos_ar").select2({

          width: "100%",
          language: "ar",
          minimumInputLength: 3,
          dir: "rtl",
          ajax: {
            url: "{{$select2_tags_videos_ar_url}}",
            dataType: 'json',
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page,
              };
            },

            processResults: function (data, params) {
              console.log(data);
              params.page = params.page || 1;

              return {
                results: $.map(data.data, function (item) {

                  return {
                    text: item.tag,
                    id: item.tag,
                  };
                }),
                pagination: {
                  more: (params.page * 30) < data.total,
                },
              };
            },
            cache: true,
          },
          escapeMarkup: function (markup) {
            return markup;
          }, // let our custom formatter work
        });
        $("#tags_videos_en").select2({

          width: "100%",
          language: "ar",
          minimumInputLength: 3,
          dir: "rtl",
          ajax: {
            url: "{{$select2_tags_videos_en_url}}",
            dataType: 'json',
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page,
              };
            },

            processResults: function (data, params) {
              console.log(data);
              params.page = params.page || 1;

              return {
                results: $.map(data.data, function (item) {

                  return {
                    text: item.tag,
                    id: item.tag,
                  };
                }),
                pagination: {
                  more: (params.page * 30) < data.total,
                },
              };
            },
            cache: true,
          },
          escapeMarkup: function (markup) {
            return markup;
          }, // let our custom formatter work
        });




         $("#tags_vectors_ar").select2({

          width: "100%",
          language: "ar",
          minimumInputLength: 3,
          dir: "rtl",
          ajax: {
            url: "{{$select2_tags_vectors_ar_url}}",
            dataType: 'json',
            data: function (params) {
              return {
                q: params.term, // search term 
                page: params.page,
              };
            },

            processResults: function (data, params) {
              console.log(data);
              params.page = params.page || 1;

              return {
                results: $.map(data.data, function (item) {

                  return {
                    text: item.tag,
                    id: item.tag,
                  };
                }),
                pagination: {
                  more: (params.page * 30) < data.total,
                },
              };
            },
            cache: true,
          },
          escapeMarkup: function (markup) {
            return markup;
          }, // let our custom formatter work
        });
        $("#tags_vectors_en").select2({

          width: "100%",
          language: "ar",
          minimumInputLength: 3,
          dir: "rtl",
          ajax: {
            url: "{{$select2_tags_vectors_en_url}}",
            dataType: 'json',
            data: function (params) {
              return {
                q: params.term, // search term
                page: params.page,
              };
            },

            processResults: function (data, params) {
              console.log(data);
              params.page = params.page || 1;

              return {
                results: $.map(data.data, function (item) {

                  return {
                    text: item.tag,
                    id: item.tag,
                  };
                }),
                pagination: {
                  more: (params.page * 30) < data.total,
                },
              };
            },
            cache: true,
          },
          escapeMarkup: function (markup) {
            return markup;
          }, // let our custom formatter work
        });


      });
    </script>
@endpush
