@if(auth()->check() && !auth()->user()->mobile)
    <div class="modal fade" id="check-mobile-modal" tabindex="-1" role="dialog" aria-labelledby="check-mobile-label"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="check-mobile-label">{{ __('Please enter your mobile number') }}</h5>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('account.update-mobile') }}">
                        @csrf
                        <div class="form-group">
                            <label for="mobile" class="col-form-label">{{ __('Mobile') }}:</label>
                            <input type="text" class="form-control" id="modal-mobile" name="mobile">
                        </div>
                        <div class="alert alert-danger d-none" role="alert">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary submit-button"
                            onclick="updateMobile()">{{ __("views.Save Changes") }}</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"
            integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"/>
    <style>
        #modal-mobile{direction: ltr;height: 40px;}
        .iti__arrow{margin-right: 6px;margin-left: 0;}
        .iti{display: block;}
        .iti--allow-dropdown input, .iti--allow-dropdown input[type=text], .iti--allow-dropdown input[type=tel], .iti--separate-dial-code input, .iti--separate-dial-code input[type=text], .iti--separate-dial-code input[type=tel]{padding-left: 50px;}
    </style>
    <script>
        $('#check-mobile-modal').modal({backdrop: 'static',keyboard: false});
        $('#check-mobile-modal').modal('show');
        var input = document.querySelector("#modal-mobile");
        window.intlTelInputGlobals.loadUtils("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js");
        iti = intlTelInput(input, {
            allowExtensions: true,
            autoFormat: false,
            autoHideDialCode: false,
            customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
                return selectedCountryPlaceholder;
            },
            defaultCountry: "auto",
            ipinfoToken: "yolo",
            nationalMode: false,
            separateDialCode: false,
            numberType: "MOBILE",
            preventInvalidNumbers: true,
            initialCountry: "sa",
        });

        function updateMobile() {
            var $button = $('#check-mobile-modal .modal-footer [type="button"]');
            $('#check-mobile-modal form #mobile').val();
            if (!iti.isValidNumber()) {
                $('#check-mobile-modal form #mobile').focus()
            }
            $button.attr('disabled', true).append('<div class="btn-loader"></div>');
            $.ajax({
                type: "POST",
                url: '{{ route('account.update-mobile') }}',
                data: $('#check-mobile-modal form').serialize(),
                success: function ($response) {
                    $button.attr('disabled', !1).find('.btn-loader').remove();
                    $('#check-mobile-modal').modal('hide');
                },
                dataType: 'JSON',
                complete: function () {
                    $button.attr('disabled', !1).find('.btn-loader').remove();
                },
                error: function ($response) {
                    $button.attr('disabled', false).find('.btn-loader').remove();

                    $errors = JSON.parse($response.responseText).errors;
                    $first = $errors[Object.keys($errors)[0]][0];
                    $('#check-mobile-modal .alert').removeClass('d-none').text($first)
                },
            });
        }
    </script>
@endif
