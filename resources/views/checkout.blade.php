<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Xendivel Cards Payment Template</title>

        @vite('resources/css/checkout.css')
    </head>
    <body class="antialiased relative h-screen grid bg-gray-300">

        {{-- OTP Dialog --}}
        <div id="payer-auth-wrapper" class="hidden fixed left-0 top-0 z-10 h-full w-full items-center justify-center bg-black bg-opacity-75 backdrop-blur-md">
            <div class="flex h-3/4 max-w-2xl flex-col items-center justify-center overflow-hidden rounded-xl bg-white p-8 shadow-2xl">
                <span class="w-3/4 text-center text-xl font-bold">
                    Please confirm your identity by entering the
                    one-time password (OTP) provided to you.
                </span>
                <iframe id="payer-auth-url" class="h-full w-full"></iframe>
            </div>
        </div>

        <div class="container mt-8 mx-auto flex flex-col items-center gap-4">
            <header class="text-sm">
                <h1 class="mb-2 text-xl font-bold">
                    Xendivel Checkout Example
                </h1>
                <p class="flex gap-3">
                    <a
                        href="https://docs.xendit.co/credit-cards/integrations/test-scenarios"
                        class="border-b border-blue-600 text-blue-600"
                        target="_tab"
                    >
                        Test card numbers
                    </a>

                    <a
                        href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions"
                        class="border-b border-blue-600 text-blue-600"
                        target="_tab"
                    >
                        Test failed scenarios
                    </a>
                </p>
            </header>

            {{-- Payment form --}}
            <div class="mt-8 flex w-[500px] flex-col rounded-md border border-gray-300 bg-white">
                <div class="flex w-full text-sm">
                    <span
                        id="card-payment"
                        class="flex-1 cursor-pointer p-4 text-center bg-white font-bold text-black rounded-tl-md"
                    >
                        Credit/Debit Card
                    </span>
                    <span
                        id="ewallet-payment"
                        class="flex-1 cursor-pointer p-4 text-center rounded-tr-md text-black bg-gray-200"
                    >
                        E-Wallet
                    </span>
                </div>

                {{-- Cards payment --}}
                <div class="p-8 pb-0 flex">
                    <input id="amount-to-pay" placeholder="Amount to pay" type="text" class="rounded-md border border-gray-300 mb-2 w-full">
                </div>
                <div
                    id="card-panel"
                    class="flex flex-col rounded-bl-md rounded-br-md bg-white p-8 pt-0 shadow-md font-medium"
                >
                    <form
                        id="payment-form"
                        class="mb-4 flex flex-col overflow-hidden rounded-md border border-gray-300 bg-gray-100 shadow-sm"
                    >
                        @csrf
                        <div class="flex border-b border-gray-300">
                            <div class="flex w-full flex-col">
                                <div class="flex flex-col">
                                    <div class="relative flex">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            data-slot="icon"
                                            class="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                                        >
                                            <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                                            <path
                                                fill-rule="evenodd"
                                                d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <input
                                            type="text"
                                            id="card-number"
                                            name="card-number"
                                            class="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                            placeholder="Card number"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex w-1/2">
                                    <input
                                        type="text"
                                        id="card-exp-month"
                                        name="card-exp-month"
                                        class="w-14 border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                        placeholder="MM"
                                        maxLength="2"
                                    />
                                    <span class="self-center px-3 font-bold text-gray-500">
                                        /
                                    </span>
                                    <input
                                        type="text"
                                        id="card-exp-year"
                                        name="card-exp-year"
                                        class="w-auto border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                        placeholder="YYYY"
                                        maxLength="4"
                                    />
                                </div>
                                <div class="relative flex w-1/2 border-l border-gray-300">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        data-slot="icon"
                                        class="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <input
                                        type="text"
                                        id="card-cvn"
                                        name="card-cvn"
                                        class="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                        placeholder="CVV"
                                        maxLength="4"
                                    />
                                </div>
                            </div>
                        </div>
                    </form>
                    <div
                        id="errorDiv"
                        class="hidden col-span-6 mb-4 justify-center gap-x-4 rounded-md bg-red-200 p-3 font-medium text-red-800"
                    >
                        <span id="error-message">Card error</span>
                    </div>
                    <div class="col-span-6 flex items-center gap-x-4 rounded-md border border-gray-300 p-4 text-sm font-medium">
                        <label
                            for="save-card-checkbox"
                            class="order-2"
                        >
                            Save my information for faster checkout
                        </label>
                        <input
                            id="save-card-checkbox"
                            type="checkbox"
                        />
                    </div>
                    <div class="mt-4 flex flex-col gap-4">
                        <button
                            id="charge-card-btn"
                            class="w-full rounded-md text-sm bg-black py-3 font-bold uppercase text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black"
                        >
                            Charge Card
                        </button>
                    </div>
                </div>

                {{-- eWallet payment --}}
                <div
                    id="ewallet-panel"
                    class="hidden w-full grid-cols-6 gap-4 rounded-bl-md rounded-br-md bg-white p-8 pt-2 shadow-sm"
                >
                    <button
                        id="charge-ewallet-btn"
                        class="col-span-6 text-sm uppercase rounded-md bg-black py-3 font-bold text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-green-600"
                    >
                        Charge with eWallet
                    </button>
                </div>
            </div>

            {{-- API response --}}
            <div id="charge-response" class="my-2 hidden w-[500px] flex-col whitespace-nowrap rounded-md border border-gray-300 bg-white p-8 shadow-md">
                <span class="mb-2 text-lg font-bold">
                    Xendit API Response
                </span>

                <span id="multi-use-token-notice" class="mb-2 hidden items-center gap-4 whitespace-pre-wrap text-sm">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        data-slot="icon"
                        class="h-8 w-8 text-blue-600"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="flex-1">If you choose to save this card for future transactions, make sure to store the <code class="rounded bg-gray-200 px-2 py-1 text-xs">credit_card_token_id </code> in your database. This token is necessary for future charges without re-entering card details.</span>
                </span>

                <pre id="api-response" class="flex flex-col w-full whitespace-pre-wrap rounded-md bg-gray-100 p-4 text-xs items-center justify-center leading-relaxed">api response</pre>
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
                '{{ getenv('XENDIT_PUBLIC_KEY_TEST') }}'
            );
        </script>

        {{-- Process for tokenizing the card details, validation
             and charging the credit/debit card. --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Payment options
                var cardPayment = document.getElementById('card-payment')
                var ewalletPayment = document.getElementById('ewallet-payment')
                var cardPanel = document.getElementById('card-panel')
                var ewalletPanel = document.getElementById('ewallet-panel')

                // Form elements
                var form = document.getElementById('payment-form');
                var saveCardCheckBox = document.getElementById("save-card-checkbox")
                var chargeCardBtn = document.getElementById('charge-card-btn')
                var chargeEwalletBtn = document.getElementById('charge-ewallet-btn')
                var save_card = false

                // Banners
                var multiUseToken = document.getElementById('multi-use-token-notice')

                // 3DS/OTP Dialog
                var authDialog = document.getElementById('payer-auth-wrapper')

                // API Responses (Success/Error)
                var chargeResponseDiv = document.getElementById('charge-response')
                var errorDiv = document.getElementById('errorDiv')
                var errorCode = errorDiv.querySelector('#error-code')
                var errorMessage = errorDiv.querySelector('#error-message')

                // Payment mode toggle buttons
                cardPayment.addEventListener('click', function(event){
                    event.preventDefault()
                    ewalletPanel.style.display = 'none'
                    cardPanel.style.display = 'flex'
                    ewalletPayment.classList.add('bg-gray-200')
                    ewalletPayment.classList.remove('bg-white')
                    cardPayment.classList.remove('bg-gray-200')
                    cardPayment.classList.add('bg-white')
                    ewalletPayment.classList.remove('font-bold')
                    cardPayment.classList.add('font-bold')
                })

                ewalletPayment.addEventListener('click', function(event){
                    event.preventDefault()
                    cardPanel.style.display = 'none'
                    ewalletPanel.style.display = 'grid'
                    ewalletPayment.classList.add('bg-white')
                    ewalletPayment.classList.remove('bg-gray-200')
                    cardPayment.classList.remove('bg-white')
                    cardPayment.classList.add('bg-gray-200')
                    ewalletPayment.classList.add('font-bold')
                    cardPayment.classList.remove('font-bold')
                })

                // Toggle save card checkbox: If you want the card to be "multi-use", check this option.
                saveCardCheckBox.addEventListener('change', function() {
                    if (this.checked) {
                        save_card = true

                    } else {
                        save_card = false
                    }
                });

                // Charge card button
                chargeCardBtn.addEventListener('click', function(event) {
                    event.preventDefault();

                    // Disable the submit button to prevent repeated clicks
                    // var chargeCardBtn = form.querySelector('.submit');
                    chargeCardBtn.disabled = true;

                    // Show the 'processing...' label to indicate the tokenization is processing.
                    // payLabel.style.display = 'none'
                    // processingLabel.style.display = 'inline-block'

                    // Card validation: The 'card_number', 'expiry_date' and 'cvn'
                    // vars returns boolean values (true, false).
                    var card_number = Xendit.card.validateCardNumber(form.querySelector('#card-number').value);
                    var expiry_date = Xendit.card.validateExpiry(
                        form.querySelector("#card-exp-month").value,
                        form.querySelector("#card-exp-year").value
                    )

                    var cvn = Xendit.card.validateCvn(form.querySelector("#card-cvn").value)
                    var amount_to_pay = document.getElementById("amount-to-pay").value

                    // Card CVN/CVV data is optional when creating card token.
                    // But it is highly recommended to include it.
                    // Reference: https://developers.xendit.co/api-reference/#create-token
                    if(form.querySelector("#card-cvn").value === '') {
                        // chargeResponseDiv.style.display = 'none'

                        // errorCode.textContent = ''
                        // errorCode.style.display = 'none'
                        errorMessage.textContent = 'Card CVV/CVN is optional when creating card token, but highly recommended to include it.'
                        errorDiv.style.display = 'flex'

                        chargeCardBtn.disabled = false;
                        // payLabel.style.display = 'inline-block'
                        // processingLabel.style.display = 'none'
                        return;
                    }

                    // If the amount is less than 20.
                    if(amount_to_pay < 20) {
                        // chargeResponseDiv.style.display = 'none'

                        // errorCode.textContent = ''
                        // errorCode.style.display = 'none'
                        errorMessage.textContent = 'The amount must be at least 20.'
                        errorDiv.style.display = 'flex'

                        chargeCardBtn.disabled = false;
                        // payLabel.style.display = 'inline-block'
                        // processingLabel.style.display = 'none'

                        return;
                    }

                    // Request a token from Xendit
                    Xendit.card.createToken({
                        // Card details and the amount to pay.
                        amount: document.getElementById('amount-to-pay').value,
                        card_number: form.querySelector('#card-number').value,
                        card_exp_month: form.querySelector('#card-exp-month').value,
                        card_exp_year: form.querySelector('#card-exp-year').value,
                        card_cvn: form.querySelector('#card-cvn').value,

                        // Change the currency you want to charge your customers in.
                        // This defaults to the currency of your Xendit account.
                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'USD',

                        // Determine if single-use or multi-use card token.
                        // Value is determined by "Save card for future use" checkbox.
                        // Multi-use token is for saving the card token for
                        // future charges without entering card details again.
                        is_multiple_use: save_card === true ? true : false,

                        // 3DS authentication (OTP).
                        // Note: Some cards will not show 3DS Auth.
                        should_authenticate: true
                    }, tokenizationHandler);

                    return
                })

                chargeEwalletBtn.addEventListener('click', function(event) {
                    event.preventDefault()
                    chargeEwallet()
                })

                // Capture the response from Xendit API to process the 3DS verification,
                // handle errors, and get the card token for single charge or multi-use.
                function tokenizationHandler(err, creditCardToken) {
                    // If there's any error given by Xendit's API.
                    if (err) {
                        // Please check your console for more information.
                        console.log('Error: ', err);
                        chargeCardBtn.disabled = false

                        // Hide the 3DS authentication dialog.
                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'none';

                        // Show the errors on the form.
                        errorDiv.style.display = 'flex';
                        // errorCode.textContent = err.error_code;
                        errorMessage.textContent = err.message;

                        return;
                    }

                    console.log('Card token:' + creditCardToken.id);
                    console.log(creditCardToken);

                    var card_token = creditCardToken.id
                    var authentication_id = creditCardToken.authentication_id

                    // Perform authentication of the card token. (Single use or multi-use tokens)
                    Xendit.card.createAuthentication({
                        amount: document.getElementById('amount-to-pay').value,
                        token_id: card_token,
                        // token_id: '65716539689dc6001715bd1f', // Test: Multi-use token
                    }, authenticationHandler)
                }

                // When "save card for future use" was enabled, this means you have to save the 'card_token'
                // to your database so it could be used again in the future.
                function authenticationHandler(err, response) {
                    console.log(err);

                    if(err !== null && typeof err === 'object' && Object.keys(err).length > 0) {
                        // Display an error
                        errorCode.textContent = err.error_code
                        errorMessage.textContent = err.message
                        errorMessage.style.display = 'block'
                        errorDiv.style.display = 'flex';
                        return
                    }

                    var card_token = response.credit_card_token_id
                    var authentication_id = response.id

                    switch (response.status) {
                        case 'VERIFIED':
                            console.log('VERIFIED: ', response);
                            console.log('Authentication token: ', response.id);

                            // Function to charge the card.
                            chargeCard(authentication_id, card_token)
                            break

                        case 'IN_REVIEW':
                            // With an IN_REVIEW status, this means your customer needs to
                            // authenticate their card via 3DS authentication. This will
                            // display the 3DS authentication dialog screen to enter
                            // the customer's OTP before they can continue.
                            console.log('IN_REVIEW: ', response);
                            authDialog.style.display = 'flex'

                            // Set the URL of the OTP iframe contained in "payer_authentication_url"
                            setIframeSource('payer-auth-url', response.payer_authentication_url)
                            break

                        case 'FAILED':
                            // With a FAILED status, the customer failed to verify their card,
                            // or there's with a problem with the issuing bank to authenticate
                            // the card. This will display an error code describing the problem.
                            // Please refer to Xendit's docs to learn more about error handling.
                            // Reference: https://developers.xendit.co/api-reference/#errors
                            console.log('FAILED: ', response);

                            // Hide the 3DS authentication dialog.
                            setIframeSource('payer-auth-url', "");
                            authDialog.style.display = 'none'

                            // Display an error
                            chargeResponseDiv.querySelector('pre').textContent = JSON.stringify(response, null, 2)
                            chargeResponseDiv.style.display = 'flex'
                            errorMessage.style.display = 'none'

                            // Re-enable the 'charge card' button.
                            chargeCardBtn.disabled = false
                            break

                        default:
                            break
                    }
                }

                // Charge card
                function chargeCard(auth_id, card_token) {
                    console.log('Executing payment...');
                    console.log('Authentication ID: ' + auth_id)

                    // Make a POST request to the endpoint you specified where the
                    // Xendivel::makePayment() will be executed.
                    axios.post('/checkout-email-invoice', {
                        amount: document.getElementById('amount-to-pay').value,
                        token_id: card_token,
                        authentication_id: auth_id,

                        // NOTE: When you specify the currency from the card 'tokenization' process
                        // to a different one other than the default, (e.g. USD), you need
                        // to explicitly input the currency you used from the 'tokenization' step.

                        // This defaults to the currency of your Xendit account.

                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'PHP',

                        // Other optional data goes here...
                        // Accepted parameters reference:
                        // https://developers.xendit.co/api-reference/#create-charge
                        // descriptor: "Merchant Business Name",

                        // if 'auto_id' is set to 'false' in xendivel config, you
                        // must supply your own unique external_id here:
                        // external_id: 'your-custom-external-id',

                        // Billing details is optional. But required if card needs to be verified by
                        // AVS (Address Verification System). Typically for USA/Canadian/UK cards.
                        // billing_details: {
                        //     given_names: 'Glenn',
                        //     surname: 'Raya',
                        //     email: 'glenn@example.com',
                        //     mobile_number: '+639171234567',
                        //     phone_number: '+63476221234',
                        //     address:{
                        //         street_line1: 'Ivory St. Greenfield Subd.',
                        //         street_line2: 'Brgy. Coastal Ridge',
                        //         city: 'Balanga City',
                        //         province_state: 'Bataan',
                        //         postal_code: '2100',
                        //         country: 'PH'
                        //     }
                        // },

                        // metadata: {
                        //     store_owner: 'Glenn Raya',
                        //     nationality: 'Filipino',
                        //     product: 'MacBook Pro 16" M3 Pro',
                        //     other_details: {
                        //         purpose: 'Work laptop',
                        //         issuer: 'Xendivel LTD',
                        //         manufacturer: 'Apple',
                        //         color: 'Silver'
                        //     }
                        // }
                    })
                    .then(response => {
                        console.log(response);

                        // Display the API response from Xendit.
                        chargeResponseDiv.querySelector('pre').textContent = JSON.stringify(response.data, null, 2)

                        switch (response.data.status) {
                            // The CAPTURED status means the payment went successful.
                            // And the customer's card was successfully charged.
                            case 'CAPTURED':
                                chargeResponseDiv.style.display = 'flex'

                                if(save_card === true) {
                                    multiUseToken.style.display = 'flex'
                                }

                                errorDiv.style.display = 'none'
                                chargeCardBtn.disabled = false

                                // Hide the 3DS authentication dialog after successful authentication/payment.
                                setIframeSource('payer-auth-url', "")
                                authDialog.style.display = 'none'
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

                                chargeResponseDiv.style.display = 'flex'
                                chargeCardBtn.disabled = false

                                // Display the error.
                                // errorCode.textContent = response.data.failure_reason;
                                errorMessage.style.display = 'none'
                                errorDiv.style.display = 'flex';

                                break;

                            default:
                                break;
                        }
                    })
                    .catch(error => {
                        console.log(error.response.status);

                        if(error.response.status === 500) {
                            chargeResponseDiv.style.display = 'none'

                            // Show the error response
                            // errorCode.style.display = 'block'
                            // errorCode.textContent = error.response.data.exception

                            errorMessage.style.display = 'block'
                            errorMessage.textContent = error.response.data.message

                            errorDiv.style.display = 'flex';

                            chargeCardBtn.disabled = false

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

                        chargeCardBtn.disabled = false
                    })
                }

                // Charge e-wallet
                function chargeEwallet() {
                    axios.post('/pay-via-ewallet', {
                        // You can test different failure scenarios by using the 'magic amount' from Xendit.
                        amount: parseInt(document.getElementById('amount-to-pay').value),
                        currency: 'PHP',
                        checkout_method: 'ONE_TIME_PAYMENT',
                        channel_code: 'PH_GCASH',
                        channel_properties: {
                            success_redirect_url: '{{ getenv('APP_URL') }}/ewallet/success',
                            failure_redirect_url: '{{ getenv('APP_URL') }}/ewallet/failed',
                        },
                    })
                    .then(response => {
                        // Upon successful request, you will be redirected to the eWallet's checkout url.
                        console.log('Success response: ', response.data)
                        window.location.href =
                            response.data.actions.desktop_web_checkout_url
                    })
                    .catch(error => {
                        const err = JSON.parse(error.response.data.message)
                        console.log('Error response: ', err.message)
                        console.log('Errors: ', err.errors)

                        // errorMessage.style.display = 'block'
                        // errorMessage.textContent = error.response.data.message

                        // errorDiv.style.display = 'flex';
                        chargeResponseDiv.querySelector('pre').textContent = err.message
                        chargeResponseDiv.style.display = 'flex'

                        chargeCardBtn.disabled = false
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
            });
        </script>
    </body>
</html>
