<?php $user = Auth::user(); ?>
@extends('includes.profile')
@section('profile_content')
@section('title') {{ trans('auth.password') }} - @endsection
<div class="container">
    <h3 class="pb-3">{{ trans('auth.password') }}</h3>
    <div class="wrap-center center-block">
        @if (Session::has('success'))
        <div class="alert alert-success btn-sm alert-fonts" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('success') }}
        </div>
        @endif
            @if (Session::has('incorrect_pass'))
        <div class="alert alert-danger btn-sm alert-fonts" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ Session::get('incorrect_pass') }}
        </div>
        @endif
        @include('errors.errors-forms')

        <form action="{{ route('account.password') }}" method="post" name="form">
            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group has-feedback">
                        <label>{{ trans('misc.old_password') }}</label>
                        <div class="input-with-gray">
                            <input type="password" class="form-control" name="old_password" placeholder="{{ trans('misc.old_password') }}" title="{{ trans('misc.old_password') }}" autocomplete="off" />
                            <i class="fal fa-unlock-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group has-feedback">
                        <label>{{ trans('misc.new_password') }}</label>
                        <div class="input-with-gray">
                            <input type="password" class="form-control" name="password" placeholder="{{ trans('misc.new_password') }}" title="{{ trans('misc.new_password') }}" autocomplete="off" />
                            <i class="fal fa-unlock-alt"></i>
                        </div>
                    </div>
                </div>
                </div>
                <button type="submit" id="buttonSubmit" class="btn btn-primary btn-lg">{{ trans('misc.save_changes') }}</button>
        </form>
    </div>
    @endsection
</div>
