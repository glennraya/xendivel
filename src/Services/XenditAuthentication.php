<?php

namespace GlennRaya\Xendivel\Services;

/*
|--------------------------------------------------------------------------
| Xendit Authentication
|--------------------------------------------------------------------------
|
| This will authenticate your application with Xendit's APIs by combining
| your secret key, and a ":" character, then encoding it into Bas64
| format to generate the authentication token for authorization.
|
| Reference URL: https://developers.xendit.co/api-reference/#authentication
|
*/

class XenditAuthentication
{
    /**
     * Generate the BASIC AUTH key needed for every API request on Xendit.
     * This is made with the combination of the app's secret_key and
     * the semicolon ":" character, then encoding it in base64.
     *
     * @see https://developers.xendit.co/api-reference/#authentication
     */
    public static function getAuthToken(): string
    {
        return base64_encode(config('xendivel.xendit_secret_key') . ":");
    }
}
