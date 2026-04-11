<?php

use GlennRaya\Xendivel\Invoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->invoiceStoragePath = sys_get_temp_dir().'/xendivel-test-invoices/';

    if (! is_dir($this->invoiceStoragePath)) {
        mkdir($this->invoiceStoragePath, 0755, true);
    }

    foreach (glob($this->invoiceStoragePath.'*.pdf') ?: [] as $file) {
        unlink($file);
    }

    config(['xendivel.invoice_storage_path' => $this->invoiceStoragePath]);
});

afterEach(function () {
    foreach (glob($this->invoiceStoragePath.'*.pdf') ?: [] as $file) {
        unlink($file);
    }
});

it('renders the invoice template with plain pdf css', function () {
    $html = Carbon::withTestNow(
        Carbon::parse('2026-04-11 12:43:00', 'Asia/Manila'),
        fn () => view('xendivel::invoice', [
            'invoice_data' => xendivelInvoiceData(),
            'paper_size' => 'A4',
            'orientation' => 'landscape',
        ])->render()
    );

    expect($html)
        ->toContain('Xendivel Invoice Template')
        ->toContain('invoice-page')
        ->toContain('invoice-header')
        ->toContain('invoice-header-strip')
        ->toContain('invoice-container')
        ->toContain('Date: Apr. 11, 2026 at 12:43pm')
        ->toContain('size: A4 landscape;')
        ->toContain('margin: 8mm;')
        ->toContain('background: #f1f3f7;')
        ->toContain('width: 704px;')
        ->toContain('invoice-item-row-last')
        ->toContain('invoice-summary-section')
        ->toContain('invoice-summary-layout')
        ->toContain('invoice-summary-lines')
        ->toContain('invoice-total-table')
        ->toContain('invoice-total-row')
        ->toContain('invoice-payment-table')
        ->toContain('invoice-payment-row')
        ->toContain('invoice-visa-mark')
        ->toContain('viewBox="0 0 64 20"')
        ->not->toContain('tailwindcss')
        ->not->toContain('--tw-')
        ->not->toContain('margin-left: auto')
        ->not->toContain('<tfoot')
        ->not->toContain('<img')
        ->not->toContain('src=')
        ->not->toContain('data-name="Visa credit card"');
});

it('saves a generated invoice pdf to storage', function () {
    $path = Invoice::make(xendivelInvoiceData())
        ->fileName('saved-testing')
        ->paperSize('A4')
        ->save();

    expect($path)->toBe($this->invoiceStoragePath.'saved-testing-invoice.pdf')
        ->and(file_exists($path))->toBeTrue()
        ->and(substr((string) file_get_contents($path), 0, 4))->toBe('%PDF');
});

it('streams generated invoice pdf downloads', function () {
    Route::get('/test-invoice-download', function () {
        return Invoice::make(xendivelInvoiceData())
            ->fileName('download-testing')
            ->download();
    });

    $response = $this->get('/test-invoice-download');

    $response
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect($response->headers->get('content-disposition'))
        ->toContain('attachment; filename=download-testing-invoice.pdf');
});

function xendivelInvoiceData(): array
{
    return [
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
            'address' => '4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 5],
            ['item' => 'MacBook Pro 16 inch M3 Max', 'price' => 2499, 'quantity' => 3],
        ],
        'tax_rate' => .12,
        'tax_id' => '123-456-789',
        'footer_note' => 'Thank you for your recent purchase with us.',
    ];
}
