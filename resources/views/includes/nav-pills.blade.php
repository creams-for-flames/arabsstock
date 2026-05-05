<div class="tabnavphoto">
	<div class="container">
<ul class="nav justify-content-center">
  <li @if(Request::is('latest')) class="nav-item" @endif><a href="{{route('latest')}}" class="nav-link">{{trans('misc.latest')}}</a></li>
{{--   <li @if(Request::is('popular')) class="nav-item" @endif><a href="{{route('popular')}}" class="nav-link">{{trans('misc.popular')}}</a></li>
  <li @if(Request::is('most/viewed')) class="nav-item" @endif><a href="{{route('most.viewed')}}" class="nav-link">{{trans('misc.most_viewed')}}</a></li>
  <li @if(Request::is('most/downloads')) class="nav-item" @endif><a href="{{route('most.downloads')}}" class="nav-link">{{trans('misc.most_downloads')}}</a></li> --}}
</ul>
		</div>
</div>