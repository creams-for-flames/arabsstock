@extends('app') @section('content')

<section class="cart mt-50 mb-50">
    <div class="container">
        <div class="row">

            <div class="col-12 col-sm-8 col-md-8 col-lg-8">
                <h3>{{trans_choice('global.Your items in cart',$cart->count())}}</h3>

                @foreach($cart as $video)
                <div class="row card mb-3">
                    <div class="col-12 col-sm-4 col-md-4 col-lg-4 media">
                       <a href="{{$video->parent->post_link}}"> <img src="{{cdn($video->thumbnail_sm)}}" /></a>
                    </div>
                    <div class="col-12 col-sm-8 col-md-8 col-lg-8 media-body">

                    <div class="row mt-1 mb-2">
                          <div class="col-9">  <a href="{{$video->parent->post_link}}" class="title-video-cart">{{$video->parent->title}} </a> </div>
                          <p class="price col-3"><span> $ {{$video->price}}  </span></p>
                    </div>

                        <p class="mt-2 mb-3">{{$video->width}} × {{$video->height }} px | {{$video->size/1000}} MB | {{$video->type}}</p>

                        <a href="{{route('video.cart.remove',$video->id)}}" class="btn btn-secondary"><i class="fas fa-trash"></i> {{ trans('global.Remove') }} </a>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="col-12 col-sm-4 col-md-4 col-lg-4 summary">
                <h3>{{trans('global.Order summary')}}</h3>
                <div class="summary-card">
                    <div class="row justify-content-between">
                        <div class="col-6">
                            <span class="summary-card-total">{{ trans('global.clips-billed-on-card',['count' => $cart->count()])}}</span>
                        </div>
                        <div class="col-6">
                            <p class="price">$ {{$sum_total_amount}}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        @if($cart->count() > 0)
                        <form action="{{route('video.cart.checkout')}}" id="checkout-form" method="post">
                            {!! csrf_field() !!}
                        </form>
                        <a href="javascript:;" class="btn btn-block btn-primary" onclick="document.getElementById('checkout-form').submit();">{{trans('global.Checkout')}}</a>
                        @endif
                        <a href="{{route('video.home')}}" class="btn btn-block btn btn-dark">{{trans('global.Continue Shopping')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection



<script type="text/javascript">
  

</script>