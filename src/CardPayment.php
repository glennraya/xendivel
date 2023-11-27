<?php

namespace GlennRaya\Xendivel;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class CardPayment extends Xendivel
{
    /**
     * Request payload when executing API call.
     */
    public $payload;

    /**
     * Charge card response from the API call.
     */
    public $chargeCardResponse;

    /**
     * Refund response from the API call.
     */
    public $refundResponse;

    /**
     * Make a payment request using the tokenized value of the
     * card. Then, return the CardPayment class to create
     * new instance class for method chaining.
     */
    public static function makePayment(array $payload): CardPayment
    {
        $api_payload = [
            'amount' => $payload['amount'],
            'external_id' => config('xendivel.auto_external_id') === true
                ? Str::uuid()
                : $payload['external_id'],
            'token_id' => $payload['token_id'],
        ];

        // Merge these values below to the $api_payload if entered by the user.

        // Optional: Specific descriptor to define merchant's identity.
        if (isset($payload['descriptor']) && $payload['descriptor'] !== '') {
            $api_payload['descriptor'] = $payload['descriptor'];
        }

        // Optional: If the currency is not provided, it defaults to
        // the currency based on the currency your business uses.
        if (isset($payload['currency']) && $payload['currency'] !== '') {
            $api_payload['currency'] = $payload['currency'];
        }

        // Optional: Billing details of the cardholder.
        // Required: If a card is to be verified by the Address
        // Verification System (AVS) - only for USA / Canadian / Great Britain cards.
        if (isset($payload['billing_details']) && $payload['billing_details'] !== '') {
            $api_payload['billing_details'] = $payload['billing_details'];
        }

        if (isset($payload['metadata']) && $payload['metadata'] !== '') {
            $api_payload['metadata'] = $payload['metadata'];
        }

        // Attempt to charge the card.
        $api_response = Xendivel::api('post', '/credit_card_charges', $api_payload);

        // Return the instance of the CardPayment class
        // with the response from the API call.
        return self::fetchResponse($api_response);

    }

    /**
     * Set the chargeCardResponse
     *
     * @param  Illuminate\Http\Client\Response  $api_response
     */
    private static function fetchResponse($api_response): CardPayment
    {
        $instance = new self();
        $instance->chargeCardResponse = $api_response;

        return $instance;
    }

    /**
     * Send an invoice to the customer's e-mail address.
     */
    public function sendInvoiceTo(string $email): CardPayment
    {
        return $this;
    }

    /**
     * Request for a refund.
     */
    public function refund(): CardPayment
    {
        return $this;
    }

    /**
     * Send refund confirmation e-mail to customer.
     */
    public function sendRefundConfirmationEmail(string $email): CardPayment
    {
        return $this;
    }

    /**
     * Return the response from the API call.
     */
    public function getResponse(): \stdClass
    {
        return json_decode($this->chargeCardResponse);
    }
}
