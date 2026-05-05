$('[data-toggle="collapse"]').on('click', function () {
    var $this = $(this), $target = $this.attr('data-target');
    console.log($this.attr('disabled'))
    if ($this.attr('disabled') == 'disabled') {
        return false;
    }
    $this.find('input[name="payment_method"]').prop("checked", true);
    if ($target == '#paypalCard') {
        $('#creditCard input').prop('disabled', true);
    } else {
        $('#creditCard input').prop('disabled', false);
    }
    if ($target == '#walletsCard' || $target == '#paypalCard') {
        button.setAttribute('disabled', true);
    } else {
        stop_waiting();
    }
});

var form = document.getElementById('purchase');
var button = document.getElementById('card-button');
var stripe = Stripe(button.dataset.stripekey, {
    locale: button.dataset.local
});
var paymentRequest;
document.addEventListener('DOMContentLoaded', async () => {
    paymentRequest = stripe.paymentRequest({
        country: 'US',
        currency: 'usd',
        total: {
            label: button.dataset.planname,
            amount: parseInt(button.dataset.price),
        },
        requestPayerName: true,
        requestPayerEmail: true,
    });

    var elements = stripe.elements();
    var prButton = elements.create('paymentRequestButton', {
        paymentRequest: paymentRequest,
    });
    paymentRequest.canMakePayment().then(function (result) {
        if (result) {
            if (result.applePay == false) {
                $('.purchase .card.wallets .form-check img.applayPay').remove();
                $('.purchase .card.wallets .form-check span').remove();
            }
            if (result.googlePay == false) {
                $('.purchase .card.wallets .form-check img.googlePay').remove();
                $('.purchase .card.wallets .form-check span').remove();
            }
            $('.wallets').slideDown();
            $('#payment-request-button').closest('.card').slideDown();
            $('.Divider').slideDown();
            prButton.mount('#payment-request-button');
        } else {
            document.getElementById('payment-request-button').style.display = 'none';
        }
    });
    paymentRequest.on('paymentmethod', async (e) => {
        var {error: backendError, payment_status, client_secret, subscription_id} = await fetch(
            button.dataset.purchaseurl,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    _token: CSRF_TOKEN,
                    payment_method: 'card',
                    pmethod: e.paymentMethod.id,
                    plan_id: button.dataset.planid
                }),
            }
        ).then((r) => r.json());
        if (backendError) {
            e.complete('fail');
            return;
        }
        if (payment_status === 'requires_action' || payment_status === 'requires_confirmation') {
            let {error, paymentIntent} = await stripe.confirmCardPayment(
                client_secret
            );
            if (error) {
                notify(error.message, 'danger')
                setTimeout(function () {
                    window.location.href = button.dataset.failurl;
                }, 3000)
                return;
            }
        }
        window.location.href = button.dataset.statusurl.replace(':subscription_id', subscription_id);
    });
});

var stripeElements = stripe.elements({
    fonts: [
        {
            cssSrc: 'https://fonts.googleapis.com/css?family=Cairo&display=swap&subset=arabic',
        },
    ],
    locale: button.dataset.local
});
var elementStyles = {
    base: {
        fontFamily: 'Cairo, sans-serif',
        fontSize: '14px',
        '::placeholder': {
            color: '#cecece',
        },
    },
};
var cardElement = stripeElements.create('cardNumber', {
    style: elementStyles,
});

cardElement.mount('#card-element');
var cardHolderName = document.getElementById('card-holder-name');


var cardExpiry = stripeElements.create('cardExpiry', {
    style: elementStyles,
});
cardExpiry.mount('#card-expiry');

var cardCvc = stripeElements.create('cardCvc', {
    style: elementStyles,
});
cardCvc.mount('#card-cvc');


button.addEventListener('click', async (e) => {
    e.preventDefault();
    if ($('#promocode').length && $('#promocode').val() != '' && !$('#promocode').attr('readonly')) {
        // swal($('#check_promocode').attr('data-apply-msg'));
        notify($('#check_promocode').attr('data-apply-msg'));
        return false;
    }
    button.setAttribute('disabled', !0);
    $(document.body).css({'cursor': 'wait'});
    if (($('input[name="payment_method"]:checked').val() == 'paypal')) {
        form.submit();
        return false;
    } else {
        var pmethod;
        if ($('#defaultPaymentMethod').is(':checked')) {
            pmethod = $('#defaultPaymentMethod').val();
        } else {
            if (!cardHolderName.value) {
                cardHolderName.focus();
                stop_waiting();
                return false;
            }
            var {paymentMethod, error} = await stripe.createPaymentMethod(
                'card', cardElement, {
                    billing_details: {name: cardHolderName.value}
                }
            );
            if (error) {
                // Display "error.message" to the user...
                notify(error.message, 'danger')
                stop_waiting();
                return;
            }
            pmethod = paymentMethod.id;
        }

        var {error: backendError, payment_status, client_secret, subscription_id} = await fetch(
            button.dataset.purchaseurl,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    _token: CSRF_TOKEN,
                    payment_method: 'card',
                    pmethod: pmethod,
                    plan_id: button.dataset.planid
                }),
            }
        ).then((r) => r.json());
        if (backendError) {
            notify(backendError, 'danger');
            stop_waiting();
            return;
        }
        if (payment_status === 'requires_action' || payment_status === 'requires_confirmation') {
            let {error, paymentIntent} = await stripe.confirmCardPayment(
                client_secret
            );
            if (error) {
                notify(backendError, 'danger');
                setTimeout(function () {
                    window.location.href = button.dataset.failurl;
                }, 3000)
                return;
            }
        }
        window.location.href = button.dataset.statusurl.replace(':subscription_id', subscription_id);
    }
});
$('#defaultPaymentMethod').on('change', function () {
    var $val = $('#defaultPaymentMethod:checked').val()
    if ($val) {
        $('#creditCardDetails').slideUp();
        $('#creditCardDetails input').attr('disabled', true);
    } else {
        $('#creditCardDetails').slideDown();
        $('#creditCardDetails input').attr('disabled', false);
    }
}).change();


$('#check_promocode').on('click', function (e) {
    e.preventDefault();
    var $this = $(this),
        _promocode = $('#promocode');
    if (_promocode.val()) {
        $this.append('<div class="btn-loader"></div>');
        $.ajax({
            type: "POST",
            url: $('#check_promocode').attr('data-check-url'),
            data: {_token: CSRF_TOKEN, promocode: _promocode.val(), plan_id: $('#promocode').attr('data-plan-id')},
            success: function (data) {
                if (data.status == 1) {
                    $('#promocode_msg').removeClass('text-danger').addClass('color-primary').text(data.msg).css('visibility', 'visible');
                    $('#after_discount').removeClass('d-none');
                    $('#after_discount strong').text(data.promocode.title);
                    $('#after_discount .discount').text('-$' + parseInt(data.promocode.discount));
                    $('#total_price').text('$' + parseInt(data.promocode.total));
                    $('#check_promocode').removeClass('d-none');
                    paymentRequest.update({
                        country: 'US',
                        currency: 'usd',
                        total: {
                            label: button.dataset.planname,
                            amount: data.promocode.total * 100,
                        },
                    });
                    $('#promocode').attr('readonly', true);
                    $('#check_promocode').addClass('d-none');
                    $('#delete_promocode').removeClass('d-none');
                    if (data.promocode.total == 0) {
                        $('[data-toggle="collapse"]').attr('disabled', true);
                        $('#creditCard').removeClass('show');
                        $('#payment_method > .card').attr('disabled', true);
                        $('#payment_method input').attr('readonly', true);
                        $('#complete_order').removeClass('d-none');
                    }
                } else {
                    $('#promocode_msg').removeClass('color-primary').addClass('text-danger').text(data.msg).css('visibility', 'visible');
                }
            },
            complete() {
            },
            error(http) {
                $('#promocode_msg').removeClass('color-primary').addClass('text-danger').text(http.responseJSON.message).css('visibility', 'visible');
            },
            dataType: 'json'
        });
    } else {
        _promocode.addClass('text-danger');
        _promocode.focus();
    }
});

function stop_waiting() {
    button.removeAttribute('disabled');
    $(document.body).css({'cursor': 'auto'});
}

$(document).on('click', '#delete_promocode', function (e) {
    e.preventDefault();
    $('#after_discount').addClass('d-none');
    $('#delete_promocode').addClass('d-none');
    $('#check_promocode').removeClass('d-none');
    $('#promocode').attr('readonly', false).val('');
    $('#promocode_msg').text(lang.global.promocode_deleted).css('visibility', 'visible');
    $('#total_price').text($('#promocode').attr('data-plan-price') + '$');

    $('[data-toggle="collapse"]').attr('disabled', false);
    $('#creditCard').addClass('show');
    $('#payment_method > .card').attr('disabled', false);
    $('#payment_method input').attr('readonly', false);
    $('#complete_order').addClass('d-none');
    var $this = $(this);
    $.ajax({
        type: "POST",
        url: $this.attr('data-url'),
        data: {_token: CSRF_TOKEN},
        dataType: 'json'
    });
});
$(document).on('click', '#complete_order', function () {
    $('#pmethod').val(1)
    form.submit();
    return false;
});
