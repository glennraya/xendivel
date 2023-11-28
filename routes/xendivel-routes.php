<?php

use GlennRaya\Xendivel\CardPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/card-payment-example', function () {
    return view('vendor.xendivel.cards');
});

// Perform API request to charge the credit/debit cards (visa, mastercard, amex, etc.)
Route::post('/charge-card-example', function (Request $request) {
    $request_data = $request->toArray();

    $payment = CardPayment::makePayment($request_data)
        ->sendInvoiceTo('glenn@example.com')
        ->getResponse();

    return $payment;

    // $request_data['billing_details'] = [
    //     'given_names' => 'Glenn',
    //     'surname' => 'Raya',
    //     'email' => 'glenn@xendit.co',
    //     'mobile_number' => '+639761234567',
    //     'phone_number' => '+630476331234',
    //     'address' => [
    //         'street_line1' => 'Blk. 4, Lot 27',
    //         'street_line2' => 'Judges St. Our Lady of Lourdes Subd.',
    //         'city' => 'Balanga City',
    //         'province_state' => 'Bataan',
    //         'postal_code' => '2100  ',
    //         'country' => 'PH',
    //     ],
    // ];

    // return CardPayment::makePayment([
    //     'amount' => 2500,
    //     'token_id' => '6562034f67834e00171338e7'
    // ])
    // ->sendInvoiceTo('glenn@example.com')
    // ->getResponse();

});
