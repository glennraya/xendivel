<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External or Reference ID
    |--------------------------------------------------------------------------
    |
    | Xendit requires a unique external_id for transactions. This is generated
    | by default as a ordered UUID v4. For custom external_id or reference_id
    | for ewallet transactions, set to false and supply your own
    | implementation for external_id or reference_id.
    |
    | Max of 64 characters and at least 10 characters in length.
    |
    | Reference: https://developers.xendit.co/api-reference/#create-charge
    |
    */

    'auto_id' => true,

    /*
    |--------------------------------------------------------------------------
    | Invoice Default Storage Path
    |--------------------------------------------------------------------------
    |
    | The default storage location where the invoices will be saved. You can
    | customize the location where you want the generated invoices to be
    | saved. However, Xendivel provides a sensible default for you.
    |
    */

    'invoice_storage_path' => storage_path('/app/invoices/'),

    /*
    |--------------------------------------------------------------------------
    | Browsershot PDF Rendering
    |--------------------------------------------------------------------------
    |
    | These options control the Browsershot runtime used to generate invoice
    | PDFs from HTML templates. Leave binary path options null to let
    | Browsershot resolve Node, npm, Puppeteer, and Chrome normally.
    |
    */

    'browsershot' => [
        'timeout' => 60,
        'node_binary' => env('XENDIVEL_BROWSERSHOT_NODE_BINARY'),
        'npm_binary' => env('XENDIVEL_BROWSERSHOT_NPM_BINARY'),
        'chrome_path' => env('XENDIVEL_BROWSERSHOT_CHROME_PATH'),
        'node_module_path' => env('XENDIVEL_BROWSERSHOT_NODE_MODULE_PATH'),
        'include_path' => env('XENDIVEL_BROWSERSHOT_INCLUDE_PATH'),
        'content_url' => env('XENDIVEL_BROWSERSHOT_CONTENT_URL', env('APP_URL')),
        'no_sandbox' => env('XENDIVEL_BROWSERSHOT_NO_SANDBOX', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Xendivel's Emails
    |--------------------------------------------------------------------------
    |
    | When set to true, Xendivel will automatically push the email jobs to
    | queue, whether sending an invoice or refund confirmation emails.
    |
    */

    'queue_email' => false,

    /*
    |--------------------------------------------------------------------------
    | Payment Return URLs
    |--------------------------------------------------------------------------
    |
    | These URLs are where customers return after completing or cancelling an
    | external payment authorization flow. Leave them null to use Xendivel's
    | built-in package return pages, or set them to your own app routes.
    |
    */

    'redirects' => [
        'success_url' => env('XENDIVEL_SUCCESS_REDIRECT_URL'),
        'failure_url' => env('XENDIVEL_FAILURE_REDIRECT_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Callback URL
    |--------------------------------------------------------------------------
    |
    | The default webhook callback URL required by Xendit. You can customize
    | this URL to a different one. Remember to save and test your webhook
    | url on Xendit's webhook section in your administrator dashboard.
    |
    */

    'webhook_url' => '/xendit/webhook',

    /*
    |--------------------------------------------------------------------------
    | Verify Webhook Signature
    |--------------------------------------------------------------------------
    |
    | Webhook verification is essential for securing data exchanges between
    | systems. Methods like token inclusion, as utilized by Xendit,
    | authenticate the source of incoming events, ensuring
    | trust and preventing security risks.
    |
    */
    'verify_webhook_signature' => true,

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
