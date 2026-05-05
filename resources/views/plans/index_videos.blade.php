@extends('app')
@section('title')
@if(App::isLocale('en'))
    {{trans('misc.prices and packages').' - '}}
@else
    {{trans('misc.prices and packages').' - '}}
@endif
@endsection

@section('description_custom'){{  trans('misc.prices and packages').' - '}}
@endsection

@section('content')
@include('includes.searchbar')
<div class="container-fluid mb-50">
    <div class="mb-5 mt-50">
        <h2>{{trans('global.videoforproject')}} </h2>
        <p>{!! trans('global.headtextplan_video') !!}</p>
    </div>

        <div class="row">
            <div class="col-12 col-md-5 col-lg-5">
                <div class="card pt-2">
                    <div class="card-body py-0">
                        <!-- <div class="row"> -->
                        <div>
                            <div class="col-md-12">
                                @if(App::isLocale('en'))
                                <h3>{{trans('global.Value-priced')}} {{$plantitle}} {{trans('global.packages')}}</h3>
                                @else
                                <h3>{{trans('global.packages')}} {{$plantitle}} {{trans('global.Value-priced')}}</h3>
                                @endif
                                <p>{{trans('global.textdiss')}}</p>
                            </div>
                            <form method="get" id="options_list_monthly" action="{{route('plan.subscribe')}}">
                                <div class="col-md-12 mt-5">
                                    <div class="row">
                                        <input type="hidden" name="type" value="video">
                                        <?php $once = !(count($your_plans_monthly)>0) ?>
                                        @foreach($plans as $plan)
                                        <div class=" plan-option col-12 col-sm-6 col-md-6 col-lg-6 px-2 {{in_array($plan->id,$your_plans_monthly)?'':'plan-item-monthly'}} {{!in_array($plan->id,$your_plans_monthly) && $once ? 'active':''}}">
                                            <div class="row plan-option-select">
                                                <input required type="radio" id="{{$plan->id}}-option" value="{{$plan->id}}" name="plan_id" {{in_array($plan->id,$your_plans_monthly)? 'disabled':''}} {{!in_array($plan->id,$your_plans_monthly)
                                                && $once? 'checked="checked"':''}} style="display: none;">

                                                <input type="hidden" name="type" value="video">

                                                <label for="{{$plan->id}}-option" class="plan"></label>
                                                <div class="col-12 bold-text">
                                                    <p>
                                                        {{$plan->downloads_count}}  {{trans('global.videos')}}<br />
                                                        <span> {{$plan->price}} <br> {{trans('misc.USD')}}</span>
                                                    </p>
                                                </div>
                                                <div class="col-12">
                                                    <p>
                                                        {{number_format($plan->price/$plan->downloads_count,2,'.','')}} {{trans('misc.USD')}} <br />
                                                        <span>{{trans('global.pervideo')}}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $once = false; ?>
                                        @endforeach
                                        <div class="col-md-12 px-2 mt-3">
                                        @if(Auth::check())

                                            <input value="{{trans('global.subscribe-now')}}" type="submit" class="btn btn-lg btn-block btn-primary {{count($plans) === count($your_plans_monthly) ? 'disabled' : '' }}" {{count($plans) === count($your_plans_monthly) ? 'disabled' : '' }} />
                                            @else <button type="button" data-toggle="modal" data-target="#login" class="btn btn-lg btn-block btn-primary">{{trans('global.subscribe-now')}}</button> @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
             </div>
            <div class="col-12 col-md-7 col-lg-7 mt-4 mt-md-0 mt-lg-0">
                <div class="card pt-2">
                    <div class="card-body py-0">
                        <!-- <div class="row"> -->
                        <div>
                            <div class="col-md-12">
                                <h3>{{trans('global.prepaidvideo')}}</h3>
                                <p>( {{trans('global.download_through_year')}} )</p>

                                <?php $once = !(count($your_plans_package)>0) ?>
                            </div>
                            <form method="get" id="options_list_package" action="{{route('plan.subscribe-package')}}">
                                <div class="col-md-12 mt-5">
                                    <div class="row">
                                        <input type="hidden" name="type" value="video">
                                    @foreach($image_plans as $image_plan)
                                    <div class="plan-option col  px-2 {{in_array($image_plan->id,$your_plans_package)?'plan-item-package active':'plan-item-package'}}  {{!in_array($image_plan->id,$your_plans_package) && $once?'active':''}}">
                                        <div class="row plan-option-select">
                                            <input required type="radio" id="{{$image_plan->id}}-option" value="{{$image_plan->id}}" name="plan_id" {{in_array($image_plan->id,$your_plans_package)? 'checked="checked"':''}} {{!in_array($image_plan->id,$your_plans_package) && $once? 'checked="checked"':''}} style="display: none;">
                                            <label for="{{$image_plan->id}}-option" class="plan"></label>
                                            <div class="col-12 bold-text">
                                                <p>
                                                    {{$image_plan->downloads_count}}  {{trans('global.videos')}}<br />
                                                    <span>{{$image_plan->price}} <br> {{trans('misc.USD')}}</span>
                                                </p>
                                            </div>
                                            <div class="col-12">
                                                <p>
                                                    {{number_format($image_plan->price/$image_plan->downloads_count,2,'.','')}} {{trans('misc.USD')}} <br />
                                                    <span>{{trans('global.pervideo')}}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $once = false; ?>
                                    @endforeach
                                    <div class="col-md-12 px-2 mt-3">
                                     @if(Auth::check())

                                        <input value="{{trans('global.subscribe-now')}}" type="submit" class="btn btn-lg btn-block btn-primary" />
                                        @else(Auth::guest()) <button id="btnLogin" type="button" data-toggle="modal" data-target="#login" class="btn btn-lg btn-block btn-primary">{{trans('global.subscribe-now')}}</button>

                                    @endif
                                    </div>
                                </div>
                            </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="contact-title mt-5">
                    <i class="far fa-headset"></i>
                    <h3>{{trans('global.Did_encounter_problem _subscription_payment_download_any_other_inquiries')}}</h3>
                    <h4><a href="{{route('technical-support')}}" class="color-primary">{{trans('global.Contact_now')}}</a></h4>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@section('javascript')
<script type="text/javascript">

    jQuery(document).ready(function( $ ) {



     // $('form#options_list_monthly').on('submit',function(event){
     //  let plan_id = $(this).find('input[name="plan_id_month"]:checked').val();
     //  alert(plan_id);
     //  return false;
     // });


     $('.plan-item-monthly').on('click',function(event){
        $('.plan-item-monthly').removeClass('active');
        $(this).addClass('active');
        // $('#options_list-monthly').find('input:radio').removeAttr('checked');
        $(this).find('input:radio').prop('checked',true);

     });

     $('.plan-item-package').on('click',function(event){
        $('.plan-item-package').removeClass('active');
        $(this).addClass('active');
        // $('#options_list-package').find('input:radio').removeAttr('checked');
        $(this).find('input:radio').prop('checked',true);

     });

     $('label.plan').on('click',function(event){
       $input = $(this).prev('input');
       if($input.attr('disabled'))
      swal({
        title: "{{trans('global.user-plans.you-are-subscribed-to-plan-already')}}",
        type: "info",
        confirmButtonText: "{{ trans('users.ok') }}"
       });
       // else
       //  alert('enabled');

     });


    });

   {{--
    @if (session('your_are_subscribed_already'))
      swal({
        title: "{{trans('global.user-plans.you-are-subscribed-to-plan-already')}}",
        type: "info",
        confirmButtonText: "{{ trans('users.ok') }}"
       });
    @endif


    @if (session('success_verify'))
      swal({
        title: "{{ trans('misc.welcome') }}",
        text: "{{ trans('users.account_validated') }}",
        type: "success",
        confirmButtonText: "{{ trans('users.ok') }}"
    });
    @endif

    @if (session('error_verify'))
     swal({
        title: "{{ trans('misc.error_oops') }}",
        text: "{{ trans('users.code_not_valid') }}",
        type: "error",
        confirmButtonText: "{{ trans('users.ok') }}"
    });
    @endif

--}}

</script>
@endsection
