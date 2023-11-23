<?php

namespace GlennRaya\Xendivel;

use Exception;

class Xendivel
{
    /**
     * Generate the BASIC AUTH key needed for every API request on Xendit.
     * This is made with the combination of the app's secret_key and
     * the semicolon ":" character, then encoding it in base64.
     *
     * @see https://developers.xendit.co/api-reference/#authentication
     */
    private static function generateAuthToken(): string
    {
        return base64_encode(config('xendivel.xendit_secret_key') . ":");
    }

    /**
     * Automatically authenticate to Xendit's API endpoints.
     */
    public static function authenticate()
    {
        return self::generateAuthToken();
    }

    /**
     * Perform Xendit API call.
     *
     * @param  array $params All required data to perform this call.
     * @return void
     * @throws Exception
     */
    public static function api(array $params)
    {
        if (empty(config('xendivel.xendit_secret_key'))) {
            throw new Exception('Your Xendit secret key (XENDIT_SECRET_KEY) is not set from your .env file');
        }

        // TODO: Perform the logic for calling Xendit's API here...
        return self::authenticate();
    }
}
