<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Validations\CardValidationService;
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
        // Validate the payload.
        CardValidationService::validate($payload);

        $api_payload = [
            'amount' => $payload['amount'],
            'external_id' => config('xendivel.auto_external_id') === true
                ? Str::uuid()
                : $payload['external_id'],
            'token_id' => $payload['token_id'],
        ];

        // Merge these values below to the $api_payload if entered by the user.
        // List of optional fields
        $optionalFields = ['descriptor', 'currency', 'billing_details', 'metadata'];

        // Merge optional values to the $api_payload if they are set and not empty.
        foreach ($optionalFields as $field) {
            if (isset($payload[$field]) && $payload[$field] !== '') {
                $api_payload[$field] = $payload[$field];
            }
        }

        // Attempt to charge the card.
        $api_request = Xendivel::api('post', '/credit_card_charges', $api_payload);

        // Thrown an exception on failure.
        if($api_request->failed()) {
            throw new Exception($api_request);
        }

        // Return the instance of the CardPayment class.
        return new self();
        // return self::fetchResponse($api_response);

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
     * Set the chargeCardResponse
     *
     * @param  Illuminate\Http\Client\Response  $api_response
     */
    // private static function fetchResponse($api_response): CardPayment
    // {
    //     $instance = new self();
    //     $instance->chargeCardResponse = $api_response;

    //     return $instance;
    // }

    /**
     * Return the response from the API call.
     */
    // public function getResponse(): \stdClass
    // {
    //     return json_decode($this->chargeCardResponse);
    // }
}
