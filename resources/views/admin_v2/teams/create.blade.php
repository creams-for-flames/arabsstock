@extends('admin_v2.layout.app')

@section('content')
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <!--Begin::App-->
        <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
            <!--Begin:: App Aside Mobile Toggle-->
            <button class="kt-app__aside-close" id="kt_user_profile_aside_close">
                <i class="la la-close"></i>
            </button>
            <!--End:: App Aside Mobile Toggle-->
            <!--Begin:: App Content-->
            <div class="kt-grid__item kt-grid__item--fluid kt-app__content" style="margin-left:0;">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="kt-portlet">
                            <div class="kt-portlet__head">
                                <div class="kt-portlet__head-label">
                                    <h3 class="kt-portlet__head-title">{{ trans('admin.add') }}</h3>
                                </div>
                            </div>
                            <form class="kt-form kt-form--label-left" method="post"
                                  action="{{ route('admin.teams.store') }}">
                                @csrf
                                @include('errors.errors-forms')
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ trans('admin.name') }}</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="name" class="form-control"
                                                           placeholder="{{ trans('admin.name') }}" required
                                                           value="{{ old('name') }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">مدير الفريق</label>
                                                <div class="col-sm-12">
                                                    <select name="leader_id" class="form-control" id="leader_id">
                                                        @if(old('leader_id'))
                                                            @php($user=\App\Models\User::find(old('leader_id')))
                                                            @if($user)
                                                                <option
                                                                    value="{{ $user->id }}">{{ $user->name }}</option>
                                                            @endif
                                                        @endif
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-portlet__foot">
                                    <div class="kt-form__actions">
                                        <div class="row">
                                            <div class="col-lg-9 col-xl-9">
                                                <button type="submit"
                                                        class="btn btn-success">{{ trans('admin.save') }}</button>&nbsp;
                                                <a href="{{ route('admin.teams.index') }}"
                                                   class="btn btn-secondary">{{ trans('admin.cancel') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--End:: App Content-->
        </div>
        <!--End::App-->
    </div>
    <!-- end:: Content -->
@endsection
@push('css')
@endpush

@push('scripts')
    <script>
        $('#leader_id').select2({
            language: "ar",
            placeholder: '{{ __('views.Choose Users') }}',
            ajax: {
                url: '{{ route('admin.members.ajax') }}',
                dataType: 'json',
                method: 'get',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    </script>
@endpush
