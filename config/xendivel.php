<?php

return [
    'xendit_secret_key' => env('XENDIT_SECRET_KEY', ''),
    'xendit_public_key' => env('XENDIT_PUBLIC_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook Verification Token
    |--------------------------------------------------------------------------
    |
    | This is the token that your webhook needs to validate to ensure the
    | callbacks are legitimately from Xendit and not from third party.
    |
    */
    'xendit_webhook_verification_token' => env('XENDIT_WEBHOOK_VERIFICATION_TOKEN', '')
];
