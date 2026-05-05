@extends('app')

@section('content')

<div class="container margin-bottom-20 text-center">
	

</div>
<div class="container margin-bottom-40">
  <div class="row">
 <div class="col-md-12 Value-priced-pay">
         @if($plan->type != 'package')
         <h3 class="alert alert-danger"> {!! trans('global.subscriptions.title_subscription') !!}  </h3> 
         @else
         <h3 class="alert alert-danger"> {!! trans('global.subscriptions.title_package') !!}  </h3> 
         @endif
  
<div class="pay-methods">

     <div class="row">
       <div class="col-md-8 col-md-offset-2">
        <div id="ajaxRespone">   </div>
        <div id="loading-form"> <h3> {{trans('global.subscriptions.loading')}} </h3> </div>
         <div id="dropin-container"></div>
          <button id="payment-button" disabled style="display: none;" class="btn btn-primary btn-flat" type="submit">{{$plan->type == 'package'? trans('global.subscriptions.pay_now'): trans('global.subscriptions.subscribe')}}</button>

       </div>
     </div>
 
</div>
  
 </div>
 
</div>
</div>


@endsection

@section('javascript')
 
  <script>
 
 
  </script>
 
@endsection
