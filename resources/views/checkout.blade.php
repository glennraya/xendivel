<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Xendivel Cards Payment Template</title>

        @vite('resources/css/main.css')
    </head>
    <body class="antialiased relative h-screen grid bg-gray-300 pt-4">

        {{-- 3DS Auth Dialog (OTP) --}}
        <div id="payer-auth-wrapper" class="hidden justify-center items-center absolute top-0 left-0 w-full h-full bg-black bg-opacity-50 backdrop-blur-md z-10">
            <div class="flex flex-col max-w-2xl h-3/4 bg-white rounded-xl shadow-2xl overflow-hidden items-center justify-center p-8">
                <span class="font-bold text-xl w-3/4 text-center">Please confirm your identity by entering the one-time password (OTP) provided to you.</span>
                <iframe id="payer-auth-url" frameborder="0" class="w-full h-full"></iframe>
            </div>
        </div>
        {{-- End: 3DS Auth Dialog (OTP) --}}

        <div class="max-w-2xl flex flex-col gap-4 px-8 xl:max-w-7xl">
            <header class="text-sm">
                <h1 class="text-xl font-bold mb-2">Xendivel Checkout Example</h1>
                <p class="flex gap-3">
                    <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios" class="text-blue-600 border-b border-blue-600" target="_tab">Test card numbers</a>

                    <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions" class="text-blue-600 border-b border-blue-600" target="_tab">Test failed scenarios</a>
                </p>
            </header>

            <div class="flex flex-col gap-8 lg:flex-row">
                {{-- Payment Form --}}
                <div class="flex flex-col gap-4 w-full relative xl:w-1/2">
                    {{-- Example Product Lists (Hard-coded) --}}
                    <div class="flex flex-col bg-white p-8 rounded-xl shadow-sm divide-y divide-gray-200">
                        <h2 class="text-xl font-bold mb-4">Items in your bag</h2>
                        <div class="flex gap-4 py-4">
                            <img src="{{ asset('vendor/xendivel/images/macbook-pro.jpg') }}" alt="MacBook Pro" class="w-24 rounded-xl">
                            <div class="flex flex-col gap-2 w-full">
                                <span class="flex justify-between font-bold w-full">
                                    <span class="inline-block">MacBook Pro 16" M3 Max 1TB</span>
                                    <span class="inline-block font-normal text-gray-500">Qty 1</span>
                                </span>
                                <span class="text-gray-500">$3,999.00</span>
                            </div>
                        </div>
                        <div class="flex gap-4 py-4">
                            <img src="{{ asset('vendor/xendivel/images/iphone.jpg') }}" alt="iPhone" class="w-24 rounded-xl">
                            <div class="flex flex-col gap-2 w-full">
                                <span class="flex justify-between font-bold w-full">
                                    <span class="inline-block">iPhone 15 Pro Max</span>
                                    <span class="inline-block font-normal text-gray-500">Qty 1</span>
                                </span>
                                <span class="text-gray-500">$1,199.00</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between py-4">
                            <span>Your bag total is</span>
                            <span class="font-bold">$5,198.00</span>
                        </div>
                        <div class="flex items-center justify-between pt-4">
                            <span>Delivery</span>
                            <span>FREE</span>
                        </div>
                    </div>

                    {{-- Card payment form --}}
                    <form action="/checkout-example" method="POST" id="payment-form" class="grid grid-cols-6 gap-4 bg-white shadow-sm rounded-xl p-6">
                        @csrf
                        {{-- Amount to pay: This element was hidden --}}
                        <div class="gap-x-4 col-span-6">
                            <div class="flex flex-col w-full">
                                <div class="flex gap-4 items-center mb-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-blue-500">
                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                                    </svg>

                                    <span class="text-sm">You can enter a pre-defined <span class="font-bold">'failure charge amount'</span> to simulate failed charges. <a href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions" class="text-blue-500 border-b border-blue-500" target="_tab">Failed charge scenarios</a></span>
                                </div>

                                <label for="amount-to-pay" class="text-sm uppercase font-bold text-gray-500">Amount to pay</label>
                                <div class="flex flex-col">
                                    <div class="flex">
                                        <input type="text" id="amount-to-pay" name="amount" class="w-full bg-gray-100 p-3 rounded-xl outline-none focus:ring focus:ring-blue-400" placeholder="PHP" value="5198">
                                    </div>
                                    <span class="text-xs text-gray-500 mt-1"><strong>Note:</strong> The "amount to pay" field, doesn't need to be included in the checkout UI. This is shown here so you could easily test different amount values and failure scenarios.</span>
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
                    </form>

                </div>

                {{-- Payment Response --}}
                <div id="charge-response" class="hidden flex-col bg-white p-4 rounded-xl shadow-md gap-y-2 w-full xl:w-1/2">
                    <span class="font-bold">API Response:</span>
                    <span>When the <code class="font-bold">status</code> is <code class="font-bold">CAPTURED</code> it means that the payment is <span class="text-green-500 font-bold">successful</span>. You can now do something using this data, typically saving some or all data to the database, displaying a message to the customer about the payment, or generate invoice using Xendivel's own Invoice API.</span>
                    <pre class="bg-gray-100 p-4 rounded-xl mt-2 whitespace-pre-wrap text-sm"></pre>
                </div>

                {{-- Error Panel --}}
                <div id="errorDiv" class="hidden flex-col bg-white p-4 rounded-xl shadow-md gap-y-2 w-full xl:w-1/2">
                    <span class="font-bold">Error:</span>
                    <pre id="error-code" class="bg-gray-100 p-4 text-center rounded-xl whitespace-pre-wrap"></pre>
                    <pre id="error-message" class="bg-gray-100 p-4 text-center rounded-xl mt-2 whitespace-pre-wrap"></pre>
                    <span>Using this error code, you can give the user a customized message based on the error code. <span class="font-bold">You could also check your console for more information.</span></span>
                    <span class="font-medium mt-4">Xendit Documentation:</span>
                    <ul class="flex flex-col gap-2">
                        <li><a href="https://docs.xendit.co/credit-cards/understanding-card-declines#sidebar" class="text-blue-500 border-b border-blue-500" target="_tab">Understanding card declines</a></li>
                        <li><a href="https://developers.xendit.co/api-reference/#capture-charge" class="text-blue-500 border-b border-blue-500" target="_tab">Capture card — error codes</a></li>
                        <li><a href="https://developers.xendit.co/api-reference/#create-token" class="text-blue-500 border-b border-blue-500" target="_tab">Create token — error codes</a></li>
                    </ul>
                </div>

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
                var chargeResponseDiv = document.getElementById('charge-response')
                var errorDiv = document.getElementById('errorDiv')
                var errorCode = errorDiv.querySelector('#error-code')
                var errorMessage = errorDiv.querySelector('#error-message')

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

                    // Card CVN/CVV data is optional when creating card token.
                    // But it is highly recommended to include it.
                    // Reference: https://developers.xendit.co/api-reference/#create-token
                    if(form.querySelector("#card-cvn").value === '') {
                        chargeResponseDiv.style.display = 'none'

                        errorCode.textContent = ''
                        errorCode.style.display = 'none'
                        errorMessage.textContent = 'Card CVV/CVN is optional when creating card token, but highly recommended to include it.'
                        errorDiv.style.display = 'flex'

                        chargeCardBtn.disabled = false;
                        payLabel.style.display = 'inline-block'
                        processingLabel.style.display = 'none'
                        return;
                    }

                    // If the amount is less than 20.
                    if(amount_to_pay < 20) {
                        chargeResponseDiv.style.display = 'none'

                        errorCode.textContent = ''
                        errorCode.style.display = 'none'
                        errorMessage.textContent = 'The amount must be at least 20.'
                        errorDiv.style.display = 'flex'

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

                        // Change the currency you want to charge your customers in.
                        // This defaults to the currency of your Xendit account.

                        // NOTE: When performing a 'card charge', you need to input
                        // the currency you used as 'currency', parameter.

                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'USD',

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
                        errorCode.textContent = err.error_code;
                        errorMessage.textContent = err.message;

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

                        // Submit the form to your server with the tokenized
                        // value of the customer's card details.
                        chargeCard()

                    } else if (creditCardToken.status === 'IN_REVIEW') {
                        // With an IN_REVIEW status, this means your customer needs to
                        // authenticate their card via 3DS authentication. This will
                        // display the 3DS authentication dialog screen to enter
                        // the customer's OTP before they can continue.
                        authDialog.style.display = 'flex'

                        // Set the URL of the OTP iframe contained in "payer_authentication_url"
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
                        errorCode.textContent = creditCardToken.failure_reason;
                        errorMessage.style.display = 'none'
                        errorDiv.style.display = 'flex';

                        // Re-enable the 'charge card' button.
                        reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)
                    }
                }

                // Execute the charging of the card.
                function chargeCard() {
                    console.log('Executing payment...');

                    // Make a POST request to the endpoint you specified where the
                    // CardPayment::makePayment() will be executed.
                    axios.post('/checkout-email-invoice', {
                        amount: form.querySelector('#amount-to-pay').value,
                        token_id: form.querySelector('#token_id').value,

                        // NOTE: When you specify the currency from the card 'tokenization' process
                        // to a different one other than the default, (e.g. USD), you need
                        // to explicitly input the currency you used from the 'tokenization' step.

                        // This defaults to the currency of your Xendit account.

                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'USD',

                        // Other optional data goes here...
                        // Accepted parameters reference:
                        // https://developers.xendit.co/api-reference/#create-charge

                        // descriptor: "Merchant Business Name...",

                        // if 'auto_external_id' is set to 'true' in xendivel config, you
                        // must supply your own external_id here:
                        // external_id: 'your-own-external-id',

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
                                errorDiv.style.display = 'none'
                                break;

                            // With a FAILED status, the customer failed to verify their card,
                            // or there's with a problem with the issuing bank to authenticate
                            // the card. This will display an error code describing the problem.
                            // Please refer to Xendit's docs to learn more about error handling.
                            // Reference: https://developers.xendit.co/api-reference/#errors
                            case 'FAILED':

                                // Hide the 3DS authentication dialog.
                                setIframeSource('payer-auth-url', "");
                                authDialog.style.display = 'none'

                                chargeResponseDiv.style.display = 'none'

                                // Display the error.
                                // status.textContent = response.data.status;
                                errorCode.textContent = response.data.failure_reason;
                                errorMessage.style.display = 'none'
                                errorDiv.style.display = 'flex';

                                break;

                            default:
                                break;
                        }

                        reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)
                    })
                    .catch(error => {
                        console.log(error.response.status);

                        if(error.response.status === 500) {
                            chargeResponseDiv.style.display = 'none'

                            // Show the error response
                            errorCode.style.display = 'block'
                            errorCode.textContent = error.response.data.exception

                            errorMessage.style.display = 'block'
                            errorMessage.textContent = error.response.data.message

                            errorDiv.style.display = 'flex';

                            reEnableSubmitButton(chargeCardBtn, payLabel, processingLabel)

                            return;
                        }

                        const err = JSON.parse(error.response.data.message)
                        console.log(err);

                        chargeResponseDiv.style.display = 'none'

                        // Show the error response from Xendit's API
                        errorCode.style.display = 'block'
                        errorCode.textContent = err.error_code

                        errorMessage.style.display = 'block'
                        errorMessage.textContent = err.message

                        errorDiv.style.display = 'flex';

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
