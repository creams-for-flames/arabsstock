<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Frames v2: Multi Input</title>
  <link rel="stylesheet" href="{{asset('checkout/normalize.css')}}">
  <link rel="stylesheet" href="{{asset('checkout/style.css')}}">
</head>

<body>


    <form id="payment-form" method="POST" action="https://merchant.com/charge-card">
    <label for="card-number">Card number</label>
    <div class="input-container card-number">
      <div class="icon-container">
        <img id="icon-card-number" src="{{asset('checkout/images/card-icons/card.svg')}}" alt="PAN" />
      </div>
      <div class="card-number-frame"></div>
      <div class="icon-container payment-method">
        <img id="logo-payment-method" />
      </div>
      <div class="icon-container">
        <img id="icon-card-number-error" src="{{asset('checkout/images/card-icons/error.svg')}}" />
      </div>
    </div>

    <div class="date-and-code">
      <div>
        <label for="expiry-date">Expiry date</label>
        <div class="input-container expiry-date">
          <div class="icon-container">
            <img id="icon-expiry-date" src="{{asset('checkout/images/card-icons/exp-date.svg')}}" alt="Expiry date" />
          </div>
          <div class="expiry-date-frame"></div>
          <div class="icon-container">
            <img id="icon-expiry-date-error" src="{{asset('checkout/images/card-icons/error.svg')}}" />
          </div>
        </div>
      </div>

      <div>
        <label for="cvv">Security code</label>
        <div class="input-container cvv">
          <div class="icon-container">
            <img id="icon-cvv" src="{{asset('checkout/images/card-icons/cvv.svg')}}" alt="CVV" />
          </div>
          <div class="cvv-frame"></div>
          <div class="icon-container">
            <img id="icon-cvv-error" src="{{asset('checkout/images/card-icons/error.svg')}}"/>
          </div>
        </div>
      </div>
    </div>

    <button id="pay-button" disabled="">
      PAY GBP 25.00
    </button>

    <div>
      <span class="error-message error-message__card-number"></span>
      <span class="error-message error-message__expiry-date"></span>
      <span class="error-message error-message__cvv"></span>
    </div>

    <p class="success-payment-message"></p>
  </form>

  <script src="https://cdn.checkout.com/js/framesv2.min.js"></script>
  <script src="{{asset('checkout/app.js')}}"></script>

</body>

</html>
