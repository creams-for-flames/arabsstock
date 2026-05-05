@extends('app')
@section('title')
    @if(App::isLocale('en'))
        {{ 'Technical Support - ' }}
    @else
        {{ 'الدعم الفني - ' }}
    @endif
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <h1 class="title-site">
                    {{trans('global.Technical_support' )}}
                </h1>
            </div>
        </div>
    </div>
    <div class="container mt-50 mb-50">
        <div class="row">
            <div class="col-12 col-md-10 offset-md-1">
                <div class="contact-title mb-5">
                    <i class="far fa-headset"></i>
                    <h2>{{trans('global.Did_encounter_problem _subscription_payment_download_any_other_inquiries')}}</h2>
                    <h4>{{trans('global.contact_through_following_form')}}</h4>
                </div>
                <div id="modal_body_error"></div>
                <div class="bd-example">
                    <form id="ticketForm" method="post" onsubmit="return false;">
                        <input type="hidden" name="_recaptcha">
                        @csrf
                        <div id="wrap_validation"></div>
                        <div class="form-row">
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4 ">
                                <div class="form-group">
                                    <label>{{trans('global.Name' )}} <span>*</span></label>
                                    <div class="input-with-gray">
                                        <input type="text" value="{!! $auth_user->name??'' !!}" name="name" title="First Name"
                                               oninvalid="setCustomValidity('The Name Is Requerd')"
                                               oninput="setCustomValidity('')" required autocomplete="off"
                                               class="form-control" placeholder="{{trans('global.Name' )}}"
                                        />
                                        <i class="fal fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.Email' )}} <span>*</span></label>
                                    <div class="input-with-gray">
                                        <input type="text" value="{!! $auth_user->email??'' !!}" name="email" title="Email"
                                               oninvalid="setCustomValidity('The Email Is required')"
                                               oninput="setCustomValidity('')" required autocomplete="off"
                                               class="form-control" placeholder="{{trans('global.Email' )}}"/>
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label>{{trans('global.Mobile' )}}</label>
                                    <div class="input-with-gray">
                                        <input type="tel" value="{!! $auth_user->mobile??'' !!}" name="mobile" title="{{trans('global.Mobile' )}}"
                                               autocomplete="off" class="form-control"
                                               placeholder="{{trans('global.Mobile' )}}"/>
                                        <i class="fal fa-phone-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="" for="inlineFormCustomSelect">{{trans('global.Message' )}}
                                        <span>*</span> </label>
                                    <div class="input-with-gray">
                                        <textarea class="form-control" name="message" aria-label="With textarea"
                                                  required placeholder="{{trans('global.Message' )}}"
                                                  rows="6"></textarea>
                                        <i class="fal fa-file-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" style="margin-top:40px;">
                                <div class="form-group">
                                    <button type="submit" name="btnSubmit" onclick="send_ticket(this)"
                                            class="btn btn-primary btn-lg btn-block">{{trans('global.Send' )}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('javascript_navbar')
<!-- Start of LiveChat (www.livechatinc.com) code -->
<script type="text/javascript">
    window.__lc = window.__lc || {};
    window.__lc.license = 13510821;
    ;(function(n,t,c){function i(n){return e._h?e._h.apply(null,n):e._q.push(n)};
    var e={_q:[],_h:null,_v:"2.0",on:function(){i(["on",c.call(arguments)])},once:function(){
    i(["once",c.call(arguments)])},off:function(){i(["off",c.call(arguments)])},
    get:function(){if(!e._h)throw new Error("[LiveChatWidget] You can't use getters before load.");
    return i(["get",c.call(arguments)])},call:function(){i(["call",c.call(arguments)])},init:function(){
    var n=t.createElement("script");
    n.async=!0,n.type="text/javascript",
    n.src="https://cdn.livechatinc.com/tracking.js",t.head.appendChild(n)}};
    !n.__lc.asyncInit&&e.init(),n.LiveChatWidget=n.LiveChatWidget||e}(window,document,[].slice))
  </script>
  <noscript>
  <a href="https://www.livechatinc.com/chat-with/13510821/" rel="nofollow">Chat with us</a>,
  {{-- powered by <a href="https://www.livechatinc.com/?welcome" rel="noopener nofollow" target="_blank">LiveChat</a> --}}
  </noscript>
  <!-- End of LiveChat code -->
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('reCAPTCHA_site_key')}}"></script>
    <script>
        function send_ticket(btn) {
            $(btn).attr('disabled',true)
            var form = $("#ticketForm");
            var url = form.attr('action');
            var isValid = $("#ticketForm")[0].checkValidity();

            if (!isValid) {
                $("#ticketForm")[0].reportValidity();
                return false;
            }
            var errors = '';
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('app.recaptcha.site_key')}}', {action: 'support'}).then(function (token) {
                    $('#ticketForm input[name="_recaptcha"]').val(token);
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: form.serialize(), // serializes the form's elements.
                        success: function (data) {
                            $(btn).attr('disabled',false);
                            form.trigger("reset");
                            $('#ticketForm #wrap_validation').html('<p class="alert alert-success"> ' + data.message + ' </p>');
                        },
                        error: function (err) {
                            $(btn).attr('disabled',false);
                            $('#ticketForm #wrap_validation').html('');
                            var errors = '<div class="alert alert-danger"><ul>';
                            if (err.status == 422) {
                                $.each(err.responseJSON.errors, function (index, value) {
                                    errors += '<li>' +
                                        '' + value[0] + '</i>' +
                                        '</li> ';
                                });
                            } else {
                                errors += " There Problem , try again ";
                            }
                            errors += '</ul></div>';
                            $('#ticketForm #wrap_validation').append(errors);
                        },
                    });
                });
            });
        }
    </script>
@endpush
