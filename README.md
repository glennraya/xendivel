![Project Logo](artwork/xendivel.jpg)

# Xendivel — A Laravel package for Xendit payment gateway

A Laravel package designed for seamless integration of [Xendit](https://xendit.co) payment gateway into your Laravel-powered applications or websites. It facilitates payments through credit cards, debit cards, and eWallets. Additionally, the package provides support for custom invoicing, queued invoice or refund email notifications, webhook event listeners and verification.

## Roadmap

The following features offered by Xendit are not currently included in this package but will be incorporated in future updates.

- Direct Bank Debit
- Promotions (coupon/discount codes)
- Subscription services
- Real-time push notifications for payment status (PusherJS, WebSockets)
- Disbursement APIs (for mass payment processing like employee payroll)
- PayLater
- QR Code payments

## Table of Contents

1. [Features](#features)
2. [Pre-requisites](#pre-requisites)
3. [Installation](#installation)
4. [Initial Setup](#initial-setup)
    - [Xendit API keys](#xendit-api-keys)
    - [Configure Mail (Optional)](#configure-mail-optional)
    - [Queues (Optional)](#queues-optional)
    - [Publish Config and Assets](#publish-config-and-assets)
	    - [Publish Individual Assets](#publish-individual-assets)
5. [Checkout Templates](#checkout-templates)
6. [Usage](#usage)
    - [Card Payments](#card-payments)
        - [Card Details Tokenization](#card-details-tokenization)
        - [Charge Credit Or Debit Cards](#charge-credit-or-debit-cards)
	        - [External ID](#external-id)
        - [Get Card Charge Transaction](#get-card-charge-transaction)
        - [Multi-Use Card Token](#multi-use-card-token)
    - [eWallet Payments](#ewallet-payments)
        - [Charge eWallet](#charge-ewallet)
        - [Get eWallet Charge](#get-ewallet-charge)
        - [Void eWallet Charge](#void-ewallet-charge)
    - [PDF Invoicing](#pdf-invoicing)
        - [Generate PDF Invoice](#generate-pdf-invoice)
        - [Download PDF Invoice](#download-pdf-invoice)
        - [Paper Size](#invoice-paper-size)
        - [Change Invoice Paper Size](#change-invoice-paper-size)
        - [Change Invoice Orientation](#change-invoice-orientation)
        - [Invoice Filenaming](#invoice-filename)
        - [Customizing PDF Invoice](#customizing-pdf-invoice-template)
        - [Sending PDF Invoice As Email Attachment](#sending-pdf-invoice-as-email-attachment)
	        - [Sending PDF Invoice for Card Payments](#sending-pdf-invoice-for-card-payments)
	        - [Sending PDF Invoice for eWallet Payments](#sending-pdf-invoice-for-ewallet-payments)
        - [Queue Invoice Email](#queue-invoice-email)
    - [Refunds](#refunds)
        - [Refund for Card Payments](#refund-for-card-payments)
        - [Refund for ewallet Payments](#refund-for-ewallet-payments)
        - [Get Refund Details](#get-refund-details)
        - [List All eWallet Refunds](#list-all-ewallet-refunds)
        - [Email Refund Notification](#email-refund-notifications)
    - [Webhook](#webhook)
        - [Listen to Webhook Event](#listen-to-webhook-event)
        - [Webhook Verification](#webhook-verification)
7. [Deploying to Production](#deploying-to-production)
8. [Tests](#tests)

## Features

- **Credit/Debit Cards** - Easily process payments through major credit or debit cards.
- **eWallet Payments** - Accepts a diverse range of eWallet payments based on your region (GCash, ShopeePay, PayMaya, GrabPay, etc.).
- **Custom Invoicing** - Provides built-in, highly customizable, and professional-looking invoice templates.
- **Queued Email Notifications** - Enables the use of markdown email templates and the option to schedule email notifications for background processing.
- **Webhooks** - Comes with built-in webhook event listeners from Xendit and ensures secure webhook verification.

### Pre-requisites

- PHP 8.0 or higher
- Laravel 9 or higher

## Installation

**Composer**

Xendivel utilizes Composer's package auto-discovery. All you need to do is to install Xendivel via composer and it will automatically register itself.

```bash
composer install glennraya/xendivel
```

## Initial Setup

### Xendit API Keys

Prior to using Xendivel, it's essential to have a Xendit account with properly configured API keys. Activation of your Xendit account for production is not necessary to test Xendivel's features. Test mode will be automatically enabled upon signing up for a Xendit account. Obtain your API keys from the following URLs:

- Secret Key/Public Key: https://dashboard.xendit.co/settings/developers#api-keys
- Webhook Verification Token: https://dashboard.xendit.co/settings/developers#webhooks

Generate `Money-In` `secret key` with `read` and `write` permissions from your dashboard API keys section.

After you acquired all these keys, please make sure you include them to your Laravel's `.env` file:

```ini
XENDIT_SECRET_KEY=your-secret-key
XENDIT_PUBLIC_KEY=your-public-key
XENDIT_WEBHOOK_VERIFICATION_TOKEN=your-webhook-verification-token
```

### Configure Mail (Optional)

Xendivel can send invoices to your customers as email attachments. To utilize this feature, ensure your [Laravel Mail](https://laravel.com/docs/10.x/mail#main-content)  is correctly set up. Before Xendivel dispatches invoice or refund email notifications, ensure your mail credentials are filled in your `.env` file.

```ini
MAIL_MAILER=smtp
MAIL_HOST=your-mailer-host
MAIL_PORT=your-mailer-port
MAIL_USERNAME=your-mailer-username
MAIL_PASSWORD=your-mailer-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="fromaddress@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Queues (Optional)

Xendivel facilitates the queueing of email processes for background execution. If you intend to employ queued emails for tasks such as invoicing or refund notifications, ensure that you have properly configured [Laravel Queues](https://laravel.com/docs/10.x/queues#main-content).

Then, make sure you have a queue worker running:

```bash
php artisan queue:work
```

Finally, please ensure that `queue_email` is set to `true` from your `.env` file:

```php
'queue_email' => false,
```

Once you have successfully configured Laravel's queues and enabled `queue_email` to `true`, Xendivel is now capable of dispatching invoice or refund emails to the queue for background execution, enabling your app to respond to other requests or do other tasks without waiting for the jobs to finish. **This will improve overall user experience!**

### Publish Config and Assets

All assets and configuration file must be published to its proper directory for Xendivel to function properly:

```bash
php artisan vendor:publish --tag=xendivel
```

Executing this command will publish Xendivel's assets to the following directories:

- Config file - `config` directory.
- Invoice template - `resources/views/vendor/xendivel` directory.
- Email templates - `resources/views/vendor/xendivel/emails` directory.
- Blade checkout template - `resources/views/vendor/xendivel` directory.
- Webhook Event and Listener - `app/Events` and `app/Listeners` directory respectively.

#### Publish Individual Assets

##### Configuration File
```bash
php artisan vendor:publish --tag=xendivel-config
```

##### Invoice Template
```bash
php artisan vendor:publish --tag=xendivel-invoice
```

##### Checkout (Blade)
```bash
php artisan vendor:publish --tag=xendivel-checkout-blade
```

##### Checkout (ReactJS)
```bash
php artisan vendor:publish --tag=xendivel-checkout-react
```

##### Checkout (ReactJS + TypeScript)
```bash
php artisan vendor:publish --tag=xendivel-checkout-react-typescript
```

##### Webhook Event Listener
```bash
php artisan vendor:publish --tag=xendivel-webhook-listener
```

## Checkout Templates

![Checkout Template](docs/image_assets/checkout-template.png)

Xendivel ships with a complete, fully working checkout template for cards and eWallet payments. The template include various variants such as **ReactJS component**, **ReactJS+TypeScript** component, and a regular **Blade** template and **VanillaJS**.

You can choose between the currently available template variants, you can even create your own.

### Example Checkout

Xendivel has a built-in route to preview the example checkout template (Blade). It's highly recommended to inspect the template as building the UI for checkout pages requires you to implement Xendit.js library for tokenization:

```
https://your-domain.test/xendivel/checkout/blade
```

> Note: Make sure you replace the `your-domain.test` with your own domain (whether local or production).

### Blade Template

We offer a standard Blade template for the checkout example, using VanillaJS. There's a built-in route allowing you to test this template at `/xendivel/checkout/blade`. You can access it through a URL like `https://your-domain.test/xendivel/checkout/blade`.

> NOTE: When you run the command `php artisan vendor:publish --tag=xendivel` the checkout blade template will be on your `/resources/views/vendor/xendivel/checkout.blade.php` directory.

### ReactJS + TypeScript component

Xendivel also have a checkout template component for **ReactJS** or **React+TypeScript** for those who are using front-end frameworks like React instead of regular Blade template.

```bash
php artisan vendor:publish --tag=xendivel-checkout-react-typescript

php artisan vendor:publish --tag=xendivel-checkout-react
```

These will be published under `/resources/js/vendor/xendivel/Checkout.tsx` for React+TypeScript or  `/resources/js/vendor/xendivel/Checkout.jsx` for plain ReactJS.

> [!IMPORTANT]
> After publishing either one of these templates, please make sure you filled up the `public key` section on these React templates. Since this is a public key, it's perfectly safe to publish it directly on your templates.

```javascript
// Set your 'public' key here.
Xendit.setPublishableKey(
    'your-public-key',
)
```

These templates demonstrate card tokenization, credit/debit card, and eWallet payments. They serve to guide your payment collection process for implementation in your front-end stack. Alternatively, use them as fully functional standalone templates if you wish.

## Usage

### Card Payments

#### Card Details Tokenization

Xendit employs a secure method for collecting credit or debit card details known as **tokenization**. The idea is instead of transmitting sensitive credit card information to your back-end, you utilize Xendit's JavaScript library to "tokenize" the card details before securely transmitting them to your back-end.

With this approach, there's no need to transmit your customer's card number, expiry date, and CVV (Card Verification Value) to the back-end for payment processing. Instead, these details are converted into secure **"tokens."** This ensures that even if leaked, your customer's credit/debit card information remains safe and confidential.

For more details, refer to Xendit's documentation below:

https://docs.xendit.co/credit-cards/integrations/tokenization

Xendivel provides convenient templates **(ReactJS, React+TypeScript, and Blade)** that serve as fully functional checkout components for card/eWallet payments, offering a solid starting point. Refer to the [Checkout templates](#checkout-templates) section for more details.

#### Charge Credit Or Debit Cards

The `Xendivel::payWithCard` function accepts the incoming request payload with the `token_id`, `amount`, and `authentication_id`:

Example Front-end POST Request Using Axios

```javascript
axios.post('/pay-with-card', {
    amount: 1200,
    token_id: 'card-token', // From card tokenization process.
    authentication_id: 'auth-id', // From authentication process.
    // Additional optional parameters:

    // external_id: 'your-custom-external-id',

    // descriptor: "Merchant Business Name",

    // currency: 'PHP',

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
})
// ...
```

Then, in your Laravel route/controller

`POST` Request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::post('/pay-with-card', function (Request $request) {
    $payment = Xendivel::payWithCard($request)
        ->getResponse();

    return $payment;
});
```

The `getResponse()` function ensures that you get a JSON response:

```json
{
  "status": "CAPTURED",
  "authorized_amount": 5198,
  "capture_amount": 5198,
  "currency": "PHP",
  "metadata": {},
  "credit_card_token_id": "656ed874edab5300169c3092",
  "business_id": "6551f678273a62fd8d86e25a",
  "merchant_id": "104019905",
  "merchant_reference_code": "656ed874edab5300169c3091",
  "external_id": "43565633-dd58-47ae-bbe6-648f78d6652c",
  "eci": "02",
  "charge_type": "SINGLE_USE_TOKEN",
  "masked_card_number": "520000XXXXXX1005",
  "card_brand": "MASTERCARD",
  "card_type": "CREDIT",
  "ucaf": "AJkBBkhgQQAAAE4gSEJydQAAAAA=",
  "descriptor": "XDT*JSON FAKERY",
  "authorization_id": "656ed87c23f3c20015e2fb95",
  "bank_reconciliation_id": "7017631974056110603955",
  "issuing_bank_name": "PT BANK NEGARA INDONESIA TBK",
  "cvn_code": "M",
  "approval_code": "831000",
  "created": "2023-12-05T07:59:58.453Z",
  "id": "656ed87e23f3c20015e2fb96",
  "card_fingerprint": "61d6ed632aa321002350e0b2"
}

```

Xendit accepts optional parameters such as **`billing_details`**, **`metadata`**,  **`external_id`**, **`currency`**,  and **`descriptor`** as demonstrated in the Axios request above. Please refer to Xendit's documentation to learn more about these parameters:

https://developers.xendit.co/api-reference/#create-charge

> You can also forward an invoice in PDF format as an email attachment to your customer's email address. Details about this process are covered in the [PDF Invoicing](#pdf-invoicing) section.

##### External ID
Xendit requires the inclusion of an `external_id` parameter in each credit/debit card charge. By default, Xendivel simplifies this process by generating a unique external ID using Ordered UUID v4 automatically for you.

https://laravel.com/docs/10.x/strings#method-str-ordered-uuid

Nevertheless, if you choose to create your own `external_id` for some reason, you can achieve this by setting the `auto_id` option in the `xendivel.php` config file to `false`.

Config file: `config/xendivel.php`

```php
 'auto_id' => false,
```

Subsequently, ensure that you manually provide your custom `external_id` for each card charge request.

```javascript
axios.post('/pay-with-card', {
    amount: 1200,
    token_id: 'card-token', // From card tokenization process.
    authentication_id: 'auth-id', // From authentication process.
+   external_id: 'your-custom-external-id', // Provide your own external id.
})
```

#### Get Card Charge Transaction

To retrieve the details of the card charge object, you must provide the `id` of the card charge (which should come from your database or your Xendit dashboard) as the first parameter, and the string `card` as the second parameter.

`GET` Request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::get('/payment', function () {
    // card charge id example: 659518586a863f003659b718
    $response = Xendivel::getPayment('card-charge-id', 'card')
        ->getResponse();

    return $response;
});
```

This endpoint will return a JSON response that shows important details like the `status` of the card charge, `charge_type`, `card_type`, `card_brand`, etc.

```json
{
  "created": "2020-01-08T04:49:08.815Z",
  "status": "CAPTURED",
  "business_id": "5848fdf860053555135587e7",
  "authorized_amount": 10000,
  "external_id": "test-pre-auth",
  "merchant_id": "xendit",
  "merchant_reference_code": "598942aabb91a4ec309e9a35",
  "card_type": "CREDIT",
  "masked_card_number": "400000XXXXXX0002",
  "charge_type": "SINGLE_USE_TOKEN",
  "card_brand": "VISA",
  "bank_reconciliation_id": "5132390610356134503009",
  "capture_amount": 9900,
  "descriptor": "My new store",
  "id": "659518586a863f003659b718"
}
```

#### Multi-Use Card Token

It's a common practice in e-commerce platforms to offer customers the convenience of saving their credit/debit card details for future use, eliminating the need for repetitive data entry during subsequent payments.

This functionality is achieved through the card tokenization process. If you've examined the [checkout templates](#checkout-templates) included with Xendivel, you'll find that this process has already been implemented for you.

Example JSON response for multi-use card token:

```json
{
  "status": "CAPTURED",
  "authorized_amount": 5198,
  "capture_amount": 5198,
  "currency": "PHP",
  "metadata": {},
  "credit_card_token_id": "65715e52689dc6001715bc57",
  "business_id": "6551f678273a62fd8d86e25a",
  "merchant_id": "104019905",
  "merchant_reference_code": "65715e530e502a00161aa2d9",
  "external_id": "f4270ddb-650d-4973-8786-1f5b4c048c76",
  "eci": "02",
  "charge_type": "MULTIPLE_USE_TOKEN",
  "masked_card_number": "520000XXXXXX1005",
  "card_brand": "MASTERCARD",
  "card_type": "CREDIT",
  "ucaf": "AJkBBkhgQQAAAE4gSEJydQAAAAA=",
  "descriptor": "XDT*JSON FAKERY",
  "authorization_id": "65715e5d689dc6001715bc5b",
  "bank_reconciliation_id": "7019285426096226603954",
  "issuing_bank_name": "PT BANK NEGARA INDONESIA TBK",
  "cvn_code": "M",
  "approval_code": "831000",
  "created": "2023-12-07T05:55:43.603Z",
  "id": "65715e5f689dc6001715bc60",
  "card_fingerprint": "61d6ed632aa321002350e0b2"
}

```
**IMPORTANT:** When `charge_type` is `MULTIPLE_USE_TOKEN`, you should make sure that you save the `credit_card_token_id` to your database. You will use this token to charge the card again in the future without re-entering the card details again using the same endpoint use the initially charge the card.

### eWallet Payments
Xendivel is compatible with all eWallet payment channels supported by Xendit. For further details, refer to the documentation at https://docs.xendit.co/ewallet, and explore Xendit's API reference at https://developers.xendit.co/api-reference/#create-ewallet-charge.

#### Charge eWallet

Example Axios post request:

```javascript
axios
    .post('/pay-via-ewallet', {
        // You can test different failure scenarios by using the 'magic amount' from Xendit.
        amount: parseInt(amount),
        currency: 'PHP',
        checkout_method: 'ONE_TIME_PAYMENT',
        channel_code: 'PH_GCASH',
        channel_properties: {
            success_redirect_url:
                'https://your-domain.test/ewallet/success',
            failure_redirect_url: 'https://your-domain.test/ewallet/failed',
        },
    })
    .then(response => {
        // Upon successful request, you will be redirected to the eWallet's checkout url.
        console.log(response.data)
        window.location.href =
            response.data.actions.desktop_web_checkout_url
    })
    /// ...
```

Then, on your Laravel route or controller:

`POST` Request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::post('/pay-via-ewallet', function (Request $request) {
    $response = Xendivel::payWithEwallet($request)
        ->getResponse();

    return $response;
});
```

In the example Axios request above you will be redirected to the eWallet payment provider's checkout page to complete the payment authorization there. If you are on development mode, you will see something like this:

![eWallet Payment Authorization Page](docs/image_assets/ewallet-authorization.png)


The resulting JSON response would look like this:

```json
{
    "created": "2023-12-09T07:51:17.926Z",
    "business_id": "6551f678273a62fd8d86e25a",
    "event": "ewallet.capture",
    "data": {
        "id": "ewc_5b2ad2c6-11a3-410a-b5ab-b41d16e39879",
        "basket": null,
        "status": "SUCCEEDED",
        "actions": {
            "qr_checkout_string": null,
            "mobile_web_checkout_url": "https://ewallet-mock-connector.xendit.co/v1/ewallet_connector/checkouts?token=clq1oqg032dn7a8hko1g",
            "desktop_web_checkout_url": "https://ewallet-mock-connector.xendit.co/v1/ewallet_connector/checkouts?token=clq1oqg032dn7a8hko1g",
            "mobile_deeplink_checkout_url": null
        },
        "created": "2023-12-09T07:51:06.63582Z",
        "updated": "2023-12-09T07:51:17.780894Z",
        "currency": "PHP",
        "customer": null,
        "metadata": null,
        "voided_at": null,
        "capture_now": true,
        "customer_id": null,
        "void_status": null,
        "callback_url": "https://pktuw9nrxn.sharedwithexpose.com/xendit/webhook",
        "channel_code": "PH_GCASH",
        "failure_code": null,
        "reference_id": "90c0c5f5-c6f0-4f2e-bf6c-f23763911f8a",
        "charge_amount": 1000,
        "capture_amount": 1000,
        "checkout_method": "ONE_TIME_PAYMENT",
        "refunded_amount": null,
        "payment_method_id": null,
        "channel_properties": {
            "failure_redirect_url": "https://package.test/ewallet/failed",
            "success_redirect_url": "https://package.test/ewallet/success"
        },
        "is_redirect_required": true,
        "payer_charged_amount": null,
        "shipping_information": null,
        "payer_charged_currency": null
    },
    "api_version": null
}

```

Upon the successful completion of the payment, you will be redirected to the designated success or failure page URL as specified in your axios request parameters (`success_redirect_url` or `failure_redirect_url`).

#### eWallet Charge Reference ID

Like the card charge, Xendit requires the inclusion of `reference_id` on the eWallet charge payload. Xendivel also handles this for you automatically by including Ordered UUID V4 on each payload upon request.

If you wish to add your own implementation for `reference_id`, like in the card payment, set the `auto_id` to `false` from your config file:

Config file: `config/xendivel.php`

```php
 'auto_id' => false,
```

And make sure you provide your own `reference_id` for every eWallet charge request:

```javascript
axios
    .post('/pay-via-ewallet', {
        // You can test different failure scenarios by using the 'magic amount' from Xendit.
        reference_id: 'your-own-reference-id',
        amount: parseInt(amount),
        currency: 'PHP',
        // Other params...
    })
    .then(response => {
        // Upon successful request, you will be redirected to the eWallet's checkout url.
        console.log(response.data)
        window.location.href =
            response.data.actions.desktop_web_checkout_url
    })
    /// ...
```

#### Responding to eWallet Charge Webhook Event

Before your app can receive webhook callbacks from Xendit. Please make sure that you properly setup a webhook endpoint from your Xendit's dashboard under **eWallet Payment Status**:

https://dashboard.xendit.co/settings/developers#webhooks

This is required for both development and production. For this purpose you can use tools like [Ngrok](https://ngrok.com) or [Expose](https://expose.dev) so your local project (`localhost`) can receive webhook callbacks from Xendit.

By default, Xendivel will listen to `xendit/webhook` URL for callbacks as defined in Xendivel's config file whenever you make an eWallet charge, refund, or void transactions. You have the option to change the default webhook URL if you prefer:

`config/xendivel.php`

```php
'webhook_url' => '/xendit/webhook', // You can change this to whatever you like.
```

Then, after you published Xendivel's webhook event listeners from [here](#publish-config-and-assets), you can now respond to the callback event from Xendit after a successful eWallet charge from the webhook listener located in `app/Listener/eWalletWebhookListener.php`:

```php
public function handle(eWalletEvents $event)
{
    // You can inspect the returned data from the webhoook in your logs file
    // storage/logs/laravel.log
    logger('Webhook data received: ', $event->webhook_data);

    // if($event->webhook_data['data']['status'] === 'SUCCEEDED') {
    //     $invoice_data = [
    //         // Invoice data...
    //     ];

    //     $email_invoice = new Xendivel();
    //     $email_invoice->emailInvoiceTo('glenn@example.com', $invoice_data)
    //         ->send();
    // }
}
```

You can now perform other tasks based on the payload of the callback such as interacting with your database, call other APIs, send an email, etc.

> **IMPORTANT:** Xendit will send a webhook event everytime you perform an eWallet charge, refund, or void transaction to the same webhook endpoint.

#### Exclude Xendit's Webhook Callback from CSRF Protection

You should also make sure you allow Xendit's callback from your CSRF protection, so any webhook callback Xendit sends to your application will be accepted by your routes. You can exclude the routes by adding their URIs to the `$except` property of the `VerifyCsrfToken` middleware:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
  /**
    * The URIs that should be excluded from CSRF verification.
    *
    * @var  array
    */

    protected  $except  = [
        '/xendit/*',
        'https://your-domain.com/xendit/*',
    ];
}
```

#### Get eWallet Charge

Fetch the details of an eWallet charge. The `Xendivel::getPayment` function accepts the **eWallet charge ID** as the first parameter, and the type of charge which is **ewallet** as the second parameter.

`GET` Request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::get('/get-ewallet-charge', function (Request $request) {
	$response = Xendivel::getPayment('ewc_65cbfb33-a1ea-4c32-a6f3-6f8202de9d6e', 'ewallet')
	    ->getResponse();

	return $response;
});
```

The JSON response would look similar to this:

```json
{
  "id": "ewc_bb8c3po-c3po-r2d2-c3po-r2d2c3por2d2",
  "business_id": "5f218745736e619164dc8608",
  "reference_id": "test-reference-id",
  "status": "PENDING",
  "currency": "IDR",
  "charge_amount": 1000,
  "capture_amount": 1000,
  "refunded_amount": null,
  "checkout_method": "ONE_TIME_PAYMENT",
  "channel_code": "ID_SHOPEEPAY",
  "channel_properties": {
    "success_redirect_url": "https://dashboard.xendit.co/register/1"
  },
  "actions": {
    "desktop_web_checkout_url": null,
    "mobile_web_checkout_url": null,
    "mobile_deeplink_checkout_url": "https://deeplinkcheckout.this/",
    "qr_checkout_string": "ID123XenditQRTest321DI"
  },
  "is_redirect_required": true,
  "callback_url": "https://calling-back.com/xendit/shopeepay",
  "created": "2017-07-21T17:32:28Z",
  "updated": "2017-07-21T17:32:28Z",
  "void_status": null,
  "voided_at": null,
  "capture_now": true,
  "customer_id": null,
  "payment_method_id": null,
  "failure_code": null,
  "basket": null,
  "metadata": {
    "branch_code": "tree_branch"
  }
}
```

#### Void eWallet Charge

`POST` Request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::post('/ewallet/void', function(Request $request) {
    // Example eWallet charge ID: ewc_e743d499-baa1-49f1-96c0-cc810890739b
    $response = Xendivel::void($request->ewallet_charge_id)
        ->getResponse();

    return $response;
});
```

With this Void API, you can nullify a successfully processed eWallet payment, ensuring that the entire original amount is refunded to the end user.

Voiding an eWallet charge is defined as the cancellation of eWallet payments created within the same day and before the **cutoff time of 23:50:00 (UTC+07:00 for Indonesia eWallets/ UTC+08:00 for Philippines eWallets)**.

-   Void API will only work for charges created via the `/ewallets/charges` API with `SUCCEEDED` status
-   Void API will return `PENDING` `void_status` in API response upon execution. A follow-up webhook will be sent to your system's URL when void has been processed successfully.

**To cancel eWallet payments after the aforementioned cutoff time, the [Refund API](#refunds) should be used.**

### PDF Invoicing

![Invoice Template](docs/image_assets/invoice.png)

Xendivel has the ability to generate professional and customizable PDF Invoices. You can preview the default invoice template by going to the route `/xendivel/invoice/template`.

```
https://your-domain.test/xendivel/invoice/template
```

**Note:** Remember to replace the `your-domain.test` with your domain.

PDF invoices are generated using standard **Laravel Blade** templates and Xendivel will convert this to PDF invoice for you. Since invoices are just regular Blade templates, you can pass data to the template just like you would on a [Laravel Blade](https://laravel.com/docs/10.x/blade#displaying-data) file.

#### Generate PDF Invoice

```php
use GlennRaya\Xendivel\Invoice;

Route::get('/xendivel/invoice/generate', function () {
    $invoice_data = [
        'invoice_number' => 1000023,
        'card_type' => 'VISA',
        'masked_card_number' => '400000XXXXXX0002',
        'merchant' => [
            'name' => 'Xendivel LLC',
            'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
            'phone' => '+63 971-444-1234',
            'email' => 'xendivel@example.com',
        ],
        'customer' => [
            'name' => 'Victoria Marini',
            'address' => 'Alex Johnson, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
            ['item' => 'MacBook Pro 16" M3 Max', 'price' => 2499, 'quantity' => 3],
            ['item' => 'Apple Pro Display XDR', 'price' => 5999, 'quantity' => 2],
            ['item' => 'Pro Stand', 'price' => 999, 'quantity' => 2],
        ],
        'tax_rate' => .12,
        'tax_id' => '123-456-789',
        'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
    ];

    return Invoice::make($invoice_data)
        ->save();
});
```

As you can see, the `Invoice::make` function accepts an associative array that contains the information you want to appear on the invoice, typically coming from your database. By default, it will be stored in the `/storage/app/invoices` directory of your Laravel app. You can change the location where you want to save your invoices by modifying the `invoice_storage_path` option in your Xendivel config file.

```
'invoice_storage_path' => storage_path('/app/invoices/')
```

> **IMPORTANT:** You should ensure the proper permission is set to the directory of your choice so Xendivel can store the invoice there.

#### Download PDF Invoice

You can immediately download the invoice to your customer's local machine instead of storing it your Laravel app's storage directory by calling the `Invoice::download` function:

```php
use GlennRaya\Xendivel\Invoice;

Route::get('/xendivel/invoice/download', function () {
    $invoice_data = [
        // Invoice data...
    ];

    return Invoice::download($invoice_data);
});
```

#### Invoice Paper Size

By default, Xendivel will generate PDF invoices in standard **Letter** paper size. Xendivel supports the following sizes:

```
Letter: 8.5in  x  11in
Legal: 8.5in  x  14in
Tabloid: 11in  x  17in
Ledger: 17in  x  11in
A0: 33.1in  x  46.8in
A1: 23.4in  x  33.1in
A2: 16.54in  x  23.4in
A3: 11.7in  x  16.54in
A4: 8.27in  x  11.7in
A5: 5.83in  x  8.27in
A6: 4.13in  x  5.83in
```

#### Change Invoice Paper Size

You can change the invoice size by invoking the `paperSize()` function when generating or downloading an invoice and specify the name of the paper size as the parameter:

```php
use GlennRaya\Xendivel\Invoice;

Route::get('/xendivel/invoice/download', function () {
    $invoice_data = [
        // Invoice data...
    ];

    return Invoice::make($invoice_data)
        ->paperSize('A4')
        ->download();
});
```

In this example, we can modify the invoice's paper size by invoking the `paperSize('A4')` function and indicating the desired paper size as its parameter.

#### Change Invoice Orientation

Your sentence is already well-constructed, but here's a slightly refined version:

You can also modify the orientation of the invoice; by default, it's in `portrait`. You can change it to `landscape` using the `orientation()` function.

```php
use GlennRaya\Xendivel\Invoice;

Route::get('/xendivel/invoice/download', function () {
    $invoice_data = [
        // Invoice data...
    ];

    return Invoice::make($invoice_data)
        ->paperSize('A4')
        ->orientation('landscape')
        ->download();
});
```

#### Invoice Filename

Whenever Xendivel generates, downloads or email an invoice to your customers, Xendivel will assign a unique filename using UUID v4 and appending the `-invoice.pdf` at the end of the filename. Here's an example:

```
c7ff9fa5-b629-4fc9-8e61-bd203c91ca65-invoice.pdf
```

If you want to customize the filenaming convention of Xendivel, you can easily do so by using the `fileName()` function of the `Invoice` class:

```php
use GlennRaya\Xendivel\Invoice;

Route::get('/xendivel/invoice/download', function () {
    $invoice_data = [
        // Invoice data...
    ];

    return Invoice::make($invoice_data)
        ->paperSize('A4')
        ->orientation('landscape')
        ->fileName('my-awesome-invoice-filename')
        ->download();
});
```

Now the generated invoice will have a filename that looks like:

```
my-awesome-invoice-filename-invoice.pdf
```

#### Customizing PDF Invoice Template

As previously mentioned, the PDF invoice template is essentially a standard **Laravel Blade** component. This implies that it is a conventional HTML/PHP file styled with [TailwindCSS](https://tailwindcss.com). Consequently, the task of adjusting both the styles and contents of the invoice is exceptionally straightforward, it's just like working on a regular HTML file.

Publish the invoice template to your `views` directory:

```bash
php artisan vendor:publish --tag=xendivel-invoice
```

> **NOTE: ** When you published Xendivel's assets from the **Publish Assets** section, your invoice template is already published in `resources/views/vendor/xendivel/invoice.blade.php`.

This command will publish the `invoice.blade.php` to your `resources/views/vendor/xendivel` directory. Upon inspecting the file, you will notice the `$invoice_data` variable. This variable contains the associative array that you passed to the view from previous examples.

Example section from the invoice template:

```php
{{-- Other data... --}}
<table class="border-collapse w-full">
    <thead>
        <tr class="text-left">
            <th class="pb-2">Description</th>
            <th class="pb-2">Qty</th>
            <th class="pb-2 text-right">Unit Price</th>
            <th class="px-0 pb-2 text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @php
            $total_price = 0;
        @endphp
        @foreach ($invoice_data['items'] as $item)
            @php
                $total_price += $item['price'] * $item['quantity'];
            @endphp
            <tr>
                <td class="py-1">{{ $item['item']}}</td>
                <td class="py-1">{{ $item['quantity'] }}</td>
                <td class="py-1 text-right">${{ number_format($item['price'], 2) }}</td>
                <td class="py-1 text-right">
                    ${{ number_format($item['price'] * $item['quantity'], 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{-- Other data... --}}
```

Since this is just a regular HTML/Blade template, there's no limit to the customizations you can make. You can define your own styles, modify the data being rendered, and even add images to the template. Xendivel will automatically convert this template into a PDF file upon generation, downloading, or when sent via email attachment.

#### Sending PDF Invoice As Email Attachment

It's a common practice that, following a purchase on an e-commerce website or app, customers receive an email detailing their transaction, accompanied by an attached invoice. Xendivel makes it easy to send an invoice to your customers after completing the purchase.

#### Sending PDF Invoice For Card Payments

```php
use GlennRaya\Xendivel\Xendivel;

Route::post('/checkout-email-invoice', function (Request $request) {
    $invoice_data = [
        'invoice_number' => 1000023,
        'card_type' => 'VISA',
        'masked_card_number' => '400000XXXXXX0002',
        'merchant' => [
            'name' => 'Stark Industries',
            'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
            'phone' => '+63 971-444-1234',
            'email' => 'xendivel@example.com',
        ],
        'customer' => [
            'name' => 'Mr. Glenn Raya',
            'address' => 'Alex Johnson, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'MacBook Pro 16" M3 Max', 'price' => $request->amount, 'quantity' => 1],
        ],
        'tax_rate' => .12,
        'tax_id' => '123-456-789',
        'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
    ];

    $payment = Xendivel::payWithCard($request)
        ->emailInvoiceTo('glenn@example.com', $invoice_data)
        ->send()
        ->getResponse();

    return $payment;
});
```

In this example, the `emailInvoiceTo()` function accepts the email address where you want to send the invoice as the first parameter, and the `$invoice_data` that holds the details about the invoice as the second parameter. The `send()` function will instruct Xendivel to send the email.

##### Email Subject and Message

![Email Invoice](docs/image_assets/email-invoice.png)

The above image is an example of the email with the PDF invoice attached that Xendivel will send by default. You can customize the subject and the email message itself:

##### Customize Subject

To change the default email's subject, you can use the `subject()` function:

```php
use GlennRaya\Xendivel\Xendivel;

Route::post('/checkout-email-invoice', function (Request $request) {
    $invoice_data = [
        // Invoice data...
    ];

    $payment = Xendivel::payWithCard($request)
        ->emailInvoiceTo('glenn@example.com', $invoice_data)
        ->subject('Thank you for your purchase!')
        ->send()
        ->getResponse();
    });
```

##### Customize Message
To change the default email's message, you can use the `message()` function:

```php
use GlennRaya\Xendivel\Xendivel;

Route::post('/checkout-email-invoice', function (Request $request) {
    $invoice_data = [
        // Invoice data...
    ];

    $payment = Xendivel::payWithCard($request)
        ->emailInvoiceTo('glenn@example.com', $invoice_data)
        ->subject('Thank you for your purchase!')
        ->message('We appreciate your business and look forward to serving you again. We have attached your invoice.')
        ->send()
        ->getResponse();
});
```

#### Sending PDF Invoice For eWallet Payments

When employing eWallet payments, the process of sending email invoices differs slightly. As your application must respond to a webhook callback for eWallet payments, it becomes necessary to incorporate the email invoice logic directly within the webhook listener.

Navigate to the `App/Listeners/eWalletWebhookListener.php` file and locate the `handle()` method. Within this method, implement the email invoice logic to ensure seamless integration with eWallet payments.

```php
use GlennRaya\Xendivel\Xendivel;

public function handle(eWalletEvents $event)
{
    // You can inspect the returned data from the webhoook in your logs file
    // storage/logs/laravel.log
    logger('Webhook data received: ', $event->webhook_data);

    // $invoice_data = [
        // Invoice data...
    // ];

    if($event->webhook_data['data']['status'] === 'SUCCEEDED') {
        $email_invoice = new Xendivel();
        $email_invoice->emailInvoiceTo('glenn@example.com', $invoice_data)
            ->send();
    }
}
```

Remember, when initiating an eWallet payment charge request, you have the option to include a `metadata` property.

Example:

```javascript
axios.post('/charge-ewallet', {
    amount: 1200,
    currency: 'PHP',
    checkout_method: 'ONE_TIME_PAYMENT',
    channel_code: 'PH_GCASH',
    channel_properties: {
        success_redirect_url: 'https://your-domain.test/ewallet/success',
        failure_redirect_url: 'https://your-domain.test/ewallet/failed',
    },

    metadata: {
        customer_id: 17,
        name: 'Glenn Raya',
        email: 'glenn@example.com'
    }
})
```

This allows you to include supplementary information with the payment. Meaning you can include your customer's ID, email address, phone numbers, etc. Enabling you to leverage it later when processing the webhook data.

Xendit API Reference:
https://developers.xendit.co/api-reference/#create-ewallet-charge

### Queue Invoice Email

Xendivel has the ability to queue email jobs for background processing, enhancing the responsiveness of your Laravel app by handling email tasks seamlessly in the background.

All you need to do is simple set the `queue_email` option from your `xendivel.php` config file to `true`.

```php
'queue_email' => true,
```

Of course, you need to make sure that you properly setup your Laravel queue driver and there's a queue worker running:

https://laravel.com/docs/10.x/queues#main-content

> IMPORTANT: Whenever you change the email templates that comes with Xendivel, Please be sure that you restart your queue workers so it could use your newly updated email templates.

### Refunds

Xendivel supports the refund API for both cards and eWallet payments and also has the ability to notify your customers about successful refunds.

#### Refund for Card Payments

The make refunds for payments via credit or debit cards, first you must get the charge transaction using the `getPayment()` method. The first parameter is the `id` of the charge, and the second parameter should be `card`.

Then, the `refund()` method's parameter value is the amount to be refunded. And of course, the `getResponse()` method is used to return the response from the API.

`POST` request:

```php
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Request;

Route::post('/refund', function (Request $request) {
	// Example charge id: 6593a0fb82742f0056f779fd

    $response = Xendivel::getPayment($request->charge_id, 'card')
        ->refund(3500)
        ->getResponse();

    return $response;
});
```

The response from this call should looks like this:

```json
{
    "credit_card_charge_id": "656eb63c23f3c20015e2f4eb",
    "amount": 5198,
    "external_id": "375b897a-8b75-4b94-a802-29a60febf589",
    "status": "REQUESTED",
    "merchant_reference_code": "656eb63123f3c20015e2f4e6",
    "uuid": "70941a1a-a2d4-4547-bb30-e3d4c163cf04",
    "currency": "PHP",
    "client_type": "API_GATEWAY",
    "created": "2023-12-05T05:50:38.701Z",
    "updated": "2023-12-05T05:50:38.701Z",
    "id": "656eba2eedab5300169c2b19",
    "fee_refund_amount": 0,
    "user_id": "6551f678273a62fd8d86e25a"
}
```

You can always check your Xendit's dashboard for all transactions made: https://dashboard.xendit.co/home

#### Refund for eWallet Payments

Requesting refunds for eWallet payments is almost the same as for the card refunds API. The only difference is the eWallet charge ID of course and the type of the refund which is `ewallet`

`POST` request

```php
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Request;

Route::post('/refund', function (Request $request) {
	// Example charge id: ewc_b5baef87-d7b5-4d5c-803b-b31e80529147

    $response = Xendivel::getPayment($request->charge_id, 'ewallet')
+       ->refund(3500)
+       ->getResponse();

    return $response;
});
```

#### Get Refund Details

Whether the refunds are successful or not, the details of the transactions are still recorded on your Xendit's admin account.

##### Get eWallet Refund Details

Get the status and details of a specific eWallet refund by its charge and refund ID:

`GET` request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::get('/get-ewallet-refund', function () {
    $response = Xendivel::getEwalletRefund('ewc_65cbfb33-a1ea-4c32-a6f3-6f5202dx9d6e', 'ewr_a96f9a27-8838-43bf-88f0-c0ade0aeeee3')
        ->getResponse();

    return $response;
});
```

Typically, the charge and refund ID should be stored to your database. This can be done when you received the webhook callback from Xendit.

#### List eWallet Refunds

Get the details of all eWallet refunds associated with a single eWallet charge transaction by the charge ID by using the `getListOfEwalletRefunds()` method and passing the eWallet `charge_id`:

`GET` request:

```php
use GlennRaya\Xendivel\Xendivel;

Route::get('/ewallet-refund-list', function () {
    $response = Xendivel::getListOfEwalletRefunds('ewc_65cbfb33-a1ea-4c32-a6f3-9f8201de9d6a')
        ->getResponse();

    return $response;
});
```

This will output a JSON response with a collection of refund transactions and status of each refund:

```json
{
    "data": [
        {
            "id": "ewr_a96f9a27-8838-43bf-88f0-c0ade0aeeee3",
            "charge_id": "ewc_65cbfb33-a1ea-4c32-a6f3-6f8202de9d6e",
            "status": "SUCCEEDED",
            "currency": "PHP",
            "channel_code": "PH_GCASH",
            "capture_amount": 1000,
            "refund_amount": 1000,
            "failure_code": null,
            "reason": "OTHERS",
            "refund_amount_to_payer": null,
            "payer_captured_amount": null,
            "payer_captured_currency": null,
            "created": "2023-12-28T07:47:40.24517Z",
            "updated": "2023-12-28T07:47:45.253443Z"
        }
    ]
}
```

#### Email Refund Notification

##### Card Refund Email Notification

Xendivel has the capability to automatically send an email to your customers following a refund request. This is achieved by invoking the `refund()` function first, and subsequently triggering the `emailRefundConfirmationTo()` function.

```php
use GlennRaya\Xendivel\Xendivel;

Route::get('/refund', function () {
    $response = Xendivel::getPayment('6595d0fg82741f0011f778fd', 'card')
        ->refund(3500)
        ->emailRefundConfirmationTo('glenn@example.com')
        ->send()
        ->getResponse();

    return $response;
});
```

You can also customize the `subject` and `message`:

```php
use GlennRaya\Xendivel\Xendivel;

Route::get('/refund', function () {
    $response = Xendivel::getPayment('6595d0fg82741f0011f778fd', 'card')
        ->refund(3500)
        ->emailRefundConfirmationTo('glenn@example.com')
        ->subject('Your refund is on the way!')
        ->message('We have successfully processed your refund! It should reflect on your account within 3 banking days.')
        ->send()
        ->getResponse();

    return $response;
});
```

### Webhook

#### Listen to Webhook Event

As of the moment only the eWallet **charges**, **refund**, and **void** transactions can receive a webhook callback event from Xendit and are discussed in these sections [Responding to eWallet Charge Webhook Event](#responding-to-ewallet-charge-webhook-event)

#### Webhook Verification

Xendit offers the option to sign the webhook events it transmits to your endpoints. This is achieved by incorporating a token in the `x-callback-token` header of each event. This feature enables you to authenticate that the events originated from Xendit and not from a third party.

For your convenience, Xendivel handles webhook verification for you automatically everytime Xendit sends a callback to your webhook endpoints. All you need to do is simply include your accounts unique webhook verification token on your `.env` file:

```ini
XENDIT_WEBHOOK_VERIFICATION_TOKEN=your-webhook-verification-token
```

You can obtain your webhook verification token from your dashboard under **Webhooks** section.

https://dashboard.xendit.co/settings/developers#webhooks

If you don't want to verify if the webhook callback is from Xendit, you can disable this feature by setting the `verify_webhook_origin` to `false` in Xendivel's config file:

Config file `config/xendivel.php`

```php
'verify_webhook_origin' => false,
```

>**IMPORTANT:** Verifying webhook origin is optional but it is **HIGHLY RECOMMENDED** for security reasons. This is to ensure that the webhook callback event legitimately comes from Xendit and not from third-parties or illegitimate services.

## Deploying to Production

When it's time to deploy your Laravel app, setting the `APP_ENV` from your `.env` file to `production` will disable the following Xendivel routes:

- `/xendivel/invoice/template` — The example invoice template.
- `/xendivel/checkout/blade` — The example checkout page.
- `/xendivel/invoice/generate` — Generate example PDF invoice.
- `/xendivel/invoice/download` — Download example PDF invoice.

These built-in Xendivel routes are meant for development purposes only. You should publish the checkout and invoice template to your views directory to use them on your Laravel app.

### Xendit Production Keys

When deploying to production, you should switch your Xendit's `secret_key`, `public_key`, and `webhook_verification_token` to production keys.

```ini
XENDIT_SECRET_KEY=your-production-secret-key
XENDIT_PUBLIC_KEY=your-production-public-key
XENDIT_WEBHOOK_VERIFICATION_TOKEN=your-production-webhook-verification-token
```
