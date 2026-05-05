@guest
    <div class="modal fade" id="signup" tabindex="-1" role="dialog" aria-labelledby="signup" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered login-pop-form" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="modal-header-title">{{ __('auth.sign_up') }}</h4>
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
                    <!-- /.box-body -->
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
                                            <input id="emailR" value="{{ old('email') }}"
                                                oninvalid="setCustomValidity('{{trans('validation.email')}}')"
                                                oninput="setCustomValidity('')" required name="email"
                                                placeholder="{{ trans('auth.email') }}" type="email"
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
                                            <input id="mobileRegister" value="{{ old('Mobile') }}"
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
                                            <a href="{{ route('page.show',$page_data->slug) }}" style="color: #20d598"
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
                    <div class="mf-link pointer-primary" data-toggle="modal" data-target="#login" data-dismiss="modal">
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
        </div>
    </div>
    <div class="modal fade" id="login" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered login-pop-form" role="document">
            <div class="modal-content" id="loginrmodal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="modal-header-title">{{ trans('auth.sign_in') }}</h4>
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
                    <!-- /.box-body -->
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
                        <form id="loginForm" action="{{ route('login') }}" method="post" onsubmit="return false;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_url" value="{{ url()->previous() }}">
                            <div class="form-group">
                                <label>{{__('auth.email')}}</label>
                                <div class="input-with-gray">
                                    <input type="text" value="{{ old('email') }}" name="email"
                                        title="{{ trans('auth.email') }}"
                                        oninvalid="setCustomValidity('{{trans('validation.email')}}')"
                                        oninput="setCustomValidity('')" required autocomplete="off"
                                        class="form-control"
                                        placeholder="{{__('auth.email')}}">
                                    <i class="fal fa-envelope"></i></div>
                            </div>
                            <div class="form-group">
                                <label>{{ trans('auth.password') }}</label>
                                <div class="input-with-gray">
                                    <input name="password" type="password" title="{{ trans('auth.password') }}"
                                        oninvalid="setCustomValidity('{{trans('validation.password')}}')"
                                        oninput="setCustomValidity('')" required autocomplete="off"
                                        class="form-control"
                                        placeholder="*******">
                                    <i class="fal fa-unlock-alt"></i></div>
                            </div>
                            <div class="form-group">
                                <input @if( old( 'remember') ) checked="checked" @endif id="keep_login" class="no-show"
                                    name="remember" type="checkbox" value="1">
                                <span class="keep-login-title">{{ trans('auth.remember_me') }}</span>
                            </div>
                            <div class="form-group">
                                <button onclick="login()"
                                        class="btn btn-primary btn-lg btn-block">{{ trans('auth.sign_in') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mf-link">
                        <i class="fal fa-user"></i> {{trans('global.Haven_Account?')}}
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#signup"
                        data-dismiss="modal"> {{ __('auth.sign_up') }} </a>
                    </div>
                    <div class="mf-forget">
                        <a href="#" data-toggle="modal" data-target="#forgetPassword" data-dismiss="modal"><i
                                class="far fa-lock-alt"></i>{{trans('global.Forget_Password')}}
                        </a></div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="forgetPassword" tabindex="-1" role="dialog" aria-labelledby="login" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered login-pop-form" role="document">
            <div class="modal-content" id="registermodal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="modal-header-title">{{{ __('auth.password_recover') }}}</h4>
                    <div style="display: none" class="box-body mx-10 devide-wrap_validation">
                        <div class="alert alert-danger" id="dangerAlert">
                            <i class="glyphicon glyphicon-alert  "></i>
                            <div class="wrap_validation"></div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="login-form">
                        <form id="forgetForm" action="{{ route('password.email') }}" method="post"
                            onsubmit="return false;">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_url" value="{{ url()->previous() }}">
                            <div class="form-group">
                                <label>{{__('auth.email')}}</label>
                                <div class="input-with-gray">
                                    <input id="email" type="text" value="{{ old('email') }}" name="email"
                                        title="{{ trans('auth.email') }}"
                                        oninvalid="setCustomValidity('{{trans('validation.email')}}')"
                                        oninput="setCustomValidity('')" required autocomplete="off"
                                        class="form-control"
                                        placeholder="{{__('auth.email')}}">
                                    <i class="fal fa-envelope"></i></div>
                            </div>
                            <div class="form-group">
                                <button id="forgetpasswordbutton" onclick="forgetPass()"
                                        class="btn btn-primary btn-lg btn-block">{{ trans('auth.send') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="mf-link">
                        <i class="fal fa-envelope"></i> {{ trans('auth.There is a problem?') }}
                        <a href="{{route('technical-support')}}"
                        class="ml-2"> {{ trans('global.Technical_support') }} </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endguest

@if(!auth()->check())
<script> const reCAPTCHA_site_key = '{{ env('reCAPTCHA_site_key')}}'</script>
<script src="https://www.google.com/recaptcha/api.js?render=explicit&onload=onRecaptchaLoadCallback&hl={{ strtolower(app()->getLocale()) }}" async defer></script>
<script>
    
    var input = document.querySelector("#mobileRegister");
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
@endif