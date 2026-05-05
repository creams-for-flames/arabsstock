@extends('app')

@section('css')
    <link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css"/>
    <style>
        .pagination {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .pagination li {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            margin-right: .5rem;
            border-radius: 4px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple, .select2-container--default.select2-container--focus .select2-selection--single, .select2-container--default.select2-container--open .select2-selection--multiple, .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1dc9b7;
        }

        .pagination li a {
            color: #5867dd;
            text-decoration: none;
            background-color: transparent;
        }

        .img-coneiners {


        }
        textarea.form-control {
    height: auto;
    padding: 10px;
}
        form#idForm .box-body .select2-container {
            width: 100% !important;
        }

        .title-multi-img h1 {
            margin: 0;
            padding: 0;
            font-size: 1.2rem;
            font-weight: 500;
            color: #48465b;
            height: 21px;
        }

        .kt-portlet.active-check {
    border: 2px solid #efeff5;
    margin-bottom: 16px;
}

        .img-coneiners {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .img-coneiners img {
            margin: 0 4px 10px;
            height: 40px;
            object-fit: cover;
        }
    </style>
    <style>
        ul.pagination li {
            margin: 0 5px;
        }
    </style>
    <link href="{{ asset('css/style.bundle.rtl.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')

    <div class="mg-t-2 container">
        <div class="row">
        <div class="col-lg-8 order-lg-3 order-xl-1">


            <!--begin:: Widgets/Notifications-->
            <div class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label row">

                        <div id="" class="select-div">
                            <div class="m-form__group m-form__group--inline">
                                <div class="m-form__label lebel-select-div">
                                    <label>{{__('الفلتر')}}:</label>
                                </div>
                                <div class="m-form__control">

                                    <select style="opacity: 1 !important;" class="form-control m-bootstrap-select" id="m_form_status">
                                        <option value="0">الكل</option>
                                        <option @if(isset($_GET['type']) && $_GET['type']=='complete') selected @endif value="complete">مكتمل التعديلات</option>
                                        <option @if(isset($_GET['type']) && $_GET['type']=='half_edit') selected @endif value="half_edit">تعديلات جزئية</option>
                                        <option @if(isset($_GET['type']) && $_GET['type']=='no_edit') selected @endif value="no_edit">غير معدل</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="imgconeiners2" style="padding: 23px 20px 0px;display:none" class="img-coneiners2">
                            <h5><i class="fa fa-exclamation-triangle" style="
    color: #ffb822;
"></i> لديك قيم مختلفة في حالة الحفظ سيتم فقد بياناتك القديمة</h5>
                        </div>
                        <div class="img-coneiners col-12">

                        </div>
                        <!-- <ul class="nav nav-pills nav-pills-sm nav-pills-label nav-pills-bold" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#kt_widget6_tab1_content" role="tab">
                                    مصنف
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#kt_widget6_tab2_content" role="tab">
                                    قيد الانتظار
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#kt_widget6_tab3_content" role="tab">
                                    قيد المراجعة
                                </a>
                            </li>
                        </ul> -->
                    </div>

                </div>
                <div class="kt-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="kt_widget6_tab1_content" aria-expanded="true">
                            <div class="row">
                                <form action="" id="" class="kt-form form-select-img">
                                    <div class="form-group row">
                                        @foreach($images as $imgItem)

                                            <div id="img-cont" class="col-lg-4">
                                                <label id="checkCont{{$imgItem->id}}" class="kt-checkbox kt-checkbox--success checkCont">
                                                    <input onchange="showOpration('{{$imgItem->id}}','{{url($imgItem->thumbnail)}}','  {{$imgItem->original_name}}')" id="img_checker{{$imgItem->id}}" type="checkbox">
                                                    <span></span>
                                                    <div id="animateHover{{$imgItem->id}}" class="kt-portlet kt-iconbox kt-iconbox--success kt-iconbox--animate-slow">
                                                        <div class="kt-portlet__body-custom">
                                                            <div class="kt-iconbox__body">
                                                                <div class="kt-iconbox__icon">
                                                                    <img class="img-check img-fluid" src="{{url($imgItem->thumbnail)}}" alt="">
                                                                </div>
                                                                <div class="kt-iconbox__desc">
                                                                    <h3 class="kt-iconbox__title">
                                                                        {{substr($imgItem->original_name, 0, 4)}}
                                                                    </h3>
                                                                    <h3 id="wirte_name{{$imgItem->id}}" class="kt-iconbox__title img_title">
                                                                        {{$imgItem->title}}
                                                                    </h3>
                                                                    <div class="kt-iconbox__content">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach

                                    </div>
                                </form>

                            </div>
                            <div class="kt-pagination kt-pagination--sm kt-pagination--success">


                                <?php
                                // config
                                $link_limit = 7; // maximum number of links (a little bit inaccurate, but will be ok for now)
                                ?>
                                @if ($images->lastPage() > 1)
                                    <ul class="kt-pagination__links">
                                        <li class="kt-pagination__link--first {{ ($images->currentPage() == 1) ? ' disabled' : '' }}">
                                            <a href="{{ $images->url(1) }}">
                                                <i class="fa fa-angle-double-right kt-font-brand">

                                                </i></a>
                                        </li>


                                        @for ($i = 1; $i <= $images->lastPage(); $i++)
                                            <?php
                                            $half_total_links = floor($link_limit / 2);
                                            $from = $images->currentPage() - $half_total_links;
                                            $to = $images->currentPage() + $half_total_links;
                                            if ($images->currentPage() < $half_total_links) {
                                                $to += $half_total_links - $images->currentPage();
                                            }
                                            if ($images->lastPage() - $images->currentPage() < $half_total_links) {
                                                $from -= $half_total_links - ($images->lastPage() - $images->currentPage()) - 1;
                                            }
                                            ?>
                                            @if ($from < $i && $i < $to)
                                                <li class="{{ ($images->currentPage() == $i) ? ' kt-pagination__link--active' : '' }}">
                                                    <a href="{{ $images->url($i) }}@if(isset($_GET['type']))&type=<?php echo $_GET['type'] ?> @endif">{{ $i }}</a>
                                                </li>
                                            @endif
                                        @endfor
                                        <li class="kt-pagination__link--last {{ ($images->currentPage() == $images->lastPage()) ? ' disabled' : '' }}">
                                            <a href="{{ $images->url($images->lastPage()) }}"><i class="fa fa-angle-double-left kt-font-brand"></i></a>
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!--end:: Widgets/Notifications-->
        </div>
        <div class="col-xl-4 col-lg-4 order-lg-3 order-xl-1">

            <!--begin:: Widgets/Support Tickets -->
            <div id="opration_controal" style="display: none" class="kt-portlet kt-portlet--height-fluid">
                <div class="kt-portlet__head">
                    <div id="store-img" class="kt-portlet__head-label kt-widget4 store-img title-multi-img">

                    </div>

                    <div class="kt-portlet__head-toolbar">
                        <ul class="nav nav-pills nav-pills-sm nav-pills-label nav-pills-bold" role="tablist">
                            <li class="nav-item">
                                <span id="countImages" class="nav-link active" aria-selected="true" style="">0 صورة</span>
                            </li>

                        </ul>
                    </div>

                </div>
                <div class="kt-portlet__body">
                    <!--begin::Portlet-->
                    <div class="kt-portlet-custom">

                        <!--begin::Form-->
                        <form id="idForm" class="form-horizontal" method="POST" action="{{ url('panel/admin/images/store/update') }}" enctype="multipart/form-data">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="ids" id="ids">

                        @include('errors.errors-forms')

                        <!-- Start Box Body -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('admin.title_ar') }}</label>
                                    <div class="">
                                        <input onkeyup="copyName()" onkeypress="copyName()" type="text" value="" id="title_ar" name="title_ar" class="form-control" placeholder="{{ trans('admin.title_ar') }}">
                                    </div>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('admin.title_en') }}</label>
                                    <div class="">
                                        <input id="title_en" type="text" value="" name="title_en" class="form-control" placeholder="{{ trans('admin.title_en') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Start Box Body -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('misc.tags_ar') }}</label>
                                    <div class="">
                                        <select name="tag_ar[]" multiple class="form-control tag_ar">

                                        </select>
                                        <button type="button" onclick="copyTagAr()" class="btn btn-default">نسخ</button>
                                        <button onclick="pastTagAr()" type="button" class="btn btn-default">لصق</button>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-body">

                                <div class="form-group">

                                    <label class="control-label">{{ trans('misc.tags_en') }}</label>
                                    <div class="">
                                        <select name="tag_en[]" multiple class="form-control tag_en">

                                        </select>
                                        <button type="button" onclick="copyTagEn()" class="btn btn-default">نسخ</button>
                                        <button onclick="pastTagEn()" type="button" class="btn btn-default">لصق</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Start Box Body -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('misc.category') }}</label>
                                    <div class="">
                                        <select id="categories_id" multiple name="categories_id[]" class="form-control categories">

                                            @foreach(  App\Models\ImageCategory::where('mode','on')->orderBy('name_en')->get() as $category )
                                                <option value="{{$category->id}}">
                                                    @if(App::isLocale('en'))
                                                        {{ $category->name_en }}
                                                    @else
                                                        {{ $category->name_ar }}
                                                    @endif
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->


                            <!-- Start Box Body -->
                            <div style="display: none" class="box-body">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('misc.how_use_image') }}</label>
                                    <div class="">
                                        <select name="how_use_image" class="form-control">
                                            <option value="free">{{ trans('misc.use_free') }}</option>
                                            <option value="free_personal">{{ trans('misc.use_free_personal') }}</option>
                                            <option value="editorial_only">{{ trans('misc.use_editorial_only') }}</option>
                                            <option value="web_only">{{ trans('misc.use_web_only') }}</option>

                                        </select>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->


                            <!-- Start Box Body -->
                            <div class="box-body">
                                <div class="form-group">
                                    <label class=" control-label">{{ trans('admin.description_ar') }} ({{ trans('misc.optional') }})</label>
                                    <div class="">

                                        <textarea name="description_ar" rows="4" id="description_ar" class="form-control" placeholder="{{ trans('admin.description_ar') }}"></textarea>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-body">
                                <div class="form-group">
                                    <label class=" control-label">{{ trans('admin.description_en') }} ({{ trans('misc.optional') }})</label>
                                    <div class="">

                                        <textarea name="description_en" rows="4" id="description_en" class="form-control" placeholder="{{ trans('admin.description_en') }}"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Start Box Body -->
                            <div style="display: none" class="box-body">
                                <div class="form-group">
                                    <label class=" control-label">{{ trans('misc.featured')  }}</label>
                                    <div class="">

                                        <div class="radio">
                                            <label class="padding-zero">
                                                <input type="radio" name="featured" value="yes" checked>
                                                {{ trans('misc.yes')  }}
                                            </label>
                                        </div>

                                        <div class="radio">
                                            <label class="padding-zero">
                                                <input type="radio" name="featured" value="no">
                                                {{ trans('misc.no')  }}
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div><!-- /.box-body -->


                            <!-- Start Box Body -->
                            <div style="display: none" class="box-body">
                                <div class="form-group">
                                    <label class="control-label">{{ trans('misc.attribution_required')  }}</label>
                                    <div class="">

                                        <div class="radio">
                                            <label class="padding-zero">
                                                <input type="radio" name="attribution_required" value="yes" checked>
                                                {{ trans('misc.yes')  }}
                                            </label>
                                        </div>

                                        <div class="radio">
                                            <label class="padding-zero">
                                                <input type="radio" name="attribution_required" value="no">
                                                {{ trans('misc.no')  }}
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div><!-- /.box-body -->


                            <!-- Start Box Body -->
                            <div style="display: none" class="box-body">
                                <div class="form-group">
                                    <label class=" control-label">{{ trans('admin.status') }}</label>
                                    <div class="">
                                        <select name="status" class="form-control">
                                            <option value="active">{{ trans('admin.active') }}</option>
                                            <option value="pending">{{ trans('admin.pending') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->

                            <div style="display: none" class="box-body">
                                <div class="form-group">
                                    <label class=" control-label">{{{ trans('admin.in_home') }}}</label>
                                    <div class="">

                                        <div class="radio">
                                            <label class="padding-zero">
                                                <input checked type="radio" name="in_home" value="1">
                                                {{{ trans('admin.active') }}}
                                            </label>
                                        </div>

                                        <div class="radio">
                                            <label class="padding-zero">
                                                <input type="radio" name="in_home" value="0">
                                                {{{ trans('admin.disabled') }}}
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <a href="{{ url('panel/admin/images') }}" class="btn btn-default">{{ trans('admin.cancel') }}</a>
                                <button type="submit" class="btn btn-success pull-right">{{ trans('admin.save') }}</button>
                            </div><!-- /.box-footer -->
                        </form>


                        <!--end::Form-->
                    </div>

                    <!--end::Portlet-->
                </div>
            </div>

            <!--end:: Widgets/Support Tickets -->
        </div>
        </div>
    </div>



@endsection

@section('javascript')
    <script src="{{ asset('js/cookie@2_src_js.cookie.min.js') }}"></script>
    <!-- icheck -->
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>

    <script type="text/javascript">


      //Flat red color scheme for iCheck
      $('input[type="radio"]').iCheck({
        radioClass: 'iradio_flat-green',
      });
      $('#m_form_status').on('change', function (e) {

        if ($('#m_form_status').val() == 0) {
          $(location).attr('href', '{{url('panel/admin/image_store?type=:type')}}');
        }
        console.log('Selecting: ', $('#m_form_status').val());
        var url = '{{url('panel/admin/image_store?type=:type')}}';
        url = url.replace(':type', $('#m_form_status').val());

        $(location).attr('href', url);
      });
    </script>
    <script>

      $(document).ready(function () {

        /* $('select').change(function () {


         });*/
      });


      var idarray = [];
      var imgarray = [];
      var titlearray = [];
      var titlesArarray = [];
      var titlesEnarray = [];
      var descriptionarArray = [];
      var descriptionenArray = [];
      var tagsArray = [];
      var categoryArray = [];

      var index = 0;


      function add_to_array(array, element) {
        if (array.length === 0) {
          array.push(element);
        }


        /* else {
           for (var iCh = 0; iCh < array.length; iCh++) {
             var name = array[iCh];
             //   console.log(name,'ffff',categories);
             if (array.indexOf(element) === -1) {
               array.push(element);
             }

           }
         }*/
      }

      function add_to_array_id(array, element) {
        if (array.length === 0) {
          array.push(element);
        } else {
          for (var iCh = 0; iCh < array.length; iCh++) {
            var name = array[iCh];
            //   console.log(name,'ffff',categories);
            if (array.indexOf(element) === -1) {
              array.push(element);
            }

          }
        }
      }

      function add_to_array2(array, element) {
        if (array.length === 0) {
          console.log(array.length);
          array.push(element);
        }


        /*  else {
            for (var iCh = 0; iCh < array.length; iCh++) {
              var name = array[iCh];
              //   console.log(name,'ffff',categories);
              if (!arraysEqual(name, element)) {

                array.push(element);
              }

            }
          }*/
      }


      function allEqual(arr) {
        if (!arr.length) return true;
        return arr.reduce(function (a, b) {
          return (a === b) ? a : (!b);
        }) === arr[0];
      }

      function allEqualTag(arr) {
        if (!arr.length) return true;
        return arr.reduce(function (a, b) {
          return (a === b) ? a : (!b);
        }) === arr[0];
      }


      function arraysEqual(arr1, arr2) {
        if (arr1.length !== arr2.length)
          return false;
        for (var i = arr1.length; i--;) {

          if (arr1[i].tag !== arr2[i].tag)
            return false;
        }

        return true;
      }

      function arraysEqual2(arr1, arr2) {
        if (arr1.length !== arr2.length)
          return false;
        for (var i = arr1.length; i--;) {

          if (arr1[i].name !== arr2[i].name)
            return false;
        }

        return true;
      }

      function showOpration(id, img, title) {


        // console.log(categoryArray);

        //  categoryArray=[];

        $('#ids').val(id);
        var idsString = '';
        var string2 = '';

        if ($(".checkCont input:checkbox:checked").length > 0) {


          $('#countImages').text($(".checkCont input:checkbox:checked").length + ' ' + 'صورة');

          $('#opration_controal').show();

          if ($('#img_checker' + id).is(":checked")) {


            add_to_array_id(idarray, id);
            add_to_array(imgarray, img);
            add_to_array(titlearray, title);


            string2 += ' <img width="50px" src="' + img + '" alt="">\n';
            $('.img-coneiners').append(string2);

            $('#animateHover' + id).addClass("active-check");


          } else {


            for (var i = 0; i < idarray.length; i++) {
              if (idarray[i] === id) {
                idarray.splice(i, 1);
                /* imgarray.splice(i, 1);
                 titlearray.splice(i, 1);
                 titlesArarray.splice(i, 1);
                 titlesEnarray.splice(i, 1);
                 //  tagsArray.splice(i, 1);
                 //   categoryArray.splice(i, 1);
                 descriptionarArray.splice(i, 1);
                 descriptionenArray.splice(i, 1);*/


              }
            }

            $('.img-coneiners').html('');


            string2 = '';
            for (var jj = 0; jj < imgarray.length; jj++) {
              string2 += ' <img width="50px" src="' + imgarray[jj] + '" alt="">\n';


            }
            $('.img-coneiners').append(string2);

          }
          var string = '';

          if ($(".checkCont input:checkbox:checked").length == 1) {
            $('#imgconeiners2').hide();

            $('#categories_id').val(null).trigger('change');
            $('.tag_ar').empty();
            $('.tag_en').empty();
            $('.tag_ar').select2('data', null);
            $('.tag_en').select2('data', null);

            //  $('#categories_id').empty();
            var url = '{{route('admin.checkTagsAr')}}';


            $.ajax({
              type: "POST",
              url: url,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  $('.tag_ar').empty();
                  // $('.tag_en').empty();
                  /* $('.tag_ar').select2('data', null);
                   $('.tag_en').select2('data', null);
                   var newOption3 = new Option('قيم مختلفة', null, true, true);
                   var newOption4 = new Option('قيم مختلفة', null, true, true);

                   $('.tag_ar').append(newOption3).trigger('change');
                   $('.tag_en').append(newOption4).trigger('change');*/
                  checkTag = 1;
                } else {

                  //   $('.tag_ar').empty();
                  //   $('.tag_en').empty();
                  for (var tagCC = 0; tagCC < data.data.length; tagCC++) {


                    var newOption2 = new Option(data.data[tagCC].tag, data.data[tagCC].tag, true, true);
                    if (data.data[tagCC].local === 'ar') {

                      if ($('.tag_ar option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                      } else {
                        $('.tag_ar').append(newOption2).trigger('change');
                      }


                    } else {

                      /*  if ($('.tag_en option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                        } else {
                          $('.tag_en').append(newOption2).trigger('change');
                        }*/
                    }
                  }
                }

              },
            });


            var urlTag = '{{route('admin.checkTagsEn')}}';


            $.ajax({
              type: "POST",
              url: urlTag,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  //  $('.tag_ar').empty();
                  $('.tag_en').empty();
                  /* $('.tag_ar').select2('data', null);
                   $('.tag_en').select2('data', null);
                   var newOption3 = new Option('قيم مختلفة', null, true, true);
                   var newOption4 = new Option('قيم مختلفة', null, true, true);

                   $('.tag_ar').append(newOption3).trigger('change');
                   $('.tag_en').append(newOption4).trigger('change');*/
                  checkTag = 1;
                } else {

                  //   $('.tag_ar').empty();
                  //   $('.tag_en').empty();
                  for (var tagCC = 0; tagCC < data.data.length; tagCC++) {


                    var newOption2 = new Option(data.data[tagCC].tag, data.data[tagCC].tag, true, true);
                    if (data.data[tagCC].local === 'en') {

                      if ($('.tag_en option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                      } else {
                        $('.tag_en').append(newOption2).trigger('change');
                      }


                    } else {

                      /*   if ($('.tag_en option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                         } else {
                           $('.tag_en').append(newOption2).trigger('change');
                         }*/
                    }
                  }
                }

              },
            });


            var url2 = '{{route('admin.checkCategory')}}';


            $.ajax({
              type: "POST",
              url: url2,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  var optionExists = ($("#categories_id option[value=0]").length > 0);
                  if (optionExists == false) {
                    $('#categories_id').val(0).trigger('change');
                    var newOptionCat3 = new Option('قيم مختلفة', 0, true, true);
                    $('#categories_id').append(newOptionCat3).trigger('change');

                  } else {
                    $('#categories_id').val(0);
                    $('#categories_id').select2().trigger('change');
                  }
                } else {


                  var ids_cat = [];
                  for (var catCC = 0; catCC < data.data.length; catCC++) {


                    ids_cat.push(data.data[catCC].id);


                  }

                  $('#categories_id').select2('val', "");
                  $('#categories_id').val(ids_cat).change();
                }

              },
            });


            //   console.log(imgarray, '', titlearray);

            string += ' <div class="kt-widget4__item">\n' +
              '  <div class="kt-widget4__pic kt-widget4__pic--pic">\n' +
              ' <img src="' + imgarray[0] + '" alt="">\n' +
              '  </div>\n' +
              '  <div class="kt-widget4__info">\n' +
              '  <a href="#" class="kt-widget4__username">' + titlearray[0] +
              ' </a>\n' +
              '\n' +
              ' </div>\n' +
              '</div>';

            $('#store-img').html('');
            $('#store-img').append(string);


            var url3 = '{{route('admin.checkTitleAr')}}';


            $.ajax({
              type: "POST",
              url: url3,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {

                } else {

                  $('#title_ar').val(data.data.title_ar);
                }

              },
            });


            var url4 = '{{route('admin.checkTitleEn')}}';


            $.ajax({
              type: "POST",
              url: url4,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {

                } else {

                  $('#title_en').val(data.data.title_en);
                }

              },
            });


            var urlDetAr = '{{route('admin.checkDesAr')}}';


            $.ajax({
              type: "POST",
              url: urlDetAr,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {

                } else {

                  $('#description_ar').text(data.data.description_ar);

                }

              },
            });


            var urlDetEn = '{{route('admin.checkDesEn')}}';


            $.ajax({
              type: "POST",
              url: urlDetEn,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {

                } else {

                  $('#description_en').text(data.data.description_en);
                }

              },
            });


          } else {


            //    $('#categories_id').val(null).trigger('change');
            // $('.tag_ar').val(null).trigger('change');
            //  $('.tag_en').val(null).trigger('change');

            $('#store-img').html('');

            $('#store-img').append('<h1>عدة صور</h1>');


            /* if ((titlesArarray[0] !== title_ar) && ($('#img_checker' + id).is(":checked"))) {
               $('#title_ar').val('قيم مختلفة');
               $('#title_en').val('mix value');
               $('#imgconeiners2').show();

             } else {
               $('#title_ar').val(titlesArarray[0]);
               $('#title_en').val(titlesEnarray[0]);
               $('#imgconeiners2').hide();
             }*/


            var url5 = '{{route('admin.checkTitleAr')}}';


            $.ajax({
              type: "POST",
              url: url5,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  $('#title_ar').val('قيم مختلفة');

                } else {

                  $('#title_ar').val(data.data.title_ar);
                }

              },
            });


            var url6 = '{{route('admin.checkTitleEn')}}';


            $.ajax({
              type: "POST",
              url: url6,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {

                  $('#title_en').val('mix value');

                } else {

                  $('#title_en').val(data.data.title_en);
                }

              },
            });


            /*  if ((descriptionarArray[0] !== description_ar) && ($('#img_checker' + id).is(":checked"))) {
                $('#description_ar').text('قيم مختلفة');
                $('#description_en').text('mix value');
                $('#imgconeiners2').show();
              } else {
                $('#description_ar').text(descriptionarArray[0]);
                $('#description_en').text(descriptionenArray[0]);
                $('#imgconeiners2').hide();
              }*/


            var urlDetAr1 = '{{route('admin.checkDesAr')}}';


            $.ajax({
              type: "POST",
              url: urlDetAr1,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  $('#description_ar').text('قيم مختلفة');

                } else {

                  $('#description_en').text(data.data.description_en);
                }

              },
            });


            var urlDetEn1 = '{{route('admin.checkDesEn')}}';


            $.ajax({
              type: "POST",
              url: urlDetEn1,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  $('#description_en').text('mix value');

                } else {

                  $('#description_en').text(data.data.description_en);
                }

              },
            });

            var url = '{{route('admin.checkTagsAr')}}';


            $.ajax({
              type: "POST",
              url: url,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  $('.tag_ar').empty();
                  //   $('.tag_en').empty();
                  $('.tag_ar').select2('data', null);
                  //  $('.tag_en').select2('data', null);
                  var newOption3 = new Option('قيم مختلفة', null, true, true);
                  //  var newOption4 = new Option('قيم مختلفة', null, true, true);

                  $('.tag_ar').append(newOption3).trigger('change');
                  //  $('.tag_en').append(newOption4).trigger('change');
                  checkTag = 1;
                } else {

                  $('.tag_ar').empty();
                  //  $('.tag_en').empty();
                  for (var tagCC = 0; tagCC < data.data.length; tagCC++) {


                    console.log(data.data);

                    var newOption2 = new Option(data.data[tagCC].tag, data.data[tagCC].tag, true, true);
                    if (data.data[tagCC].local === 'ar') {

                      if ($('.tag_ar option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                      } else {
                        $('.tag_ar').append(newOption2).trigger('change');
                      }


                    } else {

                      /*  if ($('.tag_en option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                        } else {
                          $('.tag_en').append(newOption2).trigger('change');
                        }*/
                    }
                  }
                }

              },
            });


            var urlTagEn = '{{route('admin.checkTagsEn')}}';


            $.ajax({
              type: "POST",
              url: urlTagEn,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  // $('.tag_ar').empty();
                  $('.tag_en').empty();
                  // $('.tag_ar').select2('data', null);
                  $('.tag_en').select2('data', null);
                  // var newOption3 = new Option('قيم مختلفة', null, true, true);
                  var newOption4 = new Option('قيم مختلفة', null, true, true);

                  // $('.tag_ar').append(newOption3).trigger('change');
                  $('.tag_en').append(newOption4).trigger('change');
                  checkTag = 1;
                } else {

                  // $('.tag_ar').empty();
                  $('.tag_en').empty();
                  for (var tagCC = 0; tagCC < data.data.length; tagCC++) {


                    console.log(data.data);

                    var newOption2 = new Option(data.data[tagCC].tag, data.data[tagCC].tag, true, true);
                    if (data.data[tagCC].local === 'en') {

                      if ($('.tag_en option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                      } else {
                        $('.tag_en').append(newOption2).trigger('change');
                      }


                    } else {

                      /*  if ($('.tag_en option[value="' + data.data[tagCC].tag + '"]:selected').length > 1) {
                        } else {
                          $('.tag_en').append(newOption2).trigger('change');
                        }*/
                    }
                  }
                }

              },
            });

            var url = '{{route('admin.checkCategory')}}';


            $.ajax({
              type: "POST",
              url: url,
              headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}',
              },
              data: {ids: idarray},
              success: function (data) {
                if (data.status === false) {
                  var optionExists = ($("#categories_id option[value=0]").length > 0);
                  if (optionExists == false) {
                    $('#categories_id').val(0).trigger('change');
                    var newOptionCat3 = new Option('قيم مختلفة', 0, true, true);
                    $('#categories_id').append(newOptionCat3).trigger('change');

                  } else {
                    $('#categories_id').val(0);
                    $('#categories_id').select2().trigger('change');
                  }
                } else {

                  var ids_cat = [];
                  for (var catCC = 0; catCC < data.data.length; catCC++) {


                    ids_cat.push(data.data[catCC].id);


                  }

                  $('#categories_id').select2('val', "");
                  $('#categories_id').val(ids_cat).change();
                }

              },
            });


            for (var j = 0; j < idarray.length; j++) {
              idsString += idarray[j] + ',';
            }
            $('#ids').val(idsString);
          }
        } else {


          $('#animateHover' + id).removeClass("active-check");
          idarray = [];
          imgarray = [];
          titlearray = [];
          $('#opration_controal').hide();
          $('.img-coneiners').html('');

          idarray = [];
          imgarray = [];
          titlearray = [];
          titlesArarray = [];
          titlesEnarray = [];
          descriptionarArray = [];
          descriptionenArray = [];
          tagsArray = [];
          categoryArray = [];
        }
      }

      var data = {
        id: 1,
        text: 'Barn owl',
      };


      $(".tag_en").select2({
        tags: true,
      });
      $(".tag_ar").select2({
        tags: true,
      });
      $(".categories").select2({});

      $("#idForm").submit(function (e) {
        /*
        var fieldInput = $('#fieldName');
        var fldLength= fieldInput.val().length;
        fieldInput.focus();
        fieldInput[0].setSelectionRange(fldLength, fldLength);
         */

        if ($('#title_ar').val() === 'قيم مختلفة' || $('#title_ar').val() === 'mix value') {
          var fieldInput = $('#title_ar');
          var fldLength = fieldInput.val().length;
          fieldInput.focus();
          fieldInput[0].setSelectionRange(fldLength, fldLength);
          swal('العنوان العربي يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }


        if ($('#title_en').val() === 'قيم مختلفة' || $('#title_en').val() === 'mix value') {
          $('#title_en').focus();
          swal('العنوان الانجليزي يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }


        if ($('#tag_ar').val() === 'قيم مختلفة' || $('#tag_ar').val() === 'mix value') {
          swal('الوسم العربي يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }
        if ($('#tag_en').val() === 'قيم مختلفة' || $('#tag_en').val() === 'mix value') {
          swal('الوسم الانجليزي يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }


        if ($('#categories_id').val() === 'قيم مختلفة' || $('#categories_id').val() === 'mix value') {
          swal('التصنيف يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }


        if ($('#description_ar').val() === 'قيم مختلفة' || $('#description_ar').val() === 'mix value') {
          $('#description_ar').focus();
          swal('الوصف العربي يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }


        if ($('#description_en').val() === 'قيم مختلفة' || $('#description_en').val() === 'mix value') {
          $('#description_en').focus();
          swal('الوصف الانجليزي يحتوي على قيم مختلفة يرجى مراجعة الادخال'); // show response from the php script.
          return false;
        }

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
          type: "POST",
          url: url,
          data: form.serialize(), // serializes the form's elements.
          success: function (data) {


            swal("Here's a message!");
            if (data.status === false) {
              swal(data.message); // show response from the php script.
            } else {

              if (data.data == 2) {
                $(location).attr('href', '{{url('panel/admin/image_store?type=complete')}}');
              } else if (data.data == 0) {
                $(location).attr('href', '{{url('panel/admin/image_store?type=no_edit')}}');
              } else {
                $(location).attr('href', '{{url('panel/admin/image_store?type=half_edit')}}');
              }


              swal(data.message); // show response from the php script.
            }

          },
        });


      });

      function copyName() {
        var input = $('#title_ar').val();
        for (var i = 0; i < idarray.length; i++) {
          $('#wirte_name' + idarray[i]).text(input);
        }

      }


      // allow a list of search terms to be pasted into the select2 input

      function copyTagAr() {

        var values = $('.tag_ar').val();


        //console.log(values[0]== 'null');

        if (values[0] == 'null') {
          swal('لايمكنك نسخ القيم المختلفة');
          return false;
        }

        var ids_tagAr = [];


        for (var catCC = 0; catCC < values.length; catCC++) {


          // ids_cat.push(values[catCC]);
          ids_tagAr.push(new Option(values[catCC], values[catCC], true, true));


        }


        Cookies.set('tag_ar', values);

        if (Cookies.get('tag_ar') != null) {
          swal('تم نسخ العناصر الي الحافظةبنجاح');
          // return false;
        }
        /*  $('.tag_en').empty();
          $('.tag_en').select2('val', "");
          $('.tag_en').append(ids_cat).trigger('change');*/


      }


      function pastTagAr() {


        if (Cookies.get('tag_ar') == null) {
          swal('لايوجد عناصر تم نسخها');
          return false;
        }
        var values = jQuery.parseJSON(Cookies.get('tag_ar'));


        if (values.length < 0) {
          swal('لايوجد عناصر تم نسخها');
          return false;
        }


        var ids_cat = [];


        for (var catCC = 0; catCC < values.length; catCC++) {


          // ids_cat.push(values[catCC]);
          ids_cat.push(new Option(values[catCC], values[catCC], true, true));


        }


        //  setcookie('cookie', serialize($info), time()+3600);
        $('.tag_ar').empty();
        $('.tag_ar').select2('val', "");
        $('.tag_ar').append(ids_cat).trigger('change');

        if (Cookies.get('tag_ar') != null) {
          swal('تم لصق العناصر الي الحافظةبنجاح');
          // return false;
        }
      }






      function copyTagEn() {

        var values = $('.tag_en').val();
        if (values[0] == 'null') {
          swal('لايمكنك نسخ القيم المختلفة');
          return false;
        }

        var ids_tagEn = [];


        for (var catCC = 0; catCC < values.length; catCC++) {


          // ids_cat.push(values[catCC]);
          ids_tagEn.push(new Option(values[catCC], values[catCC], true, true));


        }


        Cookies.set('tag_en', values);

        if (Cookies.get('tag_en') != null) {
          swal('تم نسخ العناصر الي الحافظةبنجاح');
          // return false;
        }

        /*  $('.tag_en').empty();
          $('.tag_en').select2('val', "");
          $('.tag_en').append(ids_cat).trigger('change');*/


      }


      function pastTagEn() {


        if (Cookies.get('tag_en') == null) {
          swal('لايوجد عناصر تم نسخها');
          return false;
        }
        var values = jQuery.parseJSON(Cookies.get('tag_en'));


        if (values.length < 0) {
          swal('لايوجد عناصر تم نسخها');
          return false;
        }


        var ids_cat = [];


        for (var catCC = 0; catCC < values.length; catCC++) {


          // ids_cat.push(values[catCC]);
          ids_cat.push(new Option(values[catCC], values[catCC], true, true));


        }


        //  setcookie('cookie', serialize($info), time()+3600);
        $('.tag_en').empty();
        $('.tag_en').select2('val', "");
        $('.tag_en').append(ids_cat).trigger('change');

        if (Cookies.get('tag_en') != null) {
          swal('تم لصق العناصر الي الحافظةبنجاح');
          // return false;
        }
      }

    </script>

@endsection
