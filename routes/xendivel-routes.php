<?php

use GlennRaya\Xendivel\CardPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Invoice template - The values are hard-coded for demonstration.
// You should supply your own data with this format.
Route::get('/xendivel-invoice-template', function () {
    return view('vendor.xendivel.views.invoice', [
        'invoice_data' => [
            'invoice_number' => 1000023,
            'card_type' => 'VISA',
            'masked_card_number' => '400000XXXXXX0002',
            'company_name' => 'The XYZ Tech LTD',
            'company_address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
            'company_phone' => '+63 971-444-1234',
            'company_email' => 'xendivel@example.com',
            'items' => [
                ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
                ['item' => 'MacBook Pro 16" M3 Max', 'price' => 2499, 'quantity' => 3],
                ['item' => 'Pro Display XDR', 'price' => 5999, 'quantity' => 2],
            ],
            'footer_note' => 'Thank you for your recent purchase with us! We\'re thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
        ]
    ]);
});

// Example card payment test route.
Route::get('/xendivel-card', function () {
    return view('vendor.xendivel.views.cards');
});

// Route::get('/download', function () {
//     return CardPayment::downloadInvoice('516b21e4-8ffc-4c4e-9a96-175891d063d9-invoice.pdf');
// });

// Example Card Charge Request:
// Perform API request to charge the credit/debit cards (visa, mastercard, amex, etc.)
Route::post('/charge-card-example', function (Request $request) {
    $payment = CardPayment::makePayment($request)
        ->getResponse();

    return $payment;

    // $request_data['billing_details'] = [
    //     'given_names' => 'John',
    //     'surname' => 'Smith',
    //     'email' => 'john@example.com',
    //     'mobile_number' => '+639761234567',
    //     'phone_number' => '+630476331234',
    //     'address' => [
    //         'street_line1' => '#1723 Lilac St.',
    //         'street_line2' => 'BlueRidge Avenue',
    //         'city' => 'Cedarville City',
    //         'province_state' => 'Arcadia',
    //         'postal_code' => '54321',
    //         'country' => 'Philippines',
    //     ],
    // ];

    // return CardPayment::makePayment([
    //     'amount' => 2500,
    //     'token_id' => '6562034f67834e00171338e7'
    // ])
    // ->sendInvoiceTo('glenn@example.com')
    // ->getResponse();

});
