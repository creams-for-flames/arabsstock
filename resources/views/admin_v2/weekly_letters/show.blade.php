@extends('admin_v2.layout.app')

@section('content')
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content" style="margin-left:0;">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="kt-portlet kt-portlet--tabs">
                            <div class="kt-portlet__body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="kt_user_edit_tab_1" role="tabpanel">
                                        <div class="kt-form kt-form--label-right">
                                            <div class="kt-form__body">
                                                <div class="kt-section kt-section--first">
                                                    <div class="kt-section__body">
                                                        <div class="form-group row">
                                                            <label class="col-xl-1 col-lg-1 col-form-label">{{__('views.Title')}} </label>
                                                            <div class="col-lg-11 col-xl-11">
                                                                <div class="input-group validated">
                                                                    <input disabled type="text" value="{{$WeeklyLetter->title}}" name="title" class="form-control" placeholder="{{__('views.Title')}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-xl-3 col-lg-1 col-form-label text-center">{{ __('views.To') }}</label>
                                                            <div class="col-lg-11 col-xl-8">
                                                                <div class="input-group validated">
                                                                    <select class="form-control" disabled name="target">
                                                                        <option
                                                                            {{$WeeklyLetter->target === 'all' ? 'selected' : ''}} value="all">{{__('views.All Users')}}</option>
                                                                        <option
                                                                            {{$WeeklyLetter->target === 'custom' ? 'selected' : ''}} value="custom">{{__('views.specific')}}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row " id="custom-target" @if($WeeklyLetter->target != 'custom') style="display: none;" @endif>
                                                            <label class="col-xl-3 col-lg-1 col-form-label text-center">{{ __('views.specific') }}</label>
                                                            <div class="col-lg-11 col-xl-8">
                                                                <div class="input-group">
                                                                    <input type="text" value="{{ $WeeklyLetter->custom_target }}" disabled name="custom_target" class="form-control" placeholder="{{ __('views.specific') }}" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row ">
                                                            <label class="col-xl-3 col-lg-1 col-form-label text-center">{{ __($WeeklyLetter->users()->count().'/'.$WeeklyLetter->target_count) }}</label>
                                                            <div class="col-lg-11 col-xl-8">
                                                                <div class="progress" style="height: 40px;">
                                                                    <div class="progress-bar @if($progress < 100) progress-bar-striped progress-bar-animated @else bg-success @endif" role="progressbar" style="width: {{$progress}}%;font-size: 20px;font-weight: 600;" aria-valuenow="{{$progress}}" aria-valuemin="0" aria-valuemax="100">{{$progress}}%</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <fieldset style="border-radius:10px;padding:30px; border:2px solid rgb(192, 192, 192);">
                                                            <legend>#</legend>
                                                            <div class="alert alert-info" role="alert">
                                                                <span class="pr-1 pl-1">
                                                                    {{__("admin.categories_count")}}

                                                                </span>
                                                                <span class="pl-2 pr-2 badge badge-light" id="categories_count"> {{$WeeklyLetter->categories_count}} </span>
                                                                <span class="pr-1 pl-1">
                                                                    {{__("admin.files_count")}}

                                                                </span>
                                                                <span class="pl-2 pr-2 badge badge-light" id="files_count"> {{$WeeklyLetter->files_count}} </span>

                                                              </div>
                                                            <div class="form-group row">
                                                                @foreach ($files as $file)

                                                                <div class="rowContainer">
                                                                    <div class="form-group row">
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="file_type[]">{{__('admin.categories')}}</label>
                                                                                <select disabled name="file_type[]" class="form-control" id="file_type[]">
                                                                                    <option selected label="{{__("admin.".$file->file_type)}}" >{{$file->file_type}}</option>
                                                                                </select>
                                                                              </div>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="fileable_type[]">{{__("admin.type")}}</label>
                                                                                <select disabled name="fileable_type[]" class="form-control" id="fileable_type[]">
                                                                                    <option selected label="{{__("admin.".$file->type)}}" >{{$file->type}}</option>

                                                                                </select>
                                                                              </div>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="file_title[]">{{__("admin.title")}}</label>
                                                                                <input disabled value="{{$file->file_type === 'category'?$file->file_title:$file->fileable->title_ar}}" name="file_title[]" type="text" class="form-control" id="file_title[]" placeholder="{{__("admin.title")}}">
                                                                              </div>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="file_url[]">{{__("admin.link")}}</label>
                                                                                <input disabled value="{{$file->file_type === 'category'?$file->file_url:$file->fileable->slug}}" name="file_url[]" type="text" class="form-control" id="file_url[]" placeholder="{{__("admin.link")}}">
                                                                              </div>
                                                                        </div>
                                                                        <div class="col">
                                                                            <div class="form-group">
                                                                                <label for="fileable_id[]">{{__("admin.file_number")}}</label>
                                                                                <input disabled value="{{$file->fileable_id}}" name="fileable_id[]" type="text" class="form-control" id="fileable_id[]" placeholder="{{__("admin.file_number")}}">
                                                                              </div>
                                                                            </div>
                                                                            <div class="col text-center">
                                                                            <img src="{{cdn($file->fileable->og_image)}}" width="100" height="100" class="img-thumbnail rounded">

                                                                            </div>

                                                                    </div>

                                                                </div>
                                                                @endforeach

                                                            </div>
                                                        </fieldset>

                                                        <br>
                                                        <fieldset style="border-radius:10px;padding:30px; border:2px solid rgb(192, 192, 192);">
                                                            <legend>{{__("global.Email")  }} - {{__("views.WeeklyLetter")}}</legend>
                                                            <div id="render_mail">
                                                                @if (($WeeklyLetter->files_generated) && ($WeeklyLetter->files_pending === 0))

                                                                {!! $mail !!}
                                                                @else
                                                                <div class="alert alert-dark" role="alert">
                                                                    <span class="mail_in_processing">
                                                                        {{__("admin.mail_in_processing",['files_generated'=>$WeeklyLetter->files_generated,'files_pending'=>$WeeklyLetter->files_pending])}}

                                                                    </span>


                                                                    <button type="button" title="{{__('admin.refresh')}}" class="  position-absolute "id="refresh"><i class="kt-menu__link-icon text-dark fa-x flaticon-refresh"></i></button>

                                                                  </div>
                                                                @endif

                                                            </div>
                                                         </fieldset>



                                                    <div
                                                    @if (($WeeklyLetter->files_pending ))
                                                    class="d-none"
                                                    @endif

                                                    id="submit_form">

                                                        <hr>
                                                        <br>
                                                        <br>
                                                        <form action="{{ route('admin.weekly_letters.submit',['id'=> $id]) }}" method="post">
                                                            @csrf

                                                            <div class="kt-form__actions">
                                                                <div class="row">
                                                                    <div class="col-xl-3"></div>
                                                                    <div class="col-lg-9 col-xl-8">
                                                                        <button class="btn btn-label-brand btn-bold @if ( (($WeeklyLetter->status === 'submit')  && ($WeeklyLetter->files_pending === 0))) dis_button @endif"
                                                                        @if ( (($WeeklyLetter->status === 'submit')  && ($WeeklyLetter->files_pending === 0)))
                                                                        disabled
                                                                        id="submit_btn"
                                                                        @endif
                                                                         type="submit">
                                                                        @if (($WeeklyLetter->status === 'submit'))

                                                                            @switch($WeeklyLetter->sent)
                                                                                @case(0)
                                                                                {{__('admin.is_being_sent')}}
                                                                                    @break
                                                                                @case(1)
                                                                                {{__('admin.sent')}}

                                                                                    @break
                                                                                @default
                                                                                {{__('admin.send')}}


                                                                            @endswitch
                                                                        @else
                                                                        {{__('admin.send')}}

                                                                        @endif
                                                                        </button>
                                                                        <a class="btn btn-clean btn-bold" href="{{ route('admin.weekly_letters.index') }}">إلغاء</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{  asset('css/tagsinput.css') }}" rel="stylesheet"/>
@endpush
@push('scripts')
    <script src="{{ asset('js/tagsinput.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/mode/javascript/javascript.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/addon/selection/active-line.js') }}"></script>
    <script src="{{ asset('admin_assets/plugins/custom/codemirror/addon/edit/matchbrackets.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/custom/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/plugins/custom/codemirror/theme/dracula.css') }}">
    <script src="{{ asset('js/select2@4.1.0-rc.0_dist_js_select2.min.js') }}"></script>

    <style>
        .CodeMirror, .CodeMirror *{
            direction: ltr;
            text-align: left;
        }
        #refresh{
            left: 15px;
            font-size: 22px;
            top: 3px;
        }
        #submit_btn{
            cursor: not-allowed !important;
        }
    </style>
    <script>
        const reFresh = document.getElementById("refresh");

        reFresh.addEventListener("click", function() {
            $.ajax({
                type: "GET",
                url: "{{$show_link}}",
                dataType: "json",
                success: function(data) {
                    if (data.mail) {
                        $('#render_mail').html(data.mail);
                        $('#submit_form').removeClass('d-none');


                    }else{
                        $('.mail_in_processing').text(data.message);
                    }
                 },
                error: function(jqXHR, textStatus, errorThrown) {
                          console.error(textStatus, errorThrown);
                 }
            });

        });

    </script>
@endpush
