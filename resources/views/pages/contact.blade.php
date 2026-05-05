@extends('app')
@section('title') @if(App::isLocale('en')) {{ 'Model - ' }} @else {{ 'العارضين - ' }} @endif @endsection
@section('content')
    <link href="{{  asset('css/tagsinput.css') }}" rel="stylesheet"/>
    <link href="{{ asset('css/select2@4.1.0-rc.0_dist_css_select2.min.css') }}" rel="stylesheet"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"/>
    <style type="text/css">
        .bootstrap-tagsinput .badge{
            margin: 2px 3px;
            padding: 5px 14px;
            position: relative;
        }
        .bootstrap-tagsinput .badge [data-role="remove"]{
            margin-left: unset !important;
            position: absolute;
        }
        [class^='select2']{
            /* border-radius: 0px !important; */
        }
        .select2-selection--single{
            background-color: unset !important;
            border: unset !important;

        }
        .hidden{
            display: none;
        }
        .newStyle{
            background-color: #63f7c5;
            border-color: #63f7c5;
        }

        .newStyle:hover{
            background-color: #20d598;
            border-color: #20d598;
        }


        .fa-plus:before{
            content: "\f067";
            font-family: 'Font Awesome 5 Pro';}

        .imagePreview{
            width: 100%;
            height: 180px;
            background-position: center center;
            background: url(http://cliquecities.com/assets/no-image-e3699ae23f866f6cbdf8ba2443ee5c4e.jpg);
            background-color: #fff;
            background-size: cover;
            background-repeat: no-repeat;
            display: inline-block;
            box-shadow: 0px -3px 6px 2px rgba(0, 0, 0, 0.2);
        }


        .imagePreview2{
            width: 100%;
            height: 180px;
            background-position: center center;
            background: url(http://cliquecities.com/assets/no-image-e3699ae23f866f6cbdf8ba2443ee5c4e.jpg);
            background-color: #fff;
            background-size: cover;
            background-repeat: no-repeat;
            display: inline-block;
            box-shadow: 0px -3px 6px 2px rgba(0, 0, 0, 0.2);
        }
        .btn-primary{
            display: block;
            border-radius: 0px;
            box-shadow: 0px 4px 6px 2px rgba(0, 0, 0, 0.2);
            margin-top: -5px;
            background-color: #20d899;
            border-color: #20d899;
        }
        .imgUp{
            margin-bottom: 15px;
        }

        .imgUp2{
            margin-bottom: 15px;
        }
        .del{
            position: absolute;
            top: 0px;
            right: 15px;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }
        .imgAdd{
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #4bd7ef;
            color: #fff;
            box-shadow: 0px 0px 2px 1px rgba(0, 0, 0, 0.2);
            text-align: center;
            line-height: 30px;
            margin-top: 0px;
            cursor: pointer;
            font-size: 15px;
        }

        .imgAdd2{
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #4bd7ef;
            color: #fff;
            box-shadow: 0px 0px 2px 1px rgba(0, 0, 0, 0.2);
            text-align: center;
            line-height: 30px;
            margin-top: 0px;
            cursor: pointer;
            font-size: 15px;
        }


        .redcolor{
            color: rgb(220, 53, 69);
        }

        .black{
            color: black;
        }

        .customz{
            font-size: 13px;
            font-weight: 700;
        }
        .iti__arrow{margin-right: 6px;margin-left: 0;}
        .iti{display: block;}
        .iti--allow-dropdown .iti__flag-container, .iti--separate-dial-code .iti__flag-container{left: 20px;}
        .iti--allow-dropdown input, .iti--allow-dropdown input[type=text], .iti--allow-dropdown input[type=tel], .iti--separate-dial-code input, .iti--separate-dial-code input[type=text], .iti--separate-dial-code input[type=tel]{padding-left: 70px;}
    </style>

    @if(app()->getLocale() == 'en')
        <style type="text/css">
            .form-control.is-invalid, .was-validated .form-control:invalid{
                position: absolute;
                border-color: #dc3545 !important;
                /* padding-right: calc(1.5em + .95rem); */
                background-image: url(data:image/svg+xml,%3csvg xmlns= 'http://www.w3.org/2000/svg' width= '12' height= '12' fill= 'none' stroke= '%23dc3545' viewBox= '0 0 12 12' %3e%3ccircle cx= '6' cy= '6' r= '4.5' /%3e%3cpath stroke-linejoin= 'round' d= 'M5.8 3.6h.4L6 6.5z' /%3e%3ccircle cx= '6' cy= '8.2' r= '.6' fill= '%23dc3545' stroke= 'none' /%3e%3c/svg%3e) !important;
                background-repeat: no-repeat !important;
                background-position: right calc(.375em + 0.1895rem) center !important;
                background-size: calc(.75em + 1.375rem) calc(.75em + .375rem) !important;
            }
        </style>
    @else
        <style type="text/css">
            .form-control.is-invalid, .was-validated .form-control:invalid{
                z-index: 99999999999 !important;
                position: relative;
                border-color: #dc3545 !important;
                padding-left: 2.25rem !important;
                background-repeat: no-repeat !important;
                background-position: center left calc(2.25rem / 4) !important;
                /* background-size: calc(2.25rem / 2) calc(2.25rem / 2) !important; */
                /* background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='-2 -2 7 7'%3e%3cpath stroke='%23d9534f' d='M0 0l3 3m0-3L0 3'/%3e%3ccircle r='.5'/%3e%3ccircle cx='3' r='.5'/%3e%3ccircle cy='3' r='.5'/%3e%3ccircle cx='3' cy='3' r='.5'/%3e%3c/svg%3E") !important; */


            }
            .form-control.is-valid, .was-validated .form-control:valid{

                z-index: 99999999999;
                border-color: #28a745 !important;
                padding-left: 2.25rem !important;
                background-repeat: no-repeat !important;
                background-position: center left calc(9.25rem / 4) !important;
                background-size: calc(2.25rem / 2) calc(2.25rem / 2) !important;
                background-image: url(data:image/svg+xml,%3csvg xmlns= 'http://www.w3.org/2000/svg' viewBox= '0 0 8 8' %3e%3cpath fill= '%2328a745' d= 'M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z' /%3e%3c/svg%3e) !important;
            }
        </style>
    @endif
    <div class="category-header jumbo-banner" data-overlay="6"
         style="background-image: url({{ asset('img/cast.webp') }});">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <h1 class="title-site">
                    {{trans('global.register_views' )}}
                </h1>
            </div>
            <!--  Demos -->
        </div>
    </div>


    <div class="container mt-50 mb-50">
        <div class="row">
            <div class="col-12 col-md-10 offset-md-1">
                <div class="contact-title mb-5">
                    <i class="fal fa-user-edit"></i>
                    <h2>{{trans('global.please_fill_form')}}</h2>
                </div>
                @if (count($errors) > 0)
                    <ul style="border: 1px solid #e02222; background-color: white">
                        @foreach ($errors->all() as $error)
                            <li style="color: #e02222; margin: 15px">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @if (session('success'))
                    <ul style="border: 1px solid #01b070; background-color: white">
                        <li style="color: #01b070; margin: 15px">{{  session('success')  }}</li>
                    </ul>
                @endif
                <div id="modal_body_error"></div>
                <div class="bd-example">
                    <form id="bb" method="post" enctype="multipart/form-data"
                          action="{{url(app()->getLocale().'/contact_post')}}"
                          class="position-relative needs-validation" novalidate>
                        <input type="hidden" name="_recaptcha">
                        {!! csrf_field() !!}
                        <div id="wrap_validation"></div>
                        <div class="form-row">
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4 ">
                                <div class="form-group">
                                    <label>{{trans('global.Name' )}} </label>
                                    <div class="input-with-gray">
                                        <input type="text" id="name" value="{{ old('name') }}" name="name"
                                               title="First Name" oninvalid="setCustomValidity('The Name Is Requerd')"
                                               oninput="setCustomValidity('')" required autocomplete="off"
                                               class="form-control" placeholder="{{trans('global.Name' )}}"
                                        />
                                        <i class="fal fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.Email' )}} </label>
                                    <div class="input-with-gray">
                                        <input type="email" value="{{ old('email') }}" name="email" title="Email"
                                               oninvalid="setCustomValidity('The Email Is required')"
                                               oninput="setCustomValidity('')" required autocomplete="off"
                                               class="form-control" placeholder="{{trans('global.Email' )}}"/>
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('Mobile') }}</label>
                                    <div class="input-with-gray">
                                        <input id="mobileCasting" value="{{ old('mobile') }}" name="mobile" required
                                               oninvalid="setCustomValidity('{{__('Mobile')}}')"
                                               oninput="setCustomValidity('')"
                                               placeholder="{{ __('Mobile') }}" type="text"
                                               class="form-control" style="direction: ltr">
                                        <i class="fal fa-mobile"></i></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.nationality' )}}</label>
                                    <div class="input-with-gray">
                                        <select class="form-control select2" name="nationality_id"
                                                oninvalid="setCustomValidity('The Name Is Requerd')"
                                                required>
                                            <option
                                                value="">  {{trans('global.select')}} {{trans('global.nationality' )}}</option>
                                            @if (isset($countries))
                                                @foreach($countries as $country)
                                                    <option
                                                        value="{{$country->id}}" {{ old('nationality_id') == $country->id ? "selected" : "" }}>
                                                        @if(app()->getLocale() == 'en') {{$country->name_en}} @else {{$country->name_ar}} @endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <!-- <i class="fal fa-file-alt"></i> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.placeـofـresidence' )}}</label>
                                    <div class="input-with-gray">
                                        <select id="country_id" class="form-control select2" name="country_id"
                                                required>
                                            <option
                                                value="">  {{trans('global.select')}} {{trans('global.placeـofـresidence' )}}</option>
                                            @if (isset($countries_ar))
                                                @foreach($countries_ar as $country)
                                                    <option value="{{$country->id}}">
                                                        @if(app()->getLocale() == 'en') {{$country->name_en}} @else {{$country->name_ar}} @endif
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <!-- <i class="fal fa-file-alt"></i> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.city' )}}</label>
                                    <div class="input-with-gray">
                                        <select id="city_id" class="form-control select2" name="city_id"
                                                required>
                                            <option
                                                value="">  {{trans('global.select')}} {{trans('global.city' )}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.length' )}} ({{trans('global.cm' )}}) ( 2-3 )</label>
                                    <div class="input-with-gray">
                                        <input type="number" value="{{ old('length') }}" required name="length"
                                               title="{{trans('global.length' )}}" autocomplete="off"
                                               class="form-control" placeholder="{{trans('global.length' )}}"/>
                                        <i class="fal fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.weight' )}} ({{trans('global.kg' )}}) ( 2-3 )</label>
                                    <div class="input-with-gray">
                                        <input type="number" value="{{ old('weight') }}" required name="weight"
                                               title="{{trans('global.weight' )}}" autocomplete="off"
                                               class="form-control" placeholder="{{trans('global.weight' )}}"/>
                                        <i class="fal fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.work_field' ) .' (' . __('global.optional').' )'}} </label>
                                    <div class="input-with-gray">
                                        <input id="work_field" type="text" value="{{ old('work_field') }}"
                                               name="work_field" title="{{trans('global.work_field' )}}"
                                               autocomplete="off" class="form-control"
                                               placeholder="{{trans('global.work_field' )}}"/>
                                        <i class="fal fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>{{trans('global.birth_date' )}} ({{trans('global.birth' )}})</label>
                                    <div class="input-with-gray">
                                        @php
                                            $earliest_year = 1920;
                                        @endphp
                                        <select class="form-control select2" name="birth_date"
                                                required>
                                            <option
                                                value="">  {{trans('global.select')}} {{trans('global.birth_date' )}}</option>
                                            @foreach(range(date('Y'), $earliest_year) as $x)
                                                <option value="{{$x}}" {{ old('birth_date') == $x ? "selected" : "" }}>
                                                    {{ $x }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>{{trans('global.sex' )}}</label>
                                    <div class="input-with-gray">
                                        <select id="sex" required class="form-control select2" name="sex"
                                                required>
                                            <option
                                                value="">  {{trans('global.select')}} {{trans('global.sex' )}}</option>
                                            <option
                                                value="mail" {{ old('sex') == 'mail' ? "selected" : "" }} >  {{trans('global.mail')}}</option>
                                            <option
                                                value="femail" {{ old('sex') == 'femail' ? "selected" : "" }} >  {{trans('global.femail')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label
                                        id="sss">{{trans('global.skills' )  .' (' . __('global.optional').' )'}}</label>
                                    <div id="sss2"></div>
                                    <input class="form-select" value="{{ old('skill') }}" name="skill"
                                           data-role="tagsinput">
                                </div>
                            </div>
                            {{-- <div class="col-12 col-sm-6 col-md-6 col-lg-6 hidden " id="inputing" >
                                <div class="form-group">
                                    <label>{{trans('global.add_skills' )}}</label>
                                    <div class="input-with-gray">
                                        <input {{ old('skill') }} type="text" value="" id="puting" name="skill"  title="{{trans('global.skills' )}}" autocomplete="off" class="form-control" placeholder="{{trans('global.skills' )}}" />
                                        <i class="fal fa-file-alt"></i>
                                        @if(app()->getLocale() == 'en')
                                        <span style="color: #796e6e;
        font-style: italic;font-size: 11px;">Hint: Voiceover,Dance, Rendering </span>
                                        @else
                                        <span style="color: #796e6e;
        font-style: italic;font-size: 11px;">ملحوظة: التعليق الصوتي ، الرقص ، التقديم</span>
                                        @endif
                                    </div>
                                </div>
                            </div> --}}
                            <br>
                            <div class="container">
                                <label id="cur_1">{{trans('global.images_cagwal')}} :</label>
                                <div id="one2"></div>
                                <div class="row mt-3">
                                    <div class="col-sm-2 imgUp">
                                        <div class="imagePreview"></div>
                                        <label class="btn btn-primary newStyle">{{trans('global.upload' )}}
                                            <input type="file" class="uploadFile img" name="images[]"
                                                   aria-label="file example" required value="Upload Photo"
                                                   style="width: 0px;height: 0px;overflow: hidden;" id="one">
                                        </label>
                                    </div><!-- col-2 -->
                                    <i class="fa fa-plus imgAdd"></i>
                                </div><!-- row -->
                            </div><!-- container -->
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="col-12" style="margin-top:40px;">
                                <div class="form-group">
                                    <button type="submit"
                                            class="btn btn-primary btn-lg btn-block">{{trans('global.Send' )}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="{{ asset('js/select2@4.1.0-rc.0_dist_js_select2.min.js') }}"></script>
    <script src="{{ asset('js/tagsinput.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"
            integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="text/javascript">
        $('.select2').select2({});

        (function () {
            'use strict';
            window.addEventListener('load', function () {
// Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
// Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            // var list = form.querySelectorAll(':invalid');
                            // for (var item of list) {
                            //     item.setAttribute("style", "boder-color: red;")
                            // }
                            if ($('#one').val() == '') {
                                document.getElementById("cur_1").style.color = '#dc3545';
                                document.getElementById("one2").innerHTML = '';
                                var check_errors = '{{trans('global.please choose at least one image')}}';
                                $('#one2').append('<p class="redcolor">' + check_errors + '</p>');
                            }
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                        grecaptcha.ready(function () {
                            grecaptcha.execute('{{ config('app.recaptcha.site_key') }}', {action: 'contact'}).then(function (token) {
                                $('#bb input[name="_recaptcha"]').val(token);
                            });
                        });
                    }, false);
                });
            }, false);
        })();

        $(".imgAdd").click(function () {
            $(this).closest(".row").find('.imgAdd').before('<div class="col-sm-2 imgUp"><div class="imagePreview"></div><label class="btn btn-primary newStyle" >{{trans('global.upload' )}}<input type="file" required name="images[]" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fal fa-trash del" style="color:red"></i></div>');
        });
        $(document).on("click", "i.del", function () {
            $(this).parent().remove();
        });
        $(function () {
            $(document).on("change", ".uploadFile", function () {
                var uploadFile = $(this);
                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

                if (/^image/.test(files[0].type)) { // only image file
                    var reader = new FileReader(); // instance of the FileReader
                    reader.readAsDataURL(files[0]); // read the local file

                    reader.onloadend = function () { // set image data as background of div
                        //alert(uploadFile.closest(".upimage").find('.imagePreview').length);
                        uploadFile.closest(".imgUp").find('.imagePreview').css("background-image", "url(" + this.result + ")");
                        document.getElementById("cur_1").style.color = '#000';
                        $('#one2').html('');
                        $('#one2').addClass('hidden');
                    }
                }

            });
        });

        $(".imgAdd2").click(function () {
            $(this).closest(".row").find('.imgAdd2').before('<div class="col-sm-2 imgUp2"><div class="imagePreview2"></div><label class="btn btn-primary newStyle">{{trans('global.upload' )}}<input type="file" required name="images[]" class="uploadFile2 img2" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><i class="fal fa-trash del" style="color:red"></i></div>');
        });
        $(document).on("click", "i.del", function () {
            $(this).parent().remove();
        });
        $(function () {
            $(document).on("change", ".uploadFile2", function () {
                var uploadFile = $(this);
                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

                if (/^image/.test(files[0].type)) { // only image file
                    var reader = new FileReader(); // instance of the FileReader
                    reader.readAsDataURL(files[0]); // read the local file

                    reader.onloadend = function () { // set image data as background of div
                        //alert(uploadFile.closest(".upimage").find('.imagePreview2').length);
                        uploadFile.closest(".imgUp2").find('.imagePreview2').css("background-image", "url(" + this.result + ")");
                        document.getElementById("cur_2").style.color = '#000';
                        // $('#two2').html('');
                        // $('#two2').addClass('hidden');
                    }
                }

            });
        });

        function getID(ID) {
            if (document.getElementById("skill_8").checked == true) {
                $('#inputing').removeClass('hidden');
                $('#puting').prop('required', true);
            } else {
                $('#inputing').addClass('hidden');
                $('#puting').prop('required', false);
            }

            if ($('input[name="skills[]"]:checked').length != 0) {
                $("#sss2").addClass('hidden');
                document.getElementById("sss").style.color = 'black';

            } else {

                document.getElementById("sss").style.color = '#dc3545';
                document.getElementById("sss2").html = '';
                var check_errors = '{{trans('global.please choose at least one')}}';
                $('#sss2').append('<p class="redcolor">' + check_errors + '</p>');

            }
        }

        $('#country_id').on('change', function () {
            // alert( this.value );
            var country_id = this.value;
            var url = '{{url('/en/getCity')}}';

            var csrf_token = '{{csrf_token()}}';
            $.ajax({
                type: 'GET',
                headers: {'X-CSRF-TOKEN': csrf_token},
                url: url,
                data: {country_id: country_id},
                success: function (response) {
                    //console.log(response);
                    if (response) {
                        $('#city_id').html("");
                        //console.log(response);
                        var toAppend = '';

                        toAppend += '<option value="">{{trans('global.select')}} {{trans('global.city' )}}</option>';

                        $.each(response, function (i, o) {
                            <?php  if(app()->getLocale() == 'en'){ ?>
                                toAppend += '<option value=' + o.id + '>' + o.name_en + '</option>';
                            <?php }else{ ?>
                                toAppend += '<option value=' + o.id + '>' + o.name_ar + '</option>';
                            <?php }  ?>


                        });


                        $('#city_id').append(toAppend);
                        // $r = document.getElementById('sessions').value;
                        // alert($r);
                    }
                },
                error: function (e) {

                }
            });
        })

        function blockSpecialChar(e) {
            var k;
            document.all ? k = e.keyCode : k = e.which;
            return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
        }

        $("#name").keypress(function (event) {
            var character = String.fromCharCode(event.keyCode);
            return isValid(character);
        });

        $("#work_field").keypress(function (event) {
            var character = String.fromCharCode(event.keyCode);
            return isValid(character);
        });

        function isValid(str) {
            return !/[~`!@#$%\^&*()+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
        }

        document.addEventListener("DOMContentLoaded", () => {
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('app.recaptcha.site_key') }}', {action: 'contact'}).then(function (token) {
                    $('#bb input[name="_recaptcha"]').val(token);
                });
            });
        })
    </script>
    <script>
        var input = document.querySelector("#mobileCasting");
        window.intlTelInputGlobals.loadUtils("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js");
        iti = intlTelInput(input, {
            allowExtensions: true,
            autoFormat: false,
            autoHideDialCode: false,
            customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
                return selectedCountryPlaceholder;
            },
            defaultCountry: "auto",
            ipinfoToken: "yolo",
            nationalMode: false,
            separateDialCode: false,
            numberType: "MOBILE",
            preventInvalidNumbers: true,
            initialCountry: "sa",
        });
    </script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('reCAPTCHA_site_key')}}" defer></script>
@endsection
