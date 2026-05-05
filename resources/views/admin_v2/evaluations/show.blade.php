@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="kt-portlet kt-portlet--tabs">
        <div class="alert alert-secondary">
                <a class="pr-2 pl-2" href="{{$index_url}}">
                        <i class="kt-menu__link-icon flaticon-comment"></i>
                        {{__('views.back')}}
                </a>
                <span class="pr-2 pl-2" > # {{ $evaluation->id }}</span>

            </div>
        <div class="kt-portlet__body">
                <div class="row">
                        <div class="col  text-capitalize  font-grid ">
                                <p class="text-justify">
                                {{ $evaluation->message }}
                                </p>
                        </div>
                </div>

        </div>
    </div>
</div>
<!-- end:: Content -->


@endsection

@push('css')
@endpush



