@extends('video.admin.layout')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">
    <h3 class="page-title">@lang('global.invoices.title')</h3>



    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($invoices) > 0 ? 'datatable' : '' }} @can('invoice_delete') @if ( request('show_deleted') != 1 ) dt-select @endif @endcan">
                <thead>
                    <tr>
                        @can('invoice_delete')
                            @if ( request('show_deleted') != 1 )<th style="text-align:center;"><input type="checkbox" id="select-all" /></th>@endif
                        @endcan

                        <th>@lang('global.invoices.fields.unique-code')</th>
                        <th>@lang('global.invoices.fields.date-of-payment')</th>
                        <th>@lang('global.invoices.fields.price')</th>
                        <th>@lang('global.invoices.fields.status')</th>
                        <th>@lang('global.invoices.fields.user')</th>
                        <th>@lang('global.video')</th>

                    </tr>
                </thead>

                <tbody>
                    @if (count($invoices) > 0)
                        @foreach ($invoices as $invoice)
                            <tr data-entry-id="{{ $invoice->id }}">
                                @can('invoice_delete')
                                    @if ( request('show_deleted') != 1 )<td></td>@endif
                                @endcan

                                <td field-key='unique_code'>{{ $invoice->unique_code }}</td>
                                <td field-key='created_at'>{{ $invoice->created_at }}</td>
                                    <td field-key='status'>{{ $invoice->total }}</td>
                                    <td field-key='status'>
                                    @if($invoice->is_paid)
                                        Complete

                                    @else
                                        Not completed
                                    @endif
                                    </td>
                                <td field-key='user'>{{ $invoice->user->name ?? '' }}</td>
                                    <td>
                                        <a href="{{ $invoice->video->parent->post_link }}"><img class="img-fluid" width="150px" src="{{ url($invoice->video->thumbnail)}}" alt=""></a>
                                    </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

        </section>

    </div>
@stop

@section('javascript')
    <script>
        @can('invoice_delete')
            @if ( request('show_deleted') != 1 ) window.route_mass_crud_entries_destroy = '{{ route('admin.invoices.mass_destroy') }}'; @endif
        @endcan

    </script>
@endsection
