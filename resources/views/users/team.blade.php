<?php $user = Auth::user(); ?>
@extends('includes.profile')
@section('profile_content')
    <link rel="stylesheet" href="{{ asset('css/team.css') }}?v={{ config('app.assets.version') }}">
    <div class="container team-page">
        @section('title',__('Team'))
        <div class="container">
            <h3 class="pb-3">{{__('Team')}}</h3>
            <ul class="nav mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button
                        class="mb-0 nav-link active"
                        id="members-tab"
                        data-toggle="pill"
                        data-target="#members"
                        type="button"
                        role="tab"
                        aria-controls="members"
                        aria-selected="true"
                    >
                        {{ __('admin.members') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button
                        class="mb-0 nav-link"
                        id="subscriptions-tab"
                        data-toggle="pill"
                        data-target="#subscriptions"
                        type="button"
                        role="tab"
                        aria-controls="subscriptions"
                        aria-selected="true"
                    >
                        {{ __('Subscriptions') }}
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="members" role="tabpanel"
                     aria-labelledby="members-tab">
                    <div class="wrap-center center-block">
                        @if (Session::has('notification'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ Session::get('notification') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>
                                    <p style="color: red;">{{ $error }}</p>
                                </li>
                            @endforeach
                        </ul>
                        <table class="table">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>{{ __('views.Name') }}</th>
                                <th class="text-center">{{ __('views.email') }}</th>
                                <th class="text-center">{{ __('views.Status') }}</th>
                                <th class="text-center">{{ __('global.user-plans.fields.remaining-credits') }}</th>
                                <th class="text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invitation as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="text-center">
                                            <p class="first-ch">{{ strtoupper(substr($r->name,0,1)) }}</p>
                                        </div>
                                    </td>
                                    <td>{{ $r->name }}</td>
                                    <td class="text-center">{{ $r->email }}</td>
                                    <td class="text-center">{{ __("global.status.{$r->status}") }}</td>
                                    <td class="text-center">
                                        {{ optional($r->user)->team_remaining_credits?:'-' }}
                                    </td>
                                    <td class="text-right">
                                        @if(optional($r->user)->isLeader())
                                            <strong>
                                                {{ optional($r->user)->isLeader()?__('Admin'):'' }}
                                            </strong>
                                        @else
                                            <a href="javascript:;"
                                               data-url="{{ route('team.delete_invitation',$r->id) }}"
                                               class="text-info delete-invitation">{{ __('global.Remove') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <a href="javascript:;" class="text-info new-invitation">
                                        {{ __('Invite') }}
                                    </a>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade " id="subscriptions" role="tabpanel"
                     aria-labelledby="subscriptions-tab">
                    <div class="mb-4">
                        @if(!$subscriptions->count())
                            <section class="border rounded p-4">
                                <p class="mb-0"> {{ __("You don't have any subscription") }} </p>
                                <small>
                                    <a href="{{ route('plans') }}"> {{ trans('misc.prices and packages') }} </a>
                                </small>
                            </section>
                        @endif
                        @if($subscriptions->count())
                            @foreach($subscriptions as $r)
                                <section class="border rounded p-3 mb-3 subscription">
                                    <div class="mb-3">
                                        <h4>#{{ $r->id }} {!! $r->plan->description !!}
                                            - {{ $r->plan->members_limit }} {{ __('misc.members') }}
                                            @if($r->status==\App\Models\Subscription::STATUS_PENDING)
                                                <small class="text-danger">({{trans('global.status.pending')}})</small>
                                            @elseif(in_array($r->status,[\App\Models\Subscription::STATUS_REFUND]))
                                                <small class="text-danger">({{trans('global.status.notactive')}}
                                                    )</small>
                                            @elseif($r->status==\App\Models\Subscription::STATUS_ACTIVE)
                                                @if($r->isActive())
                                                    <small class="text-success">({{trans('global.status.active')}}
                                                        )</small>
                                                @else
                                                    <small class="text-danger">({{trans('global.status.finished')}}
                                                        )</small>
                                                @endif
                                            @endif
                                        </h4>
                                        <small
                                            class="d-block"> {!! __("plans.plan_title.{$r->plan->type}.".($r->plan->can_cancel?'no_contract':'contract')) !!} </small>
                                    </div>
                                    @if($r->ends_at)
                                        <div class="mb-3">
                                            <h5> {{ __('Plan expires') }} </h5>
                                            <small> {{ format_date('d M Y - h:i a',$r->finished_at) }} </small>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <h5> {{ __('Credits left') }} </h5>
                                        <small> {{ $r->remaining_credits }} {{ __('credit') }} </small>
                                        @if($r->plan_type=='annual')
                                            <p><small>{{ __('Credits are automatically renewed every month') }}</small>
                                            </p>
                                        @endif
                                    </div>
                                    <a href="{{ route('team.subscription_credits',$r->id) }}" data-fancybox
                                       data-type="iframe" data-preload="false" class="text-info"
                                       data-width="900"
                                       data-height="600"
                                       style="text-decoration: underline!important;">{{ __('Credits distribution') }}</a>
                                </section>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('javascript_navbar')
    <div class="modal fade" id="newInvitationModal" tabindex="-1" role="dialog" aria-labelledby="login"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered login-pop-form" role="document">
            <div class="modal-content" id="registermodal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fal fa-times"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="modal-header-title">{{{ __('Invite users') }}}</h4>
                    <div style="display: none" class="box-body mx-10 devide-wrap_validation">
                        <div class="alert alert-danger" id="dangerAlert">
                            <i class="glyphicon glyphicon-alert  "></i>
                            <div class="wrap_validation"></div>
                        </div>
                    </div>
                    <div class="login-form">
                        <form action="{{ route('team.new_invitation') }}" method="post"
                              onsubmit="return false;">
                            @csrf
                            <div class="form-group">
                                <label>{{__('auth.full_name')}}</label>
                                <div class="input-with-gray">
                                    <input type="text" value="{{ old('name') }}" name="name"
                                           title="{{ trans('auth.name') }}"
                                           oninvalid="setCustomValidity('{{trans('auth.name')}}')"
                                           oninput="setCustomValidity('')" required autocomplete="off"
                                           class="form-control"
                                           placeholder="{{__('auth.name')}}">
                                    <i class="fal fa-user"></i></div>
                            </div>
                            <div class="form-group">
                                <label>{{__('auth.email')}}</label>
                                <div class="input-with-gray">
                                    <input type="email" value="{{ old('email') }}" name="email"
                                           title="{{ trans('auth.email') }}"
                                           oninvalid="setCustomValidity('{{trans('auth.email')}}')"
                                           oninput="setCustomValidity('')" required autocomplete="off"
                                           class="form-control"
                                           placeholder="{{__('auth.email')}}">
                                    <i class="fal fa-envelope"></i></div>
                            </div>
                            <div class="form-group row">
                                <div class="col">
                                    <button class="btn btn-primary btn-lg btn-block"
                                            type="submit">{{ trans('auth.send') }}</button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-secondary btn-lg btn-block"
                                            data-dismiss="modal">{{ trans('views.Close') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/fancyapps_ui@4.0_dist_fancybox.umd.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/fancyapps_ui@4.0_dist_fancybox.css') }}"/>
    <script src="{{ asset('js/swee') }}"></script>
    <script>
        $(document).on('submit', '#newInvitationModal form', function (e) {
            e.preventDefault();
            var $form = $(this);
            $.ajax({
                type: "POST",
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function ($response) {
                    $('#newInvitationModal').modal('hide');
                    $('#newInvitationModal form')[0].reset();
                    if ($response.status) {
                        notify($response.message, 'success')
                    } else {
                        notify($response.message, 'danger')
                    }
                    update_content();
                },
                dataType: 'JSON'
            });
        })
        $(document).on('click', '.delete-invitation', function (e) {
            e.preventDefault();
            var $this = $(this);
            swal({
                title: "{!! __('Are you sure you want to delete') !!}",
                buttons: ['{{ __('global.Cancel') }}', "{{ __('global.Sure') }}"],
                dangerMode: true,
            })
                .then((confirm) => {
                    if (confirm) {
                        $.ajax({
                            type: "POST",
                            url: $this.attr('data-url'),
                            data: {_token: CSRF_TOKEN, _method: 'DELETE'},
                            success: function ($response) {
                                $this.closest('tr').fadeOut(function () {
                                    $(this).remove();
                                });
                            },
                            dataType: 'JSON'
                        });
                    }
                });
        });
        setInterval(function () {
            update_content();
        }, 15000)

        function update_content() {
            $.ajax({
                type: "GET",
                url: '{{ route('team') }}',
                success: function (response) {
                    $('#members table').html($(response).find('#members table'))
                },
                dataType: 'HTML'
            });
        }

        $(document).on('click', '.new-invitation', function (e) {
            $('#newInvitationModal').modal('show');
        });
    </script>

@endpush
