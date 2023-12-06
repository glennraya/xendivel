<?php

namespace GlennRaya\Xendivel;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class XenditApi
{
    public static $chargeResponse;

    private const API_VERSION = '2019-05-01';

    /**
     * Generate the BASIC AUTH key needed for every API request on Xendit.
     * This is made with the combination of your account secret_key and
     * the semicolon ":" character, then encoding it in base64.
     *
     * @see https://developers.xendit.co/api-reference/#authentication
     */
    private static function generateAuthToken(): string
    {
        return base64_encode(config('xendivel.secret_key').':');
    }

    /**
     * Perform Xendit API call.
     *
     * @param  string  $method [required]  The type of HTTP request (post, get, etc).
     * @param  string  $uri [required]  The URI for the request.
     * @param  array  $payload [required]  The request payload for the API.
     *
     * @throws Exception
     */
    public static function api(string $method, string $uri, array $payload): Response
    {
        // Check if the secret key is set in .env file.
        if (empty(config('xendivel.secret_key'))) {
            throw new Exception('Your Xendit secret key (XENDIT_SECRET_KEY) is not set from your .env file.');
        }

        // Perform Xendit API call with proper authentication token setup.
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.self::generateAuthToken(),
            'x-api-version' => self::API_VERSION,
            'X-IDEMPOTENCY-KEY' => isset($payload['idempotency']) ? $payload['idempotency'] : '',
        ])
            ->$method("https://api.xendit.co/{$uri}", $payload);

        // Throw an exception when the request failed.
        if ($response->failed()) {
            throw new Exception($response);
        }

        self::$chargeResponse = $response;

        return $response;
    }

    /**
     * Return the response from the API call.
     */
    public function getResponse()
    {
        return json_decode(self::$chargeResponse);
    }
}
