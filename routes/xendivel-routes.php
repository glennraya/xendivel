<?php

use Illuminate\Http\Request;
use App\Events\eWalletEvents;
use GlennRaya\Xendivel\Invoice;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Support\Facades\Route;

if(config('app.env') === 'local') {
    // Invoice template - The values are hard-coded for demonstration.
    // You should supply your own data for the invoice.
    Route::get('/xendivel/invoice/template', function () {
        return view('xendivel::invoice', [
            'invoice_data' => [
                'invoice_number' => 1000023,
                'card_type' => 'VISA',
                'masked_card_number' => '400000XXXXXX0002',
                'merchant' => [
                    'name' => 'Xendivel LLC',
                    'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
                    'phone' => '+63 971-444-1234',
                    'email' => 'xendivel@example.com',
                ],
                'customer' => [
                    'name' => 'Victoria Blakely',
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
                'tax_rate' => .12,
                'tax_id' => '123-456-789',
                'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
            ],
        ]);
    });

    Route::get('/xendivel/checkout/blade', function() {
        return view("xendivel::checkout");
    });

    // Will generate an invoice and store it in storage. But will not download it right away.
    Route::get('/xendivel/invoice/generate', function () {
        return Invoice::make([
            'invoice_number' => 1000023,
            'card_type' => 'VISA',
            'masked_card_number' => '400000XXXXXX0002',
            'merchant' => [
                'name' => 'Xendivel LLC',
                'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
                'phone' => '+63 971-444-1234',
                'email' => 'xendivel@example.com',
            ],
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
            'tax_rate' => .12,
            'tax_id' => '123-456-789',
            'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
        ])
            ->paperSize('A4')
            ->save();
    });

    // Download example invoice.
    Route::get('/xendivel/invoice/download', function () {
        $invoice_data = [
            'invoice_number' => 1000023,
            'card_type' => 'VISA',
            'masked_card_number' => '400000XXXXXX0002',
            'merchant' => [
                'name' => 'Xendivel LLC',
                'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
                'phone' => '+63 971-444-1234',
                'email' => 'xendivel@example.com',
            ],
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
            'tax_rate' => .12,
            'tax_id' => '123-456-789',
            'footer_note' => 'Thank you for your recent purchase with us! We are thrilled to have the opportunity to serve you and hope that your new purchase brings you great satisfaction.',
        ];

        return Invoice::download($invoice_data);
    });
}

// Listen to webhook events from Xendit. This will fire up an event listener
// where you can perform whatever task you need with the returned data.
Route::post(config('xendivel.webhook_url'), function (Request $request) {

    event(new eWalletEvents($request->toArray()));

})->middleware('xendit-webhook-verification');
