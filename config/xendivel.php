<?php

return [
    'auto_create_xendit_external_id' => true,
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
    'xendit_secret_key' => env('XENDIT_SECRET_KEY', ''),

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
    'xendit_public_key' => env('XENDIT_PUBLIC_KEY', ''),

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
    'xendit_webhook_verification_token' => env('XENDIT_WEBHOOK_VERIFICATION_TOKEN', '')
];
