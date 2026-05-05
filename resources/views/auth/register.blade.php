@php
    $invitation=request('invitation')?\App\Models\Invitation::find(request('invitation')):null;
@endphp
@extends('app')
<style>
    header{
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.06);
    }
    .register-page{
        border: 1px solid #f3f4f5;
        box-shadow: black;
        box-shadow: 7px 4px 19px #f3f4f5;
    }
</style>
@section('title',trans('auth.sign_up').' - ')
@push('javascript_navbar')
    <link href="{{ asset('plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="container">
        <div class="row mt-70 mb-70">
            @if( $settings->registration_active == 1 )
                <div class="col-md-6 offset-md-2 mx-auto">
                    <div class="register-page" id="signup">
                        <h2 class="text-center">{{ __('auth.sign_up') }}</h2>
                        <div class="social-login">
                            <ul>
                                @if( $settings->facebook_login == 'on' )
                                    <li style="width:100%">
                                        <a href="{{ url('/auth/redirect/facebook')}}" class="btn connect-fb"><i
                                                class="fab fa-facebook-f"></i>{{__('auth.Login_with_Facebook')}}
                                        </a></li>
                                @endif @if( $settings->twitter_login == 'on')
                                    <li>
                                        <a href="#" class="btn connect-twitter"><i
                                                class="fab fa-twitter"></i>{{__('auth.Login_with_twitter')}}
                                        </a></li>
                                @endif
                                @if( $settings->google_login == 'on')

                                    <li style="width:100%">
                                        <a href="{{ url('/auth/redirect/google')}}" class="btn connect-google"><i
                                                class="fab fa-google"></i>{{__('auth.Login_with_Google')}}
                                        </a></li>
                                @endif
                            </ul>
                        </div>
                        <div class="devide-wrap">
                            <span>{{__('auth.or')}}</span>
                        </div>
                        <div style="display: none" class="box-body mx-10 devide-wrap_validation">
                            <div class="alert alert-danger" id="dangerAlert">
                                <i class="glyphicon glyphicon-alert  "></i>
                                <div class="wrap_validation"></div>
                            </div>
                        </div>
                        <div class="login-form">
                            <form id="regForm" action="{{ route('register') }}" method="post" onsubmit="return false;">
                                <input type="hidden" name="_recaptcha">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>{{__('auth.name')}}</label>
                                            <div class="input-with-gray">
                                                <input autocomplete="off"
                                                       oninvalid="setCustomValidity('{{trans('validation.fullname')}}')"
                                                       oninput="setCustomValidity('')" required name="name" type="text"
                                                       class="form-control" value="{{ old('name') }}"
                                                       placeholder="{{__('auth.name')}}">
                                                <i class="fal fa-user"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>{{ __('auth.email') }}</label>
                                            <div class="input-with-gray">
                                                <input id="emailR"
                                                       oninvalid="setCustomValidity('{{trans('validation.email')}}')"
                                                       oninput="setCustomValidity('')" required name="email"
                                                       placeholder="{{ trans('auth.email') }}" type="email"
                                                       @if($invitation)
                                                       readonly
                                                       value="{{ $invitation->email }}"
                                                       @endif
                                                       class="form-control">
                                                <i class="fal fa-envelope"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>{{ __('Mobile') }}</label>
                                            <div class="input-with-gray">
                                                <input id="mobileR" value="{{ old('Mobile') }}"
                                                       oninvalid="setCustomValidity('{{__('Mobile')}}')"
                                                       oninput="setCustomValidity('')" required name="mobile"
                                                       placeholder="{{ __('Mobile') }}" type="text"
                                                       class="form-control" style="direction: ltr">
                                                <i class="fal fa-mobile"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>{{ trans('auth.password') }}</label>
                                            <div class="input-with-gray">
                                                <input type="password" name="password" autocomplete="off"
                                                       oninvalid="setCustomValidity('{{trans('validation.password')}}')"
                                                       oninput="setCustomValidity('')" required class="form-control"
                                                       placeholder="*******">
                                                <i class="fal fa-unlock-alt"></i></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <label>{{ __('auth.confirm_password') }}</label>
                                            <div class="input-with-gray">
                                                <input type="password" name="password_confirmation" autocomplete="off"
                                                       oninvalid="setCustomValidity('{{trans('validation.attributes.password_confirmation')}}')"
                                                       oninput="setCustomValidity('')" required class="form-control"
                                                       placeholder="*******">
                                                <i class="fal fa-unlock-alt"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <style>#inline-signup-badge .grecaptcha-badge{margin: auto;}</style>
                                    <div id="inline-signup-badge"></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-1 mb-2 text-capitalize">
                                        {{ __('auth.agreement') }}
                                        @if (isset($pages) && count($pages) >0 )
                                            @foreach ($pages as $page_data)
                                                <a href="{{ route('page.show',$page_data->slug) }}"
                                                   style="color: #20d598"
                                                   target="_blank"
                                                   rel="noopener noreferrer">{{ $page_data->{'title_'.app()->getLocale()} }}</a>
                                                @if (!$loop->last)
                                                    {{ __('auth.and') }}
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-group">
                                            <button onclick="register()"
                                                    class="btn btn-primary btn-lg btn-block">{{ __('auth.sign_up') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="mf-link pointer-primary auth-link" data-toggle="modal" data-type="login"
                             data-target="#login">
                            <i class="fal fa-user"></i>{{__('auth.Already_Have_Account?_Sign_In')}}
                        </div>
                        @if($settings->link_privacy != '')
                            <div class="mf-forget">
                                <a href="{{route('page.show','privacy')}}"><i
                                        class="far fa-info-square"></i> {{ __('admin.privacy_policy') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@push('javascript_navbar')
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <script> const reCAPTCHA_site_key = '{{ env('reCAPTCHA_site_key')}}'</script>
    <script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=onRecaptchaLoadCallback&hl={{ strtolower(app()->getLocale()) }}" async defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var input = document.querySelector("#mobileR");
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
        });
    </script>
@endpush
