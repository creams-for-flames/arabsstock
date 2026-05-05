@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container kt-container--fluid kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">تعديل فيديو</h3>
            </div>
        </div>
        <div class="alert alert-danger alert-dismissible fade show row" role="alert">
            <strong> 
                    {{ __("admin.Notes: To modify the content status of the completed modifications") }}
            </strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            <div class="col-12">
                <ul>
                    <li>{{ __("admin.The title in English is not equal to the title in Arabic and vice versa.") }}</li>
                    <li>{{ __("admin.The title in Arabic does not contain English letters.") }}</li>
                    <li>{{ __("admin.The title should not contain characters except for regex and it is preferable not to use them frequently.") }}</li>
                    <li>{{ __("admin.Preferably write the title in Arabic without movements.") }}</li>
                    <li>{{ __("admin.The file must contain tags in English and Arabic.") }}</li>
                    <li>{{ __("admin.The file must have a category .") }}</li>
                </ul>
            </div>
        </div>
        <div class="container margin-bottom-40 padding-top-40">
            <div class="row">
                @if( Auth::user()->status == 'active' ) @if( $settings->limit_upload_user == 0 || $imagesUploads < $settings->limit_upload_user || Auth::user()->role == 'admin' )
                <!-- col-md-4-->
                <div class="col-md-2">
                    <div class="alert alert-warning" role="alert" style="display: none;">
                        <ul class="padding-zero">
                            <?php if( $settings->limit_upload_user == 0 ) { $limit = strtolower(trans('admin.unlimited')); } else { $limit = $settings->limit_upload_user; } ?>
                            <li class="margin-bottom-10"><i class="glyphicon glyphicon-warning-sign myicon-right"></i> {{ trans('conditions.terms') }}</li>
                            <li class="margin-bottom-10"><i class="glyphicon glyphicon-info-sign myicon-right"></i> {{ trans('conditions.upload_max', ['limit' => $limit ]) }}</li>
                            <li class="margin-bottom-10"><i class="glyphicon glyphicon-info-sign myicon-right"></i> {{ trans('conditions.sex_content') }}</li>
                            <li class="margin-bottom-10"><i class="glyphicon glyphicon-info-sign myicon-right"></i> {{ trans('conditions.own_images') }}</li>
                        </ul>
                    </div>
                </div>
                <!-- col-md-4-->

                <!-- col-md-8 -->
                <div class="col-md-8">
                    @include('errors.errors-forms')
                    <!-- form start -->
                    <form method="POST" action="{{ route('admin.videos.videos.update', $data->id) }}" enctype="multipart/form-data" id="formUpload">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-md-9">
                                        <div class="form-group m-form__group row">
                                            <video width="500" controls>
                                                <source src="{{cdn($data->cut_video)}}" id="video_here" />
                                            </video>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Start Form Group -->
                            <div class="form-group">
                                <label>{{ trans('admin.title_ar') }}</label>
                                <input type="text" value="{{ $data->title_ar }}" name="title_ar" id="title_ar" class="form-control" placeholder="{{ trans('admin.title_ar') }}" />
                            </div>
                            <!-- /.form-group-->

                            <div class="form-group">
                                <label>{{ trans('admin.title_en') }}</label>
                                <input type="text" value="{{ $data->title_en }}" name="title_en" id="title_en" class="form-control" placeholder="{{ trans('admin.title_en') }}" />
                            </div>
                            <!-- /.form-group-->

                            <div class="form-group">
                                <label>{{ trans('admin.slug') }}</label>
                                <div class="input-group" style=" direction: ltr; ">
                                  <div class="input-group-prepend"><span class="input-group-text">clip-{{$data->id}}-</span></div>
                                  <input type="text" value="{{ str_replace("clip-{$data->id}-", '', $data->slug) }}" name="slug" id="slug" class="form-control" placeholder="{{ trans('admin.slug') }}" />
                              </div>
                            </div>
                            <!-- /.form-group-->
oad
                            <!-- Start Form Group -->

                            <div class="form-group">
                                <label>{{ trans('misc.tags_ar') }}</label>
                                <label class="remove-x" onclick="removeTagar()">X</label>
                                <input type="text" value="@if($data->tagsAll)@foreach($data->tagsAll as $tagItem)@if($tagItem->local=='ar'){{$tagItem->tag}},@endif @endforeach @endif" id="tagInput" name="tags_ar" class="form-control" placeholder="{{ trans('misc.tags_ar') }}" />
                                <p class="help-block">* {{ trans('misc.add_tags_guide') }} ({{trans('misc.maximum_tags', ['limit' => $settings->tags_limit ]) }})</p>
                            </div>
                            <!-- /.form-group-->

                            <div class="form-group">
                                <label>{{ trans('misc.tags_en') }}</label>
                                <label class="remove-x" onclick="removeTagen()">X</label>
                                <input type="text" value="@if($data->tagsAll)@foreach($data->tagsAll as $tagItem)@if($tagItem->local=='en'){{$tagItem->tag}},@endif @endforeach @endif" id="tagInput_en" name="tags_en" class="form-control" placeholder="{{ trans('misc.tags_en') }}" />

                                <p class="help-block">* {{ trans('misc.add_tags_guide') }} ({{trans('misc.maximum_tags', ['limit' => $settings->tags_limit ]) }})</p>
                            </div>
                            <!-- /.form-group-->

                            <!-- Start Form Group -->
                            <div class="form-group">
                                <label>{{ trans('misc.category') }}</label>
                                <select id="categories_id" name="categories_id[]" class="form-control select2" multiple>
                                    @if (isset($all_categories) && count($all_categories) > 0)
                                    @foreach( $all_categories  as $category )
                                    <option value="{{$category->id}}" {{ in_array($category->id, $data->category->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{App::isLocale('en') ?  $category->name_en : $category->name_ar}}
                                    </option>
                                    @endforeach

                                    @endif
                                </select>
                            </div>
                            <!-- /.form-group-->

                            <!-- Start Form Group -->
                            <div class="form-group">
                                <label>{{ trans('misc.how_use_image') }}</label>
                                <select name="how_use_image" class="form-control">
                                    <option @if($data->how_use_image=='free')selected @endif value="free">{{ trans('misc.use_free') }}</option>
                                    <option @if($data->how_use_image=='free_personal')selected @endif value="free_personal">{{ trans('misc.use_free_personal') }}</option>
                                    <option @if($data->how_use_image=='editorial_only')selected @endif value="editorial_only">{{ trans('misc.use_editorial_only') }}</option>
                                    <option @if($data->how_use_image=='web_only')selected @endif value="web_only">{{ trans('misc.use_web_only') }}</option>
                                </select>
                            </div>
                            <!-- /.form-group-->

                            <!-- Start form-group -->
                            <div class="form-group">
                                <label>{{ trans('misc.attribution_required') }}</label>

                                <div class="radio">
                                    <label class="padding-zero"> <input {{$data->attribution_required=='yes' ? 'checked' : ''}} type="radio" name="attribution_required" value="yes"> {{ trans('misc.yes') }} </label>
                                </div>

                                <div class="radio">
                                    <label class="padding-zero"> <input {{$data->attribution_required=='no' ? 'checked' : ''}} type="radio" name="attribution_required" value="no"> {{ trans('misc.no') }} </label>
                                </div>
                            </div>
                            <!-- /.form-group -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{ trans('admin.status') }}</label>
                                <div class="col-sm-10">
                                    <select name="status" class="form-control">
                                        <option @if( $data->status == 'active' ) selected="selected" @endif value="active">{{ trans('admin.active') }}</option>
                                        <option @if( $data->status == 'pending' ) selected="selected" @endif value="pending">{{ trans('admin.pending') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{{ trans('admin.in_home') }}}</label>
                                <div class="col-sm-10">
                                    <div class="radio">
                                        <label class="padding-zero">
                                            <input type="radio" name="in_home" value="1" @if( $data->in_home == 1 ) checked @endif>
                                            {{{ trans('admin.active') }}}
                                        </label>
                                    </div>

                                    <div class="radio">
                                        <label class="padding-zero">
                                            <input type="radio" @if(  $data->id == 1 ) disabled="disabled" @endif name="in_home" value="0" @if( $data->in_home == 0 ) checked @endif>
                                            {{{ trans('admin.disabled') }}}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ trans('admin.description_ar') }} ({{ trans('misc.optional') }})</label>
                                <textarea name="description_ar" rows="4" id="description_ar" class="form-control" placeholder="{{ trans('admin.description_ar') }}">{{ $data->description_ar }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>{{ trans('admin.description_en') }} ({{ trans('misc.optional') }})</label>
                                <textarea name="description_en" rows="4" id="description_en" class="form-control" placeholder="{{ trans('admin.description_en') }}">{{ $data->description_en }}</textarea>
                            </div>

                            <!-- Alert -->

                            <div class="box-footer">
                                <hr />
                                <button type="submit" id="upload" class="btn btn-lg btn-success pull-right"><i class="fa fa-cloud-upload myicon-right"></i> {{ trans('global.update') }}</button>
                            </div>
                            <!-- /.box-footer -->
                        </div>

                        <div class="col-md-2"></div>
                    </form>
                </div>
                <!-- col-md-8-->

                @else

                <div class="btn-block text-center margin-top-40">
                    <i class="icon-warning ico-no-result"></i>
                </div>

                <h3 class="margin-top-none text-center no-result no-result-mg">
                    {{trans('misc.limit_uploads_user')}}
                </h3>

                @endif @else
                <div class="btn-block text-center margin-top-40">
                    <i class="icon-warning ico-no-result"></i>
                </div>

                <h3 class="margin-top-none text-center no-result no-result-mg">{{trans('misc.confirm_email')}} <strong>{{Auth::user()->email}}</strong></h3>
                @endif {{-- Verify User Active --}}
            </div>
            <!-- row -->
        </div>
    </div>
</div>

<!-- end:: Content -->
@endsection

@push('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    i.glyphicon.glyphicon-remove-sign:before {
        content: "\f103" !important;
        font-family: Flaticon2 !important;
        font-style: normal;
        font-weight: normal;
        font-variant: normal;
        line-height: 1;
        text-decoration: inherit;
        text-rendering: optimizeLegibility;
        text-transform: none;
        -moz-osx-font-smoothing: grayscale;
        -webkit-font-smoothing: antialiased;
        font-smoothing: antialiased;
    }
    div.tagsinput span.tag a {
        font-weight: 100;
        color: #ffffff;
        text-decoration: none;
        font-size: 10px;
    }
    label.remove-x {
        margin: 0 10px;
        background: #f1f2f7;
        color: #282733;
        padding: 0px 8px;
    }
</style>
@endpush


@push('scripts')
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $(document).on('click','#avatar_file',function () {

        var _this = $(this);
        $("#uploadAvatar").trigger('click');
        _this.blur();
    });

    $('#categories_id').select2();
    //Flat red color scheme for iCheck
    $('input[type="radio"]').iCheck({
        radioClass: 'iradio_flat-red'
    });

    $('#removePhoto').click(function(){
        $('#filePhoto').val('');
        $('#title').val('');
        $('.previewPhoto').css({backgroundImage: 'none'}).hide();
        $('.filer-input-dragDrop').removeClass('hoverClass');
    });

    //================== START FILE IMAGE FILE READER
    $("#filePhoto").on('change', function(){

        var loaded = false;
        if(window.File && window.FileReader && window.FileList && window.Blob){
            if($(this).val()){ //check empty input filed
                oFReader = new FileReader(), rFilter = /^(?:image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/png|image)$/i;
                if($(this)[0].files.length === 0){return}


                var oFile = $(this)[0].files[0];
                var fsize = $(this)[0].files[0].size; //get file size
                var ftype = $(this)[0].files[0].type; // get file type


                if(!rFilter.test(oFile.type)) {
                    $('#filePhoto').val('');
                    $('.popout').addClass('popout-error').html("{{ trans('misc.formats_available') }}").fadeIn(500).delay(5000).fadeOut();
                    return false;
                }

                var allowed_file_size = {{$settings->file_size_allowed * 1024}};

                if(fsize>allowed_file_size){
                    $('#filePhoto').val('');
                    $('.popout').addClass('popout-error').html("{{trans('misc.max_size').': '.App\Helper::formatBytes($settings->file_size_allowed * 1024)}}").fadeIn(500).delay(5000).fadeOut();
                    return false;
                }
                <?php $dimensions = explode('x',$settings->min_width_height_image); ?>

                        oFReader.onload = function (e) {

                    var image = new Image();
                    image.src = oFReader.result;

                    image.onload = function() {

                        if( image.width < {{ $dimensions[0] }}) {
                            $('#filePhoto').val('');
                            $('.popout').addClass('popout-error').html("{{trans('misc.width_min',['data' => $dimensions[0]])}}").fadeIn(500).delay(5000).fadeOut();
                            return false;
                        }

                        if( image.height < {{ $dimensions[1] }} ) {
                            $('#filePhoto').val('');
                            $('.popout').addClass('popout-error').html("{{trans('misc.height_min',['data' => $dimensions[1]])}}").fadeIn(500).delay(5000).fadeOut();
                            return false;
                        }

                        $('.previewPhoto').css({backgroundImage: 'url('+e.target.result+')'}).show();
                        $('.filer-input-dragDrop').addClass('hoverClass');
                        var _filname =  oFile.name;
                        var fileName = _filname.substr(0, _filname.lastIndexOf('.'));
                        $('#title').val(fileName);
                    };// <<--- image.onload


                }

                oFReader.readAsDataURL($(this)[0].files[0]);

            }
        } else{
            $('.popout').html('Can\'t upload! Your browser does not support File API! Try again with modern browsers like Chrome or Firefox.').fadeIn(500).delay(5000).fadeOut();
            return false;
        }
    });




    $('input[type="file"]').attr('title', window.URL ? ' ' : '');


    function removeTagen()
    {
        //var input = $('tagInput').siblings('.tagsinput');
    //    input.children('div').hide();
    //    $('tagInput').tagsInput('removeAll');
    //    var input = $(this);

        $('#tagInput_en_tagsinput .tag').hide();
        $('#tagInput_en').val('');
    }

    function removeTagar()
    {
        //var input = $('tagInput').siblings('.tagsinput');
        //    input.children('div').hide();
        //    $('tagInput').tagsInput('removeAll');
        //    var input = $(this);

        $('#tagInput_tagsinput .tag').hide();
        $('#tagInput').val('');
    }

    $("#tagInput").tagsInput({

        'delimiter': [','],   // Or a string with a single delimiter. Ex: ';'
        'width':'auto',
        'height':'auto',
        'removeWithBackspace' : true,
        'minChars' : 2,
        'maxChars' : 25,
        'defaultText':'{{ trans("misc.add_tag") }}',
        onChange: function() {
            var input = $(this).siblings('.tagsinput');
            var maxLen = {{$settings->tags_limit}};

            if( input.children('span.tag').length >= maxLen){
                input.children('div').hide();
            }
            else{
                input.children('div').show();
            }
        },
    });


    $("#tagInput_en").tagsInput({

        'delimiter': [','],   // Or a string with a single delimiter. Ex: ';'
        'width':'auto',
        'height':'auto',
        'removeWithBackspace' : true,
        'minChars' : 2,
        'maxChars' : 25,
        'defaultText':'{{ trans("misc.add_tag") }}',
        onChange: function() {
            var input = $(this).siblings('.tagsinput');
            var maxLen = {{$settings->tags_limit}};

            if( input.children('span.tag').length >= maxLen){
                input.children('div').hide();
            }
            else{
                input.children('div').show();
            }
        },
    });
</script>

<script>
    $(document).on("change", ".file_multi_video", function (evt) {
        var $source = $("#video_here");
        $source[0].src = URL.createObjectURL(this.files[0]);
        $source.parent()[0].load();
    });
</script>
@endpush
