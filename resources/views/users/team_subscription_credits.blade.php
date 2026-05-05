<!DOCTYPE html>
<html lang="en" dir="{{ config('app.locale')=='ar'?'rtl':'ltr' }}">
<head>
    <title>{{ __('Credits distribution') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/bootstrap@5.2.3_dist_css_bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ asset('js/bootstrap@5.2.3_dist_js_bootstrap.bundle.min.js') }}"></script>
    <style>
        body{
            margin: 20px;
        }

        .cell-edit{
            visibility: hidden;
        }

        table{
            width: auto !important;
        }

        td, th{
            vertical-align: middle !important;
            height: 47px !important;
        }

        tr:hover .cell-edit{
            visibility: visible;
        }

        tr td:nth-child(1){
            width: 200px;
        }
        tr td:nth-child(2){
            width: 200px;
        }
        tr td:nth-child(3){
            width: 24px;
        }
    </style>
</head>
<body>
<div class="container mt-3 mx-auto">
    <h2>{{ __('Credits distribution') }}</h2>
    <h3>#{{ $subscription->id }} {!! $subscription->plan->description !!}
        - {{ $subscription->plan->members_limit }} {{ __('misc.members') }}</h3>
    <p> {{ __('Credits left') }}: {{ $subscription->remaining_credits }} {{ __('credit') }}</p>
    <form action="" method="post" class="mx-auto">
        @csrf
        <table class="table table-bordered table-striped mx-auto">
            <thead>
            <tr>
                <th>{{ __('views.Name') }}</th>
                <th>{{ __('views.email') }}</th>
                <th class="text-center" width="140">{{ __('credits') }}</th>
                <th class="text-center" width="140">{{ __('Consumed credits') }}</th>
                {{--                <th class="text-center">{{ __('global.user-plans.fields.remaining-credits') }}</th>--}}
                <th></th>
            </tr>
            </thead>
            <tbody>
            @php
                $reserved=DB::table('team_user_subscription')->where('subscription_id', $subscription->id)->where('credits','>',0)->pluck('user_id')->toArray();
                $lock=false;
                if(count($reserved)==$subscription->plan->members_limit)
                    $lock=true;
            @endphp
            @foreach($users as $r)
                <tr data-user_id="{{ $r->id }}">
                    <td>{{ $r->name }}</td>
                    <td>{{ $r->email }}</td>
                    <td class="text-center">
                        @php($credits=optional(optional(optional($r->team_subscriptions)->first())->pivot)->remaining_credits?:0)
                        {{ $credits?:0 }}
                        {{--                        <input type="number" name="users[{{ $r->id }}][credits]"--}}
                        {{--                               class="form-control text-center mx-auto" min="0"--}}
                        {{--                               value="{{ $credits?:0 }}"--}}
                        {{--                               style="width: 100px;" placeholder="{{ __('credits') }}"--}}
                        {{--                               @if($lock && !in_array($r->id,$reserved))--}}
                        {{--                               disabled--}}
                        {{--                            @endif--}}
                        {{--                        >--}}
                    </td>
{{--                                        <td class="text-center"--}}
{{--                                            data-remaining_credits>{{ optional(optional(optional($r->team_subscriptions)->first())->pivot)->remaining_credits?:'-' }}</td>--}}
                    <td class="text-center">{{ $r->consumed_credits?:0 }}</td>
                    <td class="cell-edit">
                        <button type="button" class="btn btn-light">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path
                                    d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                <path fill-rule="evenodd"
                                      d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="{{ asset('js/sweetalert@2.1.2_dist_sweetalert.min.js') }}"></script>
<script>
    $(document).on('click', 'tbody .cell-edit', function () {
        var credits = parseInt($(this).parent().find('td:eq(2)').text());
        $(this).parent().find('td:eq(2)').html('<input min="0" type="number" class="form-control input-sm text-center" value="' + credits + '" data-old-value="' + credits + '">');
        $(this).parent().find('td:eq(4)').replaceWith('<td class="cell-save"><button type="button" class="btn btn-success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg></button></td>');
    });
    $(document).on('click', 'tbody .cell-save', function () {
        var $this = $(this),
            credits = parseInt($this.parent().find('td:eq(2) input').val()),
            user_id = $this.closest('tr').attr('data-user_id')
        ;
        $.ajax({
            type: "POST",
            url: window.location.href,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: {
                user_id: user_id,
                credits: credits,
            },
            success: function (response) {
                if (response.status) {
                    $this.parent().find('td:eq(2)').html(credits);
                } else {
                    $this.parent().find('td:eq(2)').html($this.parent().find('td:eq(2) input').attr('data-old-value'));
                }
                $this.parent().find('td:eq(4)').replaceWith('<td class="cell-edit"><button type="button" class="btn btn-light"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path></svg></button></td>');
                if (response.message) {
                    swal(response.message)
                }

            },
            error: function ($ajax) {
                if ($ajax.responseJSON && $ajax.responseJSON.errors) {
                    $.each($ajax.responseJSON.errors, function ($k, $r) {
                        swal($r[0]);
                    });
                }
            }

        });
    });
</script>
</body>
</html>
