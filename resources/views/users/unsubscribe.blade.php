@extends('app')
@section('content')
<div class="jumbotron profileUser index-header jumbotron_set m-5 jumbotron-cover-user bg-white">

    <div class="container wrap-jumbotron position-relative">
        <div class="container h-100">
            <div class="row h-100">
                <div class="col text-center">
                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading">  {{__("misc.Unsubscribe_from_the_newsletter")}} - {{$user->name??''}} !</h4>
                        <p>{{$message}}</p>
                        <hr>
                      </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
@endsection