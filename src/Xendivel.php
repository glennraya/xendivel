<?php

namespace GlennRaya\Xendivel;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

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
     * Perform Xendit API call.
     *
     * @throws Exception
     */
    public static function api(string $method, string $uri, array $payload = []): Response
    {
        // Check if the secret key is set in .env file.
        if (empty(config('xendivel.xendit_secret_key'))) {
            throw new Exception('Your Xendit secret key (XENDIT_SECRET_KEY) is not set from your .env file.');
        }

        // Perform Xendit API call with proper authorization token setup.
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . self::generateAuthToken(),
        ])
        ->$method("https://api.xendit.co/{$uri}", $payload);

        // Throw an exception when the request failed.
        if($response->failed()) {
            throw new Exception($response);
        }

        return $response;
    }
}
