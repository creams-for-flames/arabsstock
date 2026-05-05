@extends('admin_v2.layout.app')

@section('content')
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">


    <div class="content-wrapper">

    <section class="content-header">

    <h3 class="page-title">@lang('global.plans.title')</h3>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('global.app_view')
        </div>

        <div class="panel-body table-responsive">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped">

                        <tr>
                            <th>@lang('global.plans.fields.title_en')</th>
                            <td field-key='title'>{{ $plan->title_en }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.plans.fields.title_en')</th>
                            <td field-key='title'>{{ $plan->title_ar }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.plans.fields.price')</th>
                            <td field-key='price'>{{ $plan->price }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.plans.fields.downloads-count')</th>
                            <td field-key='downloads_count'>{{ $plan->downloads_count }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.plans.fields.type')</th>
                            <td field-key='type'>{{ $plan->type }}</td>
                        </tr>
                        <tr>
                            <th>@lang('global.plans.fields.status')</th>
                            <td field-key='status'>{{ $plan->status? 'Active' : 'Inactive' }}</td>
                        </tr>
                    </table>
                </div>
            </div><!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">

<li role="presentation" class="active"><a href="#user_plans" aria-controls="user_plans" role="tab" data-toggle="tab">User plans</a></li>
<li role="presentation" class=""><a href="#user_payment" aria-controls="user_payment" role="tab" data-toggle="tab">User payment</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

<div role="tabpanel" class="tab-pane active" id="user_plans">
<table class="table table-bordered table-striped {{ count($user_plans) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.user-plans.fields.plan')</th>
                        <th>@lang('global.user-plans.fields.user')</th>
                        <th>@lang('global.user-plans.fields.date-start')</th>
                        <th>@lang('global.user-plans.fields.date-end')</th>
                        <th>@lang('global.user-plans.fields.days-remaining')</th>
                        <th>@lang('global.user-plans.fields.download-remaining')</th>
                        <th>@lang('global.user-plans.fields.status')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
        </tr>
    </thead>

    <tbody>
        @if (count($user_plans) > 0)
            @foreach ($user_plans as $user_plan)
                <tr data-entry-id="{{ $user_plan->id }}">
                    <td field-key='plan'>{{ $user_plan->plan->title ?? '' }}</td>
                                <td field-key='user'>{{ $user_plan->user->name ?? '' }}</td>
                                <td field-key='date_start'>{{ $user_plan->starts_at }}</td>
                                <td field-key='date_end'>{{ $user_plan->ends_at }}</td>
{{--                                 <td field-key='days_remaining'>{{ $user_plan->days_remaining }}</td>
 --}}                                <td field-key='download_remaining'>{{ $user_plan->download_remaining }}</td>
                                <td field-key='status'>{{ $user_plan->status }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.user_plans.restore', $user_plan->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.user_plans.perma_del', $user_plan->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                     </td>
                                @else
                                <td>
                                    @can('user_plan_view')
                                    <a href="{{ route('admin.user_plans.show',[$user_plan->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('user_plan_edit')
                                    <a href="{{ route('admin.user_plans.edit',[$user_plan->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('user_plan_delete')
                                        {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.user_plans.destroy', $user_plan->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
{{--
<div role="tabpanel" class="tab-pane " id="user_payment">
<table class="table table-bordered table-striped {{ count($user_payments) > 0 ? 'datatable' : '' }}">
    <thead>
        <tr>
            <th>@lang('global.user-payment.fields.user')</th>
                        <th>@lang('global.user-payment.fields.payment')</th>
                        <th>@lang('global.user-payment.fields.payment-method')</th>
                        <th>@lang('global.user-payment.fields.payment-date')</th>
                        <th>@lang('global.user-payment.fields.type')</th>
                        <th>@lang('global.user-payment.fields.plan')</th>
                        <th>@lang('global.user-payment.fields.image')</th>
                        @if( request('show_deleted') == 1 )
                        <th>&nbsp;</th>
                        @else
                        <th>&nbsp;</th>
                        @endif
        </tr>
    </thead>

    <tbody>
        @if (count($user_payments) > 0)
            @foreach ($user_payments as $user_payment)
                <tr data-entry-id="{{ $user_payment->id }}">
                    <td field-key='user'>{{ $user_payment->user->name ?? '' }}</td>
                                <td field-key='payment'>{{ $user_payment->id }}</td>
                                <td field-key='payment_method'>{{ $user_payment->payment_method->title_en ?? '' }}</td>
                                <td field-key='payment_date'>{{ $user_payment->created_at }}</td>
                                <td field-key='type'>{{ $user_payment->type }}</td>
                                <td field-key='plan'>{{ $user_payment->plan->title ?? '' }}</td>
                                <td field-key='image'>{{ $user_payment->image->thumbnail ?? '' }}</td>
                                @if( request('show_deleted') == 1 )
                                <td>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'POST',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.user_payments.restore', $user_payment->id])) !!}
                                    {!! Form::submit(trans('global.app_restore'), array('class' => 'btn btn-xs btn-success')) !!}
                                    {!! Form::close() !!}
                                                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.user_payments.perma_del', $user_payment->id])) !!}
                                    {!! Form::submit(trans('global.app_permadel'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                                                </td>
                                @else
                                <td>
                                    @can('user_payment_view')
                                    <a href="{{ route('admin.user_payments.show',[$user_payment->id]) }}" class="btn btn-xs btn-primary">@lang('global.app_view')</a>
                                    @endcan
                                    @can('user_payment_edit')
                                    <a href="{{ route('admin.user_payments.edit',[$user_payment->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                    @endcan
                                    @can('user_payment_delete')
{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                        'route' => ['admin.user_payments.destroy', $user_payment->id])) !!}
                                    {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                                @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="12">@lang('global.app_no_entries_in_table')</td>
            </tr>
        @endif
    </tbody>
</table>
</div>
--}}
</div>

            <p>&nbsp;</p>

            <a href="{{ route('admin.plans.index') }}" class="btn btn-default">@lang('global.app_back_to_list')</a>
        </div>
    </div>
    </section>
    </div>


</div>
<!-- end:: Content -->
@endsection

@push('css')
@endpush


@push('scripts')
@endpush

