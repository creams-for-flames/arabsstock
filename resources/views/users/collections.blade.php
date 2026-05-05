@extends('includes.profile')
@section('profile_content')
<div class="container collections">
    @section('title'){{ $title }}@endsection
    <h3 class="pb-3">{{ trans('misc.collections') }} </h3>

    <ul class="nav">
    <li class="nav-item">
    <a class="nav-link active" href="#"><i class="fal fa-camera-alt"></i> {{ trans('misc.images') }} ({{number_format($data->total())}})</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="{{route('me.collections.videos')}}"><i class="fal fa-video"></i> {{ trans('misc.videos') }} </a>
    </li>

     <li class="nav-item">
    <a class="nav-link" href="{{route('me.collections.vectors')}}"><i class="fal fa-tilde fa-lg"></i> {{ trans('misc.vectors') }} </a>
    </li>
    </ul>
	<hr class="mt-0">

    <div class="center-row-section row">
        @if( $data->total() != 0 )
            @include('includes.collections')
            @if( $data->count() != 0  )
                <div class="container-paginator">
                    {{ $data->links() }}
                </div>
            @endif
       @else
            <div class="btn-block text-center pt-5">
                <i class="fal fa-exclamation-circle"></i>
            </div>

            <h3 class="btn-block text-center no-result no-result-mg">{{ trans('misc.no_results_found') }}
            </h3>
       @endif

   </div>

   {{-- <div class="row">
        @if( $data->total() != 0 )
            @include('video.includes.collections')
            @if( $dataVideo->count() != 0  )
                <div class="container-paginator">
                    {{ $dataVideo->links() }}
                </div>
            @endif
        @else
            <div class="btn-block text-center pt-5">
                <i class="fal fa-exclamation-circle"></i>
            </div>

            <h3 class=" text-center no-result no-result-mg">{{ trans('misc.no_results_found') }}
            </h3>
        @endif

    </div>






 --}}



</div>

@endsection
@section('javascript')
@endsection
