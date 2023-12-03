<?php

use GlennRaya\Xendivel\Invoice;
use GlennRaya\Xendivel\Xendivel;
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
            'merchant_name' => 'The Xendivel Tech LTD',
            'merchant_address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
            'merchant_phone' => '+63 971-444-1234',
            'merchant_email' => 'xendivel@example.com',
            'customer' => [
                'name' => 'Victoria Marini',
                'address' => 'Georgetown, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
                'email' => 'victoria@example.com',
                'phone' => '+63 909-098-654',
            ],
            'items' => [
                ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
                ['item' => 'MacBook Pro 16" M3 Max', 'price' => 2499, 'quantity' => 3],
                ['item' => 'Apple Pro Display XDR', 'price' => 5999, 'quantity' => 2],
                ['item' => 'Pro Stand', 'price' => 999, 'quantity' => 2],
            ],
            'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
        ],
    ]);
});

// Example card payment test route.
Route::get('/xendivel-card', function () {
    return view('vendor.xendivel.views.cards');
});

// Will generate an invoice and store it in storage. But will not download it right away.
Route::get('/generate', function () {
    return Invoice::make([
        'invoice_number' => 1000023,
        'card_type' => 'VISA',
        'masked_card_number' => '400000XXXXXX0002',
        'merchant_name' => 'Stark Industries',
        'merchant_address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
        'merchant_phone' => '+63 971-444-1234',
        'merchant_email' => 'xendivel@example.com',
        'customer' => [
            'name' => 'Victoria Marini',
            'address' => 'Alex Johnson, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
            ['item' => 'MacBook Pro 16" M3 Max', 'price' => 2499, 'quantity' => 3],
            ['item' => 'Apple Pro Display XDR', 'price' => 5999, 'quantity' => 2],
            ['item' => 'Pro Stand', 'price' => 999, 'quantity' => 2],
        ],
        'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
    ])
        ->paperSize('A4')
        ->save();
});

// Example Card Charge Request:
// Perform API request to charge the credit/debit cards (visa, mastercard, amex, etc.)
Route::post('/checkout-example', function (Request $request) {

    $payment = Xendivel::payWithCard($request)
        ->getResponse();

    return $payment;
});

// Example card charge, then send invoice to email as an attachment.
Route::post('/checkout-email-invoice', function (Request $request) {
    $invoice_data = [
        'invoice_number' => 1000023,
        'card_type' => 'VISA',
        'masked_card_number' => '400000XXXXXX0002',
        'merchant_name' => 'Stark Industries',
        'merchant_address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
        'merchant_phone' => '+63 971-444-1234',
        'merchant_email' => 'stark@example.com',
        'customer' => [
            'name' => 'Mr. Glenn Raya',
            'address' => 'Alex Johnson, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'MacBook Pro 16" M3 Max', 'price' => 3999, 'quantity' => 1],
        ],
        'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
    ];

    $payment = Xendivel::payWithCard($request)
        ->emailInvoiceTo('glenn@example.com', $invoice_data)
        ->send()
        ->getResponse();

    return $payment;
});

// Example invoice download.
Route::get('/download', function () {
    $invoice_data = [
        'invoice_number' => 1000023,
        'card_type' => 'VISA',
        'masked_card_number' => '400000XXXXXX0002',
        'company_name' => 'The XYZ Tech LTD',
        'company_address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
        'company_phone' => '+63 971-444-1234',
        'company_email' => 'xendivel@example.com',
        'customer' => [
            'name' => 'Glenn Raya',
            'address' => 'Alex Johnson, 4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
            ['item' => 'MacBook Pro 16" M3 Max', 'price' => 2499, 'quantity' => 3],
            ['item' => 'Apple Pro Display XDR', 'price' => 5999, 'quantity' => 2],
            ['item' => 'Pro Stand', 'price' => 999, 'quantity' => 2],
        ],
        'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
    ];

    return Invoice::download($invoice_data, orientation: 'portrait');
});
