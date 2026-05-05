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
                                    <h3 class="kt-portlet__head-title">{{ trans('admin.edit') }}</h3>
                                </div>
                            </div>
                            <form class="kt-form kt-form--label-left" method="post"
                                  enctype="multipart/form-data"
                                  action="{{ route('admin.subscriptions.update',$subscription) }}">
                                @csrf
                                @method('PUT')
                                @include('errors.errors-forms')
                                <div class="kt-portlet__body">
                                    <div class="kt-section kt-section--first">
                                        <div class="kt-section__body">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ __('Team') }}</label>
                                                <div class="col-sm-12">
                                                    <select name="team_id" class="form-control" id="team_id" required
                                                            disabled>
                                                        <option
                                                        >{{ $subscription->user->name }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{ __('Plan') }}</label>
                                                <div class="col-sm-12">
                                                    <select name="plan_id" class="form-control select2-input"
                                                            id="plan_id" disabled>
                                                        <option>{{ number_format($subscription->plan->price) }}
                                                            $/{{ $subscription->plan->description }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">مكان الشراء</label>
                                                <div class="col-sm-12">
                                                    @php($city=\App\Models\Cities::find($subscription->city_id))
                                                    <select name="city_id" class="form-control select2-input"
                                                            id="city_id" required>
                                                        @if($city)
                                                            <option
                                                                value="{{ $city->id }}">{{ $city->name_ar }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">رقم الحوالة</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="payment_id" class="form-control"
                                                           required
                                                           value="{{ old('payment_id',$subscription->payment_id) }}">
                                                </div>
                                            </div>
                                            @php($data=json_decode($subscription->data,true))
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">البنك</label>
                                                <div class="col-sm-12">
                                                    <input type="text" name="bank" class="form-control"
                                                           value="{{ old('bank',$data['bank']) }}">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">صورة الحوالة</label>
                                                <div class="col-sm-12">
                                                    @if(@$data['transfer'])
                                                    <a class="mb-2" target="_blank" href="{{ cdn($data['transfer']) }}">{{ cdn($data['transfer']) }}</a>
                                                    @endif
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="transfer" name="transfer"
                                                               accept="image/jpeg,image/jpg,image/png,application/pdf"
                                                        >
                                                        <label class="custom-file-label" for="transfer">Choose file</label>
                                                    </div>
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
        $('#team_id').select2({
            language: "ar",
            placeholder: 'اختر فريق',
            ajax: {
                url: '{{ route('admin.teams.ajax') }}',
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
        $('#city_id').select2({
            language: "ar",
            placeholder: 'اختر مدينة',
            ajax: {
                url: '{{ route('admin.cities.ajax') }}',
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
                                text: item.name_ar + '-' + item.country.name_ar,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    </script>
@endpush
