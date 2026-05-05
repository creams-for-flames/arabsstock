@extends('app')

@section('title')
@if(App::isLocale('en'))
{{ $response->title_en.' - ' }}
@else
{{ $response->title_ar.' - ' }}
@endif
@endsection

@section('content')
<div class="jumbotron md index-header jumbotron_set jumbotron-cover">
      <div class="container wrap-jumbotron position-relative">
        <h1 class="title-site">
            @if(App::isLocale('en'))
                <li class="active">{{ $response->title_en }}</li>
            @else
                <li class="active">{{ $response->title_ar }}</li>
            @endif
        </h1>
      </div>
    </div>

<div class="container margin-bottom-40">

<!-- Col MD -->
<div class="col-md-12">

	<ol class="breadcrumb bg-none">
          	<li><a href="{{ route('landPage') }}"><i class="glyphicon glyphicon-home myicon-right"></i></a></li>
            @if(App::isLocale('en'))
                <li class="active">{{ $response->title_en }}</li>
            @else
                <li class="active">{{ $response->title_ar }}</li>
            @endif
    </ol>
	<hr />

     <dl>
     	<dd>
     		<?php
                if (App::isLocale('en'))
                    echo html_entity_decode($response->content_en);
                else
                    echo html_entity_decode($response->content_ar);
            ?>
     	</dd>
     </dl>
 </div><!-- /COL MD -->

 </div><!-- container wrap-ui -->
@endsection

