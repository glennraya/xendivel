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
     * Static factory method to create a new instance.
     *
     * @return CardPayment
     */
    public static function makePayment(array $payload)
    {
        $charge_card = Xendivel::api('post', '/credit_card_charges', [
            'amount' => $payload['amount'],
            'external_id' => config('xendivel.auto_external_id') === true
                ? Str::uuid()
                : $payload['external_id'],
            'token_id' => $payload['card-token'],
        ]);

        return self::fetchResponse($charge_card);

    }

    /**
     * Set the chargeCardResponse
     *
     * @param  Illuminate\Http\Client\Response $charge_card
     */
    private static function fetchResponse($charge_card): CardPayment
    {
        $instance = new self();
        $instance->chargeCardResponse = $charge_card;
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
     *
     * @param  string $email
     */
    public function sendRefundConfirmationEmail(string $email): CardPayment
    {
        return $this;
    }

    /**
     * Return the response from the API call.
     */
    public function getResponse(): Response
    {
        return $this->chargeCardResponse;
    }
}
