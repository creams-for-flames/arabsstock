@extends('app')
@section('content')

 <div class="container margin-bottom-20 text-center" style="min-height: 300px;background-color: #fff">
 <div class="col-md-12 margin-bottom-20 margin-top-40">

@if( Auth::check() )

  <h2 class="{{App::isLocale('en')?'text-left':'text-right'}}"><i class="fa fa-file-text-o"></i>  @lang('global.invoices.title')  </h2> 

<div class="table-responsive">
<table class="table">
    <thead>
    <tr>
        <th>@lang('global.invoices.fields.date-of-payment')</th>
        <th>@lang('misc.type')</th>
        <th>@lang('misc.price')</th>
    </tr>
    </thead>
    <tbody>
    @if(count($invoices))    
      @foreach($invoices as $invoice)

      <tr>
          <td>{{ str_replace(['AM','PM'],[trans('misc.AM'),trans('misc.PM')],$invoice->created_at->format('Y-m-d | الساعة  h:i A')) }}</td>
          <td>{{ $invoice->video->title }}</td>
          <td>{{ $invoice->total}} @lang('misc.USD')  </td>

      </tr>
    @endforeach
   @else
    <tr>
      <td colspan="4"> {{trans('global.no_result_found')}} </td>  
    </tr>
   @endif

    </tbody>
</table>
</div>
</div>
</div>
@endif
@endsection
