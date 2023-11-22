<?php

namespace GlennRaya\Xendivel;

use GlennRaya\Xendivel\Xendivel;

class CardsPayment extends Xendivel
{
    /**
     * Create a payment via cards (debit or credit card)
     *
     * @return void
     */
    public static function createPayment(array $params)
    {
        // Logic in creating a payment goes here.
        return response()->json([
            'params' => $params,
            'auth_token' => Xendivel::api($params),
        ]);
        // return Xendivel::authenticate();
    }
}
