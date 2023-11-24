<?php

namespace GlennRaya\Xendivel;

use Illuminate\Support\Str;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Http\Client\Response;

class CardsPayment extends Xendivel
{
    public $payload;
    public $chargeCardResponse;

    /**
     * Create a payment via cards (debit or credit card)
     *
     * @param  array $payload
     * @return $this
     */
    public function createPayment(array $payload)
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

    public function sendInvoice(string $email)
    {
        return $this->chargeCardResponse;
    }

    /**
     * Return the value of Xendit's API response.
     */
    public function get(): Response
    {
        return $this->chargeCardResponse;
    }
}
