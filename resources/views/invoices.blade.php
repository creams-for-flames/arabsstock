@extends('includes.profile')
@section('profile_content')
    <div class="container">
        <h3 class="pb-3">@lang('global.invoices.title')</h3>
        <p class="mb-2"><strong>{{ trans('global.billing-details') }} </strong></p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>@lang('global.invoices.fields.date-of-payment')</th>
                    <th>{{trans('misc.payment_description')}}</th>
                    <th>@lang('misc.price')</th>
                    <th>@lang('misc.download')</th>
                </tr>
                </thead>
                <tbody>
                @if(count($invoices))
                    @foreach($invoices as $invoice)

                        <tr>
                            <td>{{$invoice['created_at']}}</td>
                            @if($invoice['type'] === 'images')
                                <td>{{$invoice['title']}}</td>
                            @else
                                <td>{{$invoice['title']}}</td>
                            @endif
                            <td>{{$invoice['amount']}} @lang('misc.USD')</td>
                            <td><a target="_blank" href="{{cdn($invoice['invoice_file'])}}"><i
                                        class="fal fa-file-pdf"></i></a></td>
                        </tr>
                    @endforeach
                @else
                    <tr class="btn-block no_result_found ">
                        <td>
                            <h5 class="btn-block no-result no-result-mg">
                                {{trans('global.no_result_found')}} <i class="fal fa-exclamation-circle mr-1"></i>
                            </h5>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('javascript')
@endsection

