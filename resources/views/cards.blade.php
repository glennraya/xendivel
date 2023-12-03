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
        @include('vendor.xendivel.views.partials.checkout-partials.otp-modal')
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
                    @include('vendor.xendivel.views.partials.checkout-partials.product-list')

                    {{-- Card payment form --}}
                    @include('vendor.xendivel.views.partials.checkout-partials.card-payment')
                </div>

                @include('vendor.xendivel.views.partials.checkout-partials.api-responses')
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
                        // to explicitly input the currency you used in the 'tokenization' step.

                        // This defaults to the currency of your Xendit account.

                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'USD',

                        // Data for invoicing example:
                        // Other data you want to include here for the invoice.
                        company_name: 'JuanTech LTD',
                        company_address: '#1 Tamaraw St., Bonifacio Global City, Taguig, Philippines 7221',
                        company_phone: '+63 917-123-4567',
                        company_email: 'xendivel@example.com',
                        items: [
                            { 'item': 'MacBook Pro 16" M3 Max', 'quantity': 1, 'price': '3999'},
                            { 'item': 'iPhone 15 Pro Max', 'quantity': 1, 'price': '1199'},
                        ],
                        footer_note: 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.'

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
                                errorDiv.style.display = 'none'
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
