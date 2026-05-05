@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title"> اضافة اسعار الفيديو </h3>
            </div>
        </div>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->


            <!-- Main content -->
            <div class="row">

                <div class="col-md-9">

                    <div class="box box-danger">


                        <!-- form start -->
                        <form class="form-horizontal" method="POST" action="{{ route('admin.videos.videos.price.edit', $data->id) }}" enctype="multipart/form-data">

                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="id" value="{{$data->id}}">

                            @include('errors.errors-forms')




                            @if(count($data->child)>0)
                                @foreach($data->child as $childItem)

                                    <div class="box-body">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">{{ $childItem->type }}</label>
                                            <div class="col-sm-10">
                                                <input type="text" value="{{ $childItem->price }}" name="price[]" class="form-control" placeholder="{{ trans('admin.price') }}">
                                            </div>
                                        </div>

                                    </div><!-- /.box-body -->

                                @endforeach

                                    <div class="box-footer">
                                        <a href="{{ url('video/panel/admin/videos') }}" class="btn btn-default">{{ trans('admin.cancel') }}</a>
                                        <button type="submit" class="btn btn-success pull-right">{{ trans('admin.save') }}</button>
                                    </div><!-- /.box-footer -->
                            @else

                                <div class="box-body">
                                    <div class="col-sm-10 form-group">
                                        <label class="control-label">الفيديو قيد المعالجة حاليا يرجى الانتظار</label>

                                    </div>

                                </div>



                            @endif
                        </form>
                    </div>

                </div><!-- /. col-md-9 -->

                @if(count($data->child)>0)
                <div class="col-md-3">
                    <a href="{{ $data->post_link }}" target="_blank" class="btn btn-lg btn-success btn-block margin-bottom-10">{{ trans('admin.view') }}
                        <i class="fa fa-external-link-square"></i> </a>
                </div><!-- col-md-3 -->

                    @endif

            </div><!-- /.row -->
        </div><!-- /.content-wrapper -->
    </div>

</div>
<!-- end:: Content -->
@endsection

@push('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css"/>
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css"/>
@endpush


@push('scripts')
<!-- icheck -->
<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    $("#tagInput").tagsInput({
        delimiter: [","], // Or a string with a single delimiter. Ex: ';'
        width: "auto",
        height: "auto",
        removeWithBackspace: true,
        minChars: 3,
        maxChars: 25,
        defaultText: '{{ trans("misc.add_tag") }}'
        /*onChange: function() {
            var input = $(this).siblings('.tagsinput');
            var maxLen = 4;

           if( input.children('span.tag').length >= maxLen){
                   input.children('div').hide();
               }
               else{
                   input.children('div').show();
               }
           },*/
    });

    $("#tagInput_en").tagsInput({
        delimiter: [","], // Or a string with a single delimiter. Ex: ';'
        width: "auto",
        height: "auto",
        removeWithBackspace: true,
        minChars: 3,
        maxChars: 25,
        defaultText: '{{ trans("misc.add_tag") }}'
        /*onChange: function() {
            var input = $(this).siblings('.tagsinput');
            var maxLen = 4;

           if( input.children('span.tag').length >= maxLen){
                   input.children('div').hide();
               }
               else{
                   input.children('div').show();
               }
           },*/
    });

    $(".actionDelete").click(function (e) {
        e.preventDefault();

        var element = $(this);
        var id = element.attr("data-url");
        var form = $(element).parents("form");

        element.blur();

        swal(
            {
                title: "{{trans('misc.delete_confirm')}}",
                type: "warning",
                showLoaderOnConfirm: true,
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "{{trans('misc.yes_confirm')}}",
                cancelButtonText: "{{trans('misc.cancel_confirm')}}",
                closeOnConfirm: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    form.submit();
                    //$('#form' + id).submit();
                }
            }
        );
    });

    //Flat red color scheme for iCheck
    $('input[type="radio"]').iCheck({
        radioClass: "iradio_flat-red"
    });
</script>
@endpush
