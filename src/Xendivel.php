<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Validations\CardValidationService;
use Illuminate\Support\Str;

class Xendivel extends XenditApi
{
    /**
     * Request payload when executing API call.
     */
    public static $payload;

    /**
     * Refund response from the API call.
     */
    public $refundResponse;

    /**
     * Make a payment request using the tokenized value of the card.
     *
     * @param  mixed  $payload  The tokenized data of the card and amount.
     */
    public static function payWithCard($payload): self
    {
        $payload = $payload->toArray();

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
        $response = XenditApi::api('post', '/credit_card_charges', $api_payload);

        // Thrown an exception on failure.
        if ($response->failed()) {
            throw new Exception($response);
        }

        // Return the instance of the CardPayment class to enable method chaining.
        return new self();

    }

    /**
     * Send an invoice to the customer's e-mail address.
     */
    public function sendInvoiceTo(string $email): self
    {
        logger('Sending email....');
        return $this;
    }

    /**
     * Request for a refund.
     */
    public function refund(): self
    {
        return $this;
    }

    /**
     * Send refund confirmation e-mail to customer.
     */
    public function sendRefundConfirmationEmail(string $email): self
    {
        return $this;
    }
}
