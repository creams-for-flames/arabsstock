<div class="modal fade" id="licenseDetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('license.usage_licenses') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="license-details">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{ __('license.types.standard') }}</th>
                        <th>{{ __('license.types.enhanced') }}</th>
                        <th>{{ __('license.types.exclusive') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td>
                            <div class="license_title">
                                <img src="{{ asset('img/license/web.svg') }}" alt="web" class="mr-3">
                                <div>
                                    <h4> {{ __('license.all_web_mediums') }} </h4>
                                    <span>
                                                                    ({{ __('license.websites_social_media_email') }})
                                                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <i class="fal fa-check mr-2"></i>
                            {{ __('license.unlimited') }}
                        </td>
                        <td>
                            <i class="fal fa-check mr-2"></i>
                            {{ __('license.unlimited') }}
                        </td>
                        <td>
                            <i class="fal fa-check mr-2"></i>
                            {{ __('license.unlimited') }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="license_title">
                                <img src="{{ asset('img/license/ads.svg') }}" alt="ads" class="mr-3">
                                <div>
                                    <h4> {{ __('license.web&app_ad') }} </h4>
                                </div>
                            </div>
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.impressions',['count'=>'500.000']) }}
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="license_title">
                                <img src="{{ asset('img/license/print.svg') }}" alt="print" class="mr-3">
                                <div>
                                    <h4> {{ __('license.printing_and_packaging') }} </h4>
                                </div>
                            </div>
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.copy',['count'=>'500.000']) }}
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="license_title">
                                <img src="{{ asset('img/license/layouts.svg') }}" alt="layouts" class="mr-3">
                                <div>
                                    <h4> {{ __('license.outside') }} </h4>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="canceled">
                                <i class="fal fa-times mr-3"></i>
                                {{ __('license.not_included') }}
                            </div>
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="license_title">
                                <img src="{{ asset('img/license/tv.svg') }}" alt="layouts" class="mr-3">
                                <div>
                                    <h4> {{ __('license.tv_and_cinema') }} </h4>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="canceled">
                                <i class="fal fa-times mr-3"></i>
                                {{ __('license.not_included') }}
                            </div>
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                        <td>
                            <i class="fal fa-check"></i>
                            {{ __('license.unlimited') }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
