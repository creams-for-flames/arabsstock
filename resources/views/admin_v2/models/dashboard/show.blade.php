@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <!--begin::Portlet-->
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__body kt-portlet__body--fit">
            <!--begin: models -->
            <div class="container">
                    <div class="row justify-content-center">
                            <div class="col-12">
                                
                                   <h5 class="border border-success alert mt-2"> <strong>{{ __('views.Name') }} : </strong> {{ $models->name }} <strong class="pr-2 pl-2">{{ __('global.sex') }} : </strong> {{ $models->sex }} <strong class="pr-2 pl-2">{{ __('global.work_field') }} : </strong> {{ $models->work_field }}</h5> 
                                   <h5 class="border border-success alert mt-2"> <strong>{{ __('global.age') }} : </strong> {{ $models->age }}  <strong class="pl-2 pr-2">{{ __('global.birth_date') }} : </strong> {{ $models->birth_date }}</h5>
                                   <h5 class="border border-success alert mt-2"> <strong>{{ __('global.length') }} : </strong> {{ $models->length }}   <strong class="pl-2 pr-2">{{ __('global.weight') }} : </strong> {{ $models->weight }}</h5>
                                   <h5 class="border border-success alert mt-2"> <strong>{{ __('views.email') }} : </strong> {{ $models->email }} <strong class="pl-2 pr-2">{{ __('global.mobile') }} : </strong> {{ $models->mobile }}</h5>
                                   <h5 class="border border-success alert mt-2"> <strong>{{ __('global.skills') }} : </strong> {{ $models->skill??'_' }}</h5>
                                   <h5 class="border border-success alert mt-2">  <strong>{{ __('global.nationality') }} : </strong> {{ $models->nationality_casting->name_en??'_' }}  <strong class="pl-2 pr-2">{{ __('global.placeـofـresidence') }} : </strong> {{ $models->country->name_en??'_' }} <strong class="pl-2 pr-2">{{ __('global.city') }} : </strong> {{ $models->cit->name_en??'_' }}</h5>
                            </div>
                            <br/>
                            @if (isset($models->images))
                            
                            @foreach ($models->images as $file)
                            <div class="col-3 border border-success m-2 " style="height:250px; ">
                                    <a href="{{ asset($file->image) }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset($file->image) }}" alt="{{ $models->name??'' }}" title="{{ $models->name??'' }}" class="img-thumbnail h-100">
                                    </a>
                            </div>
                            @endforeach
                                    
                            @endif
                    </div>

            </div>
            <!--end: models -->
        </div>
    </div>
    <!--end::Portlet-->
</div>
<!-- end:: Content -->
@endsection

@push('css')
<link  href="{{  asset('css/tagsinput.css') }}"  rel="stylesheet" />

@endpush


@push('scripts')
<script src="{{ asset('js/tagsinput.js') }}"></script>


@endpush
