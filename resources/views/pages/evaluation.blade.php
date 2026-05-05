@extends('app')
@section('title')
    {{trans('global.tell_us_about_your_experience_at_arabstock')}}
@endsection
@section('content')
    <div class="search-header jumbo-banner" data-overlay="6" style="">
        <div class="container-fluid">
            <div class="col-lg-12 col-md-12">
                <h1 class="title-site">
                    {{trans('global.tell_us_about_your_experience_at_arabstock' )}}
                </h1>
            </div>
        </div>
    </div>

    <div class="container mt-50 mb-50">
        <div class="row">
            <div class="col-12 col-md-10 offset-md-1">
                <div class="contact-title mb-5">
                    <i class="far fa-comment"></i>
                    {{-- <i class="fa fa-commenting-o" aria-hidden="true"></i> --}}
                    <h2>{{trans('global.we_hope_you_can_help_us_to_know_more_about_your_impression_to_develop_arabstock')}}</h2>
                    <h4>{{trans('global.can_you_tell_us_by_mouth_at')}}</h4>
                    <h5>{{ trans('global.site_and_method_of_use_content_quality_packages_purchase_and_payment_technical_support_and_communication') }}</h5>
                </div>
                @if (count($errors) > 0)
                    <ul style="border: 1px solid #e02222; background-color: white">
                        @foreach ($errors->all() as $error)
                            <li style="color: #e02222; margin: 15px">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
                @if(session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif
                <div id="modal_body_error"></div>
                <div class="bd-example">
                    <form id="ticketFormEvaluation" method="post">
                        @csrf
                        <input type="hidden" name="_recaptcha">
                        <div id="wrap_validation"></div>
                        <div class="form-row">
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
                                    <button type="submit" name="btnSubmit"
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
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('reCAPTCHA_site_key')}}"></script>
    <script>
        $('#ticketFormEvaluation [type="submit"]').click('click', function (e) {
            e.preventDefault();
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('app.recaptcha.site_key') }}', {action: 'evaluation'}).then(function (token) {
                    $('#ticketFormEvaluation input[name="_recaptcha"]').val(token);
                    $('#ticketFormEvaluation').submit();
                });
            });
        })
    </script>
@endpush
