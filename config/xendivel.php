<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External ID
    |--------------------------------------------------------------------------
    |
    | Xendit requires a unique external_id for transactions. This is generated
    | by default as a UUID v4. For custom external_id, set the default to
    | false and supply your own implementation for external_id.
    |
    | Max of 64 characters and at least 10 characters in length.
    |
    | Reference: https://developers.xendit.co/api-reference/#create-charge
    |
    */
    'auto_external_id' => true,

    /*
    |--------------------------------------------------------------------------
    | Invoice Meta Data
    |--------------------------------------------------------------------------
    |
    | These are optional values that will be included in every invoice issued
    | by your application or website. You are free to include them in your
    | .env file. The invoicing template will adjust accordingly to the
    | options you chose to include in your invoice template.
    |
    */
    'invoice' => [
        'email' => env('COMPANY_EMAIL', ''),
        'phone' => env('COMPANY_PHONE', ''),
        'address' => env('COMPANY_ADDRESS', ''),
        'website' => env('APP_URL', ''),
        'tax_id_number' => env('TAX_ID_NUMBER', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Default Storage Path
    |--------------------------------------------------------------------------
    |
    | The default storage location where the invoices will be saved. You can
    | customize the location where you want to generated invoices to be
    | saved. However, Xendivel provides a sensible default for you.
    |
    */

    'invoice_storage_path' => storage_path('/app/invoices/'),

    /*
    |--------------------------------------------------------------------------
    | Xendit Secret Key
    |--------------------------------------------------------------------------
    |
    | Secret key can perform any API request to Xendit on behalf of your
    | account. You should enter your secret key on your .env file.
    |
    | Reference: https://docs.xendit.co/api-integration/api-keys
    |
    */
    'secret_key' => env('XENDIT_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Xendit Public Key
    |--------------------------------------------------------------------------
    |
    | The Public key is meant to identify your account with Xendit. You can
    | safely publish this to your front-end JavaScript worry free. This
    | is also used to create tokens for authenticating cards.
    |
    | Reference: https://docs.xendit.co/api-integration/api-keys
    |
    */
    'public_key' => env('XENDIT_PUBLIC_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook Verification Token
    |--------------------------------------------------------------------------
    |
    | This is the webhook token that your app needs to validate to ensure the
    | callbacks are legitimately from Xendit and not from third parties.
    |
    | Reference: https://developers.xendit.co/api-reference/#security
    |
    */
    'webhook_verification_token' => env('XENDIT_WEBHOOK_VERIFICATION_TOKEN', ''),
];
