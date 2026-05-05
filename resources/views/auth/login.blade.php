@extends('app')
<style>
    header{
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.06);
    }
</style>
@section('title',trans('auth.login').' - ')
@push('javascript_navbar')
    <link href="{{ asset('plugins/iCheck/all.css')}}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="container">
        <div class="row mt-70 mb-70">
        @if( $settings->registration_active == 1 )
            <!-- Col MD -->
                <div class="col-md-8 offset-md-2">
                    <div class="icon-finished-looin text-center p-150">
                        <img src="{{ asset('img/sign-in.webp') }}" class="sign-in mb-4">
                        <h4 class="mb-3">
                            {{ trans('global.Session-finished-Log-in-again') }}
                        </h4>
                        <button class="btn btn-primary btn-lg btn-block auth-link" data-toggle="modal" data-type="login"
                                data-target="#login">{{ trans('auth.login') }}</button>
                    </div>
                </div>
                <!-- /COL MD -->
            @endif
        </div>
        <!-- row -->
        <!-- container wrap-ui -->
    </div>
@endsection
@push('javascript_navbar')
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
@endpush
