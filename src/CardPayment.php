<?php

namespace GlennRaya\Xendivel;

use Illuminate\Support\Str;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Client\Response;

class CardPayment extends Xendivel
{
    /**
     * API call payload.
     *
     * @var array
     */
    public $payload;

    /**
     * It contains the response from Xendit API call.
     */
    public $chargeCardResponse;

    /**
     * Static factory method to create a new instance.
     *
     * @return CardPayment
     */
    public static function make()
    {
        return new static();
    }

    /**
     * Create a payment via cards (debit or credit card).
     *
     * @param  array $payload
     * @return $this CardsPayment instance
     */
    public function payment(array $payload)
    {
        $this->payload = $payload;

        $this->chargeCardResponse = Xendivel::api('post', '/credit_card_charges', [
            'amount' => $payload['amount'],
            'external_id' => config('xendivel.auto_create_xendit_external_id') === true
                ? Str::uuid()
                : $payload['external_id'],
            'token_id' => $payload['card-token']
        ]);

        return $this;
    }

    /**
     * Send an invoice to the customer via e-mail.
     *
     * @param  string $email
     */
    public function sendInvoiceTo(string $email): CardPayment
    {
        return $this;
    }

    /**
     * Return the value of Xendit's API response.
     */
    public function getResponse(): Response
    {
        return $this->chargeCardResponse;
    }
}
