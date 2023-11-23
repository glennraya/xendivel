{{--
    Kindly take note that this blade template is meant for demonstrating the process for tokenizing the customer's card details.
    This is a working version, of course it is like you'll need to implement this using a front-end framework of your choice.

    You can use this template as a "base" template for your own implementation.
    This uses vanilla JavaScript and styled using Tailwindcss.
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Xendivel Cards Charging Template</title>

        @vite('resources/css/app.css')
    </head>
    <body class="antialiased relative h-screen grid place-content-center bg-gray-100">

        {{-- 3DS authentication dialog. --}}
        <div id="payer-auth-wrapper" class="justify-center items-center absolute top-0 left-0 w-full h-full bg-black bg-opacity-75 hidden z-10">
            <iframe id="payer-auth-url" frameborder="0" class="w-1/2 h-3/4 bg-white rounded-xl"></iframe>
        </div>

        <div class="flex flex-col gap-y-4">
            <header>
                <h1 class="text-4xl text-center font-bold mb-4">Xendivel Card Payment Example</h1>
                <p class="text-center text-gray-500">This is a template for card payments. Consider this as a "basis" <br />for your front-end implementation.
                <br /> <br />Test card numbers are available on Xendit's docs: <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios" class="text-blue-500 border-b border-blue-500" target="_tab">Test card numbers</a>.</p>
            </header>

            <div class="flex flex-col w-[600px] bg-white shadow-md rounded-xl p-6 self-center">
                <form id="payment-form" class="grid grid-cols-6 gap-4">
                    {{-- Amount to pay --}}
                    <div class="flex gap-x-4 col-span-6">
                        <div class="flex flex-col w-full">
                            <label for="amount-to-pay" class="text-sm uppercase font-bold text-gray-500">Amount to pay</label>
                            <div class="flex flex-col">
                                <div class="flex">
                                    <input type="number" id="amount-to-pay" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="PHP" value="1000">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card number --}}
                    <div class="flex gap-x-4 col-span-3">
                        <div class="flex flex-col w-full">
                            <label for="card-number" class="text-sm uppercase font-bold text-gray-500">Card number</label>
                            <div class="flex flex-col">
                                <div class="flex">
                                    <input type="number" id="card-number" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="4XXXXXXXXXXX1091" value="5200000000002151">
                                </div>
                            </div>
                        </div>
                    </div>

                     {{-- Expiry Date --}}
                    <div class="flex gap-x-4 col-span-2">
                        <div class="flex flex-col ">
                            <label for="card-exp-month" class="text-sm uppercase font-bold text-gray-500">Expiry Date</label>
                            <div class="flex gap-x-4 bg-gray-100 rounded-xl">
                                <div class="flex">
                                    <input type="number" id="card-exp-month" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="MM" value="12">
                                </div>

                                <div class="flex">
                                    <input type="number" id="card-exp-year" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="YYYY" value="2030">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CVV --}}
                    <div class="flex gap-x-4 col-span-1">
                        <div class="flex flex-col">
                            <label for="card-cvn" class="text-sm uppercase font-bold text-gray-500">CVV</label>
                            <div class="flex gap-x-4">
                                <div class="flex">
                                    <input type="number" id="card-cvn" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="CVV" value="123">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="submit" class="submit col-span-6 bg-gray-900 text-white rounded-xl p-4 text-sm uppercase font-bold disabled:hover:bg-gray-900 disabled:opacity-75 hover:bg-gray-600">Pay with card</button>

                    <div id="token-wrapper" class="col-span-6 flex-col gap-y-2 justify-center items-center hidden">
                        <span class="font-bold">Card Token:</span>
                        <pre id="card-token" class="bg-gray-100 p-4 rounded-xl"></pre>
                        <span class="text-center">This is the tokenized value of the customer's card details. You can now begin charging the card using this token.</span>
                    </div>

                    <div id="errorDiv" class="col-span-6 flex-col gap-y-2 justify-center items-center hidden">
                        <span class="font-bold">Error Code:</span>
                        <pre class="bg-gray-100 p-4 rounded-xl"></pre>
                        <span class="text-center">Using this error code you can give the user a customized message based on the error code.</span>
                    </div>
                </form>
            </div>
        </div>

        {{-- Xendit's JavaScript library for "tokenizing" the customer's card details. --}}
        {{-- Reference: https://docs.xendit.co/credit-cards/integrations/tokenization --}}
        <script src="https://js.xendit.co/v1/xendit.min.js"></script>

        {{-- Enter your public key here. It is SAFE to directly input your public key in your views or JS templates. --}}
        <script>
            Xendit.setPublishableKey('xnd_public_development_3uULwlIxkISE6z2vhQrYK5PgbjYBzBdTCKEpig7QXWpx0GZhPnFObLexpXLfcnjC');
        </script>

        {{-- Process for tokenizing the card details and validation of the card offered by Xendit.js library --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                var form = document.getElementById('payment-form');

                form.addEventListener('submit', function(event) {
                    // Prevent the form from being submitted
                    event.preventDefault();

                    // Disable the submit button to prevent repeated clicks
                    var submitButton = form.querySelector('.submit');
                    submitButton.disabled = true;

                    // Card validation: The 'card_number', 'expiry_date' and 'cvn' vars returns boolean values (true, false).
                    var card_number = Xendit.card.validateCardNumber(form.querySelector('#card-number').value);
                    var expiry_date = Xendit.card.validateExpiry(form.querySelector("#card-exp-month").value, form.querySelector("#card-exp-year").value);
                    var cvn = Xendit.card.validateCvn(form.querySelector("#card-cvn").value);
                    var amount_to_pay = form.querySelector("#amount-to-pay").value;

                    if(amount_to_pay === '') {
                        alert("Input the amount to be paid.");
                        submitButton.disabled = false;
                        return;
                    }

                    if(!card_number || card_number === '') {
                        alert("Invalid card number.");
                        submitButton.disabled = false;
                        return;
                    }

                    if(!expiry_date || expiry_date === '') {
                        alert("Invalid card expiry date.");
                        submitButton.disabled = false;
                        return;
                    }

                    if(!cvn || cvn === '') {
                        alert("Invalid card CVN/CVV.");
                        submitButton.disabled = false;
                        return;
                    }

                    // Request a token from Xendit
                    Xendit.card.createToken({
                        // Card details and the amount to pay.
                        amount: form.querySelector('#amount-to-pay').value,
                        card_number: form.querySelector('#card-number').value,
                        card_exp_month: form.querySelector('#card-exp-month').value,
                        card_exp_year: form.querySelector('#card-exp-year').value,
                        card_cvn: form.querySelector('#card-cvn').value,

                        // Single use token only.
                        is_multiple_use: false,

                        // 3DS authentication (OTP).
                        // Note: Some cards will not show 3DS authentication.
                        should_authenticate: true
                    }, xenditResponseHandler);
                });

                //
                function xenditResponseHandler(err, creditCardToken) {
                    console.log(creditCardToken);

                    var form = document.getElementById('payment-form');
                    var authDialog = document.getElementById('payer-auth-wrapper')
                    var errorDiv = document.getElementById('errorDiv');
                    var errorPre = errorDiv.querySelector('pre');
                    var submitButton = form.querySelector('.submit');

                    if (err) {
                        // Show the errors on the form
                        console.log(err);

                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'flex';

                        errorDiv.style.display = 'flex';
                        errorPre.textContent = err.message;

                        // Re-enable submission
                        submitButton.disabled = false;
                        return;
                    }

                    if (creditCardToken.status === 'VERIFIED') {
                        // Get the tokenized value of the card details.
                        var token = creditCardToken.id;
                        var tokenWrapper = document.getElementById('token-wrapper');
                        var tokenValue = document.getElementById('card-token')

                        // Hide the 3DS authentication dialog.
                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'none'

                        // Insert the token into the form so it gets submitted to the server
                        console.log(token);
                        tokenWrapper.style.display = 'flex';
                        tokenValue.textContent = token;

                        // Re-enable submission
                        submitButton.disabled = false;

                        // var input = document.createElement('input');
                        // input.setAttribute('type', 'hidden');
                        // input.setAttribute('name', 'xenditToken');
                        // input.value = token;
                        // form.appendChild(input);

                        // Submit the form to your server
                        // form.submit();

                    } else if (creditCardToken.status === 'IN_REVIEW') {
                        authDialog.style.display = 'flex';
                        setIframeSource('payer-auth-url', creditCardToken.payer_authentication_url);
                    } else if (creditCardToken.status === 'FAILED') {

                        // Hide the 3DS authentication dialog.
                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'none'

                        // Display an error
                        errorPre.textContent = creditCardToken.failure_reason;
                        errorDiv.style.display = 'block';

                        // Re-enable submission
                        submitButton.disabled = false;
                    }
                }

                // Function to set the iframe source dynamically
                function setIframeSource(iframeId, url) {
                    var iframe = document.getElementById(iframeId);
                    if (iframe) {
                        iframe.src = url;
                    } else {
                        console.error('Iframe not found');
                    }
                }

            });
        </script>
    </body>
</html>
