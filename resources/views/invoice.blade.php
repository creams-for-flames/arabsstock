@extends('app')
@section('content')
 <div class="container margin-bottom-20 margin-top-20 text-center" style="min-height: 300px;background-color: #fff">
 <div class="col-md-12 margin-bottom-20 margin-top-40">


    @if( Auth::check() )

  <h1 class="{{App::isLocale('en')?'text-left':'text-right'}}">@lang('global.invoices.fields.head')</h1> 
   <div class="table-responsive">
  <table class="table table-striped table-hover table-bordered">
    <thead>
    <tr>
        <th>@lang('global.invoices.fields.unique-code')</th>
        <th>@lang('global.invoices.fields.date-of-payment')</th>
        <th>@lang('global.invoices.fields.price')</th>
        <th>@lang('global.invoices.fields.status')</th>
        <th>@lang('global.invoices.fields.plan')</th>
    </tr>
   </thead>
  <tbody>
    <tr>
        <td field-key='unique_code'>{{ $invoice->unique_code }}</td>
        <td field-key='created_at'>{{ $invoice->created_at }}</td>
        <td field-key='status'>{{ $invoice->total }}</td>
        <td field-key='status'>
            @if($invoice->is_paid)
        @lang('global.invoices.fields.completed') 

            @else
       @lang('global.invoices.fields.Not completed') 
            @endif
        </td>
        <td field-key='plan'>{{ $invoice->plan->title_en ?? '' }}</td>
    </tr>
    </tbody>
</table>


    @endif
    </div>
    </div>
    </div>
@endsection