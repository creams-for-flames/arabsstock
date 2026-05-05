<?php $user = Auth::user(); ?>
@extends('includes.profile')
@section('profile_content')
    <div class="container">
        @section('title') {{ trans('users.account_settings') }} - @endsection
        <h3 class="pb-3">{{ trans('users.account_settings') }}</h3>
        <div class="wrap-center center-block">
            @if (Session::has('notification'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('notification') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <ul>
                @foreach ($errors->all() as $error)
                    <li>
                        <p style="color: red;">{{ $error }}</p>
                    </li>
                @endforeach
            </ul>
            <form action="{{ route('user.profile') }}" method="post" name="form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.full_name_misc') }}  </label>
                            <div class="input-with-gray">
                                <input oninvalid="setCustomValidity('{{trans('validation.fullname')}}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="text" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->name ) }}" name="full_name"
                                       placeholder="{{ trans('misc.full_name_misc') }}"
                                       title="{{ trans('misc.full_name_misc') }}" autocomplete="off">
                                <i class="fal fa-user"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->full_name)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.fullname') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label>{{ trans('auth.email') }}   </label>
                            <div class="input-with-gray">
                                <label class="form-control login-field custom-rounded" for="">{{$user->email}}</label>
                                {{-- <input type="email" oninvalid="setCustomValidity('{{trans('validation.email')}}')"
                                    oninput="setCustomValidity('')"
                                    required
                                    class="form-control login-field custom-rounded" value="{{$user->email}}" name="email" placeholder="{{ trans('auth.email') }}" title="{{ trans('auth.email') }}" autocomplete="off"> --}}
                                <i class="fal fa-envelope"></i></div>
                        </div>
                        @if(count($errors)>0 && $errors->get('email'))
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.email') }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.username_misc') }}   </label>
                            <div class="input-with-gray">
                                <label class="form-control login-field custom-rounded"
                                       for="">{{$user->username}}</label>
                                {{-- <input oninvalid="setCustomValidity('{{trans('validation.username')}}')"
                                    oninput="setCustomValidity('')"
                                    required
                                    type="text" class="form-control login-field custom-rounded" value="{{$user->username}}" name="username" placeholder="{{ trans('misc.username_misc') }}" title="{{ trans('misc.username_misc') }}" autocomplete="off"> --}}
                                <i class="fal fa-user"></i></div>
                        </div>
                        @if(count($errors)>0 && $errors->get('username'))
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.username') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="form-group has-feedback">
                            <label>{{ __('Mobile') }}  </label>
                            <div class="input-with-gray">
                                <input id="mobile" oninvalid="setCustomValidity('{{ __('Mobile') }}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="text" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->mobile ) }}" name="mobile" placeholder="{{ __('Mobile') }}"
                                       title="{{ __('Mobile') }}" autocomplete="off" style="direction: ltr">
                                <i class="fal fa-mobile"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->user)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.mobile') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <style>
                            .custom-control-label::before, .custom-control-label::after{
                                width: 1.2rem;
                                height: 1.2rem;}
                            .business_data{
                                display:none;
                            }
                        </style>
                        <div class="form-group has-feedback">
                            <div class="custom-control custom-checkbox mt-lg-2">
                                <input type="hidden" name="is_business" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_business" name="is_business" value="1" onchange="IsBusinessChanged(this)" {{ $user->is_business?'checked':'' }}>
                                <label class="custom-control-label" for="is_business">{{ __('views.is_business') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 business_data">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.company_name_misc') }}  </label>
                            <div class="input-with-gray">
                                <input oninvalid="setCustomValidity('{{trans('validation.company_name')}}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="text" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->company_name ) }}" name="company_name"
                                       placeholder="{{ trans('misc.company_name_misc') }}"
                                       title="{{ trans('misc.company_name_misc') }}" autocomplete="off">
                                <i class="fal fa-building"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->company_name)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.company_name') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6 business_data">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.company_address_misc') }}  </label>
                            <div class="input-with-gray">
                                <input oninvalid="setCustomValidity('{{trans('validation.company_address')}}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="text" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->company_address ) }}" name="company_address"
                                       placeholder="{{ trans('misc.company_address_misc') }}"
                                       title="{{ trans('misc.company_address_misc') }}" autocomplete="off">
                                <i class="fal fa-location-arrow"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->company_address)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.company_address') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6 business_data">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.company_email_misc') }}  </label>
                            <div class="input-with-gray">
                                <input
                                       oninvalid="setCustomValidity('{{trans('validation.company_email')}}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="email" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->company_email ) }}" name="company_email"
                                       placeholder="{{ trans('misc.company_email_misc') }}"
                                       title="{{ trans('misc.company_email_misc') }}" autocomplete="off">
                                <i class="fal fa-at"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->company_email)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.company_email') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6 business_data">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.company_tax_id_misc') }}  </label>
                            <div class="input-with-gray">
                                <input oninvalid="setCustomValidity('{{trans('validation.company_tax_id')}}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="text" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->company_tax_id ) }}" name="company_tax_id"
                                       placeholder="{{ trans('misc.company_tax_id_misc') }}"
                                       title="{{ trans('misc.company_tax_id_misc') }}" autocomplete="off">
                                <i class="fal fa-coins"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->company_tax_id)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.company_tax_id') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6 business_data">
                        <div class="form-group has-feedback">
                            <label>{{ trans('misc.company_phone_misc') }}  </label>
                            <div class="input-with-gray">
                                <input oninvalid="setCustomValidity('{{trans('validation.company_phone')}}')"
                                       oninput="setCustomValidity('')"
                                       required
                                       type="text" class="form-control login-field custom-rounded"
                                       value="{{ e( $user->company_phone ) }}" name="company_phone"
                                       placeholder="{{ trans('misc.company_phone_misc') }}"
                                       title="{{ trans('misc.company_phone_misc') }}" autocomplete="off">
                                <i class="fal fa-phone"></i>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->company_phone)
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.company_phone') }}
                            </p>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <style>
                            .custom-control-label::before, .custom-control-label::after{
                                width: 1.2rem;
                                height: 1.2rem;}
                        </style>
                        <div class="form-group has-feedback">
                            <div class="custom-control custom-checkbox mt-lg-2">
                                <input type="hidden" name="receive_newsletters" value="0">
                                <input type="checkbox" class="custom-control-input" id="receive_newsletters" name="receive_newsletters" value="1" {{ $user->receive_newsletters?'checked':'' }}>
                                <label class="custom-control-label" for="receive_newsletters">{{ __('views.accept_receive_newsletters') }}</label>
                            </div>
                        </div>
                        @if(count($errors)>0 && $errors->get('username'))
                            <p style="color: red;" class="help-block">
                                {{ trans('validation.username') }}
                            </p>
                        @endif
                    </div>
                </div>
                <button type="submit" id="buttonSubmit"
                        class="btn btn-primary btn-lg">{{ trans('misc.save_changes') }}</button>
            </form>
        </div>
    </div>
@endsection
@push('javascript_navbar')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"
            integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"/>
    <style>
        .iti__arrow{margin-right: 6px;margin-left: 0;}
        .iti{display: block;}
        .iti--allow-dropdown .iti__flag-container, .iti--separate-dial-code .iti__flag-container{left: 20px;}
        .iti--allow-dropdown input, .iti--allow-dropdown input[type=text], .iti--allow-dropdown input[type=tel], .iti--separate-dial-code input, .iti--separate-dial-code input[type=text], .iti--separate-dial-code input[type=tel]{padding-left: 70px;}
    </style>
    <script>
        var input = document.querySelector("#mobile");
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

        function IsBusinessChanged(checkbox) {
            if (checkbox.checked) {
                $('.business_data').show();
                $('.business_data input').attr('disabled', false);
            } else {
                $('.business_data').hide();
                $('.business_data input').attr('disabled', true);
            }
        }

        @if($user->is_business)
        $('.business_data').show();
        $('.business_data input').attr('disabled', false);
        @else
        $('.business_data').hide();
        $('.business_data input').attr('disabled', true);
        @endif
    </script>
@endpush
