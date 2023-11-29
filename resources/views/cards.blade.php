<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Xendivel Cards Payment Template</title>

        @vite('resources/css/app.css')
    </head>
    <body class="antialiased relative h-screen grid bg-gray-100 pt-16">

        {{-- 3DS Auth dialog. --}}
        <div id="payer-auth-wrapper" class="justify-center items-center absolute top-0 left-0 w-full h-full bg-black bg-opacity-75 hidden z-10">
            <iframe id="payer-auth-url" frameborder="0" class="w-1/2 h-3/4 bg-white rounded-xl"></iframe>
        </div>
        {{-- End: 3DS Auth Dialog --}}

        <div class="flex flex-col gap-y-4">
            <header>
                <h1 class="text-4xl text-center font-bold mb-4">Xendivel Card Payment Example</h1>
                <p class="text-center text-gray-500">This is a template for card payments. Consider this as a "basis" <br />for your front-end implementation.
                <br /> <br />Test card numbers are available on Xendit's docs: <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios" class="text-blue-500 border-b border-blue-500" target="_tab">Test card numbers</a>. <br />You can also test for various failure scenarios: <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions" class="text-blue-500 border-b border-blue-500" target="_tab">Test failed scenarios</a></p>
            </header>

            <div class="flex flex-col w-[600px] bg-white shadow-md rounded-xl p-6 self-center">
                <form action="/api/charge-card" method="POST" id="payment-form" class="grid grid-cols-6 gap-4">
                    @csrf
                    {{-- Amount to pay --}}
                    <div class="flex gap-x-4 col-span-6">
                        <div class="flex flex-col w-full">
                            <label for="amount-to-pay" class="text-sm uppercase font-bold text-gray-500">Amount to pay</label>
                            <div class="flex flex-col">
                                <div class="flex">
                                    <input type="text" id="amount-to-pay" name="amount" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="PHP" value="1000">
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
                                    <input type="text" id="card-number" name="card-number" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="4XXXXXXXXXXX1091" value="5200000000001005">
                                </div>
                            </div>
                        </div>
                    </div>

                     {{-- Expiry Date --}}
                    <div class="flex gap-x-4 col-span-2">
                        <div class="flex flex-col ">
                            <label for="card-exp-month" class="text-sm uppercase font-bold text-gray-500">Expiry Date</label>
                            <div class="flex gap-x-4 bg-gray-100 rounded-xl">
                                <div class="flex w-3/4">
                                    <input type="text" id="card-exp-month" name="card-exp-month" class="w-full bg-gray-100 p-3 rounded-xl outline-none text-center focus:ring focus:ring-blue-400" placeholder="MM" value="12">
                                </div>

                                <div class="flex">
                                    <input type="text" id="card-exp-year" name="card-exp-year" class="w-full bg-gray-100 p-3 rounded-xl outline-none text-center focus:ring focus:ring-blue-400" placeholder="YYYY" value="2030">
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
                                    <input type="text" id="card-cvn" name="card-cvn" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="CVV" value="123">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Button for generating the tokenized value of card details. --}}
                    <button id="charge-card-btn" type="button" class="submit col-span-6 bg-gray-900 text-white rounded-xl p-4 text-sm uppercase font-bold disabled:hover:bg-gray-900 disabled:opacity-75 hover:bg-gray-600">
                        <span id="pay-label">Charge Card</span>
                        <span id="processing" class="hidden">Processing...</span>
                    </button>

                    {{-- Display the error from Xendit if there's any. --}}
                    <div id="errorDiv" class="col-span-6 flex-col gap-y-2 justify-center items-center hidden">
                        <span class="font-bold">Error Code:</span>
                        <pre class="bg-gray-100 p-4 rounded-xl"></pre>
                        <span class="text-center">Using this error code, you can give the user a customized message based on the error code. You could also check your console for more info.</span>
                        <span class="text-center flex flex-col gap-y-2">
                            <a href="https://docs.xendit.co/credit-cards/understanding-card-declines#sidebar" class="text-blue-500 border-b border-blue-500" target="_tab">Understanding card declines</a>
                            <a href="https://developers.xendit.co/api-reference/#capture-charge" class="text-blue-500 border-b border-blue-500" target="_tab">Capture card error codes</a>
                            <a href="https://developers.xendit.co/api-reference/#create-token" class="text-blue-500 border-b border-blue-500" target="_tab">Create token error codes</a>
                        </span>
                    </div>

                    {{-- Charge Response --}}
                    <div id="charge-response" class="col-span-6 flex-col gap-y-2 justify-center items-center hidden">
                        <span class="font-bold">API Response:</span>
                        <pre class="bg-gray-100 p-4 rounded-xl mt-2 whitespace-pre-wrap"></pre>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

        {{-- Xendit's JavaScript library for "tokenizing" the customer's card details. --}}
        {{-- Reference: https://docs.xendit.co/credit-cards/integrations/tokenization --}}
        <script src="https://js.xendit.co/v1/xendit.min.js"></script>

        {{-- Enter your public key here. It is SAFE to directly input your
            public key in your views or JS templates. But in this
            example, we are directly getting it from the .env file.  --}}
        <script>
            Xendit.setPublishableKey(
                '{{ getenv('XENDIT_PUBLIC_KEY') }}'
            );
        </script>

        {{-- Process for tokenizing the card details, validation
            and charging the credit/debit card. --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                var form = document.getElementById('payment-form');
                var chargeCardBtn = form.querySelector('#charge-card-btn')
                var payLabel = form.querySelector('#pay-label');
                var processingLabel = form.querySelector('#processing');
                var authDialog = document.getElementById('payer-auth-wrapper')
                var errorDiv = document.getElementById('errorDiv')
                var errorPre = errorDiv.querySelector('pre')

                chargeCardBtn.addEventListener('click', function(event) {
                    event.preventDefault();

                    // Disable the submit button to prevent repeated clicks
                    var chargeCardBtn = form.querySelector('.submit');
                    chargeCardBtn.disabled = true;

                    // Show the 'processing...' label to indicate the tokenization is processing.
                    payLabel.style.display = 'none'
                    processingLabel.style.display = 'inline-block'

                    // Card validation: The 'card_number', 'expiry_date' and 'cvn' vars returns boolean values (true, false).
                    var card_number = Xendit.card.validateCardNumber(form.querySelector('#card-number').value);
                    var expiry_date = Xendit.card.validateExpiry(
                        form.querySelector("#card-exp-month").value,
                        form.querySelector("#card-exp-year").value
                    );
                    var cvn = Xendit.card.validateCvn(form.querySelector("#card-cvn").value);
                    var amount_to_pay = form.querySelector("#amount-to-pay").value;

                    // Amount to pay validation
                    if(amount_to_pay === '') {
                        alert("Input the amount to be paid.");
                        chargeCardBtn.disabled = false;
                        payLabel.style.display = 'inline-block'
                        processingLabel.style.display = 'none'
                        return;
                    }

                    // Card number validation
                    if(!card_number || card_number === '') {
                        alert("Invalid card number.");
                        chargeCardBtn.disabled = false;
                        payLabel.style.display = 'inline-block'
                        processingLabel.style.display = 'none'
                        return;
                    }

                    // Expiry date validation
                    if(!expiry_date || expiry_date === '') {
                        alert("Invalid card expiry date.");
                        chargeCardBtn.disabled = false;
                        payLabel.style.display = 'inline-block'
                        processingLabel.style.display = 'none'
                        return;
                    }

                    // CVN validation
                    if(!cvn || cvn === '') {
                        alert("Invalid card CVN/CVV.");
                        chargeCardBtn.disabled = false;
                        payLabel.style.display = 'inline-block'
                        processingLabel.style.display = 'none'
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
                        // Note: Some cards will not show 3DS Auth.
                        should_authenticate: true
                    }, xenditResponseHandler);
                })

                // Capture the response from Xendit API to process the 3DS verification,
                // handle errors, and get the card token to finally charge the card.
                function xenditResponseHandler(err, creditCardToken) {
                    console.log(creditCardToken);

                    // If there's any error given by Xendit's API.
                    if (err) {
                        // Please check your console for more information.
                        console.log(err);

                        // Hide the 3DS authentication dialog.
                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'none';

                        // Show the errors on the form.
                        errorDiv.style.display = 'flex';
                        errorPre.textContent = err.error_code;

                        // Re-enable the 'pay with card' button.
                        reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)
                        return;
                    }

                    // When the card token's status is 'verified', it will now return
                    // the tokenized value of the customer's card. This token can
                    // now be used to finalize the payment and charge the card.
                    if (creditCardToken.status === 'VERIFIED') {
                        // Get the tokenized value of the card details.
                        var token = creditCardToken.id

                        // Hide the 3DS authentication dialog after successful authentication.
                        setIframeSource('payer-auth-url', "")
                        authDialog.style.display = 'none'

                        console.log('Tokenized value of the card details: ' + token);

                        // Create the hidden input that has the tokenized value of the card.
                        // So that the token_id value of the card will be include in the
                        // /api/charge-card POST request to finalize the payment.
                        var input = document.createElement('input')
                        input.setAttribute('type', 'hidden')
                        input.setAttribute('id', 'token_id')
                        input.setAttribute('name', 'token_id')
                        input.value = token
                        form.appendChild(input)
                        // form.querySelector('#amount-to-pay').value = "" // TODO: Remove this afterwards.

                        // Submit the form to your server with the tokenized
                        // value of the customer's card details.
                        chargeCard()

                    } else if (creditCardToken.status === 'IN_REVIEW') {
                        // With an IN_REVIEW status, this means your customer needs to
                        // authenticate their card via 3DS authentication. This will
                        // display the 3DS authentication dialog screen to enter
                        // the customer's OTP before they can continue.
                        authDialog.style.display = 'flex'
                        setIframeSource('payer-auth-url', creditCardToken.payer_authentication_url)
                    } else if (creditCardToken.status === 'FAILED') {
                        // With a FAILED status, the customer failed to verify their card,
                        // or there's with a problem with the issuing bank to authenticate
                        // the card. This will display an error code describing the problem.
                        // Please refer to Xendit's docs to learn more about error handling.
                        // Reference: https://developers.xendit.co/api-reference/#errors

                        // Hide the 3DS authentication dialog.
                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'none'

                        // Display an error
                        errorPre.textContent = creditCardToken.failure_reason;
                        errorDiv.style.display = 'flex';

                        // Re-enable the 'charge card' button.
                        reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)
                    }
                }

                // Execute the charging of the card.
                function chargeCard() {
                    console.log('Executing payment...');
                    var chargeResponseDiv = document.getElementById('charge-response')

                    // Make a POST request to the endpoint you specified where the
                    // CardPayment::makePayment() will be executed.
                    axios.post('/charge-card-example', {
                        amount: form.querySelector('#amount-to-pay').value,
                        token_id: form.querySelector('#token_id').value,

                        // Other optional data goes here...
                        // Accepted parameters reference:
                        // https://developers.xendit.co/api-reference/#create-charge

                        // descriptor: "The Amazing XYZ Corp.",
                        // external_id: 'your-own-external-id',
                        // currency: 'PHP',
                        // billing_details: [],
                        // metadata: []
                    })
                    .then(response => {
                        console.log(response);

                        // Display the API response from Xendit.
                        chargeResponseDiv.querySelector('pre').textContent = JSON.stringify(response.data, null, 2)

                        switch (response.data.status) {
                            // The CAPTURED status means the payment went successful.
                            // And the customer's card was successfully charged.
                            case 'CAPTURED':
                                chargeResponseDiv.style.display = 'block'
                                break;

                            case 'FAILED':
                                // With a FAILED status, the customer failed to verify their card,
                                // or there's with a problem with the issuing bank to authenticate
                                // the card. This will display an error code describing the problem.
                                // Please refer to Xendit's docs to learn more about error handling.
                                // Reference: https://developers.xendit.co/api-reference/#errors

                                // Hide the 3DS authentication dialog.
                                setIframeSource('payer-auth-url', "");
                                authDialog.style.display = 'none'

                                // Display the error.
                                errorPre.textContent = response.data.failure_reason;
                                errorDiv.style.display = 'flex';

                            default:
                                break;
                        }

                        reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)
                    })
                    .catch(error => {
                        const err = JSON.parse(error.response.data.message)
                        console.log(err);

                        // Show the API response output.
                        chargeResponseDiv.style.display = 'block'
                        chargeResponseDiv.querySelector('pre').textContent = JSON.stringify(err)

                        reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)
                    })
                }

                // Function to set the iframe src dynamically.
                function setIframeSource(iframeId, url) {
                    var iframe = document.getElementById(iframeId);
                    if (iframe) {
                        iframe.src = url;
                    } else {
                        console.error('Iframe not found');
                    }
                }

                // Re-enable the 'charge card' button.
                function reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel) {
                    chargeCardBtn.disabled = false
                    payLabel.style.display = 'inline-block'
                    processingLabel.style.display = 'none'
                }

            });
        </script>
    </body>
</html>
