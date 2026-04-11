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
        ->toContain('@page {')
        ->toContain('@page xendivel-invoice')
        ->toContain('size: A4 landscape;')
        ->toContain('page: xendivel-invoice;')
        ->toContain('margin: 8mm;')
        ->toContain('background: #f1f3f7;')
        ->toContain('width: 704px;')
        ->toContain('max-width: 100%;')
        ->toContain('width: 980px;')
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

it('generates a landscape pdf when landscape orientation is requested', function () {
    $html = view('xendivel::invoice', [
        'invoice_data' => xendivelInvoiceData(),
        'paper_size' => 'A4',
        'orientation' => 'landscape',
    ])->render();

    [$width, $height] = xendivelFirstPdfMediaBox(\Typesetsh\createPdf($html)->asString());

    expect($width)->toBeGreaterThan($height);
});

it('applies landscape orientation through the invoice api', function () {
    $path = Invoice::make(xendivelInvoiceData())
        ->fileName('landscape-api-testing')
        ->paperSize('A4')
        ->orientation('landscape')
        ->save();

    [$width, $height] = xendivelFirstPdfMediaBox((string) file_get_contents($path));

    expect($width)->toBeGreaterThan($height);
});

it('forces landscape orientation even when a published template is portrait', function () {
    $view_directory = resource_path('views/vendor/xendivel');
    $view_path = $view_directory.'/invoice.blade.php';
    $created_directory = ! is_dir($view_directory);

    if ($created_directory) {
        mkdir($view_directory, 0755, true);
    }

    file_put_contents($view_path, <<<'BLADE'
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
    </style>
</head>
<body>Forced orientation fixture</body>
</html>
BLADE);

    try {
        $path = Invoice::make(xendivelInvoiceData())
            ->fileName('forced-landscape-testing')
            ->paperSize('A4')
            ->orientation('landscape')
            ->save();

        [$width, $height] = xendivelFirstPdfMediaBox((string) file_get_contents($path));

        expect($width)->toBeGreaterThan($height);
    } finally {
        if (file_exists($view_path)) {
            unlink($view_path);
        }

        if ($created_directory && is_dir($view_directory)) {
            rmdir($view_directory);
        }
    }
});

it('generates a portrait pdf when portrait orientation is requested', function () {
    $html = view('xendivel::invoice', [
        'invoice_data' => xendivelInvoiceData(),
        'paper_size' => 'A4',
        'orientation' => 'portrait',
    ])->render();

    [$width, $height] = xendivelFirstPdfMediaBox(\Typesetsh\createPdf($html)->asString());

    expect($height)->toBeGreaterThan($width);
});

it('loads image assets from configured allowed directories', function () {
    $assets_directory = sys_get_temp_dir().'/xendivel-test-assets-'.uniqid();
    mkdir($assets_directory, 0755, true);

    $image_path = $assets_directory.'/dot.gif';
    file_put_contents($image_path, xendivelTinyGifBinary());

    config([
        'xendivel.typesetsh.allowed_directories' => [$assets_directory],
        'xendivel.typesetsh.allowed_protocols' => [],
    ]);

    $view_directory = resource_path('views/vendor/xendivel');
    $view_path = $view_directory.'/invoice.blade.php';
    $created_directory = ! is_dir($view_directory);

    if ($created_directory) {
        mkdir($view_directory, 0755, true);
    }

    file_put_contents($view_path, xendivelInvoiceImageFixtureTemplate());

    try {
        $path = Invoice::make(array_merge(xendivelInvoiceData(), [
            'image_src' => $image_path,
        ]))
            ->fileName('allowed-directory-testing')
            ->paperSize('A4')
            ->save();

        $pdf = (string) file_get_contents($path);

        expect($pdf)
            ->toStartWith('%PDF')
            ->toContain('/Subtype/Image');
    } finally {
        if (file_exists($view_path)) {
            unlink($view_path);
        }

        if ($created_directory && is_dir($view_directory)) {
            rmdir($view_directory);
        }

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        if (is_dir($assets_directory)) {
            rmdir($assets_directory);
        }
    }
});

it('ignores disallowed file resources safely during pdf rendering', function () {
    $blocked_directory = sys_get_temp_dir().'/xendivel-test-blocked-assets-'.uniqid();
    $allowed_directory = sys_get_temp_dir().'/xendivel-test-allowed-assets-'.uniqid();
    mkdir($blocked_directory, 0755, true);
    mkdir($allowed_directory, 0755, true);

    $image_path = $blocked_directory.'/dot.gif';
    file_put_contents($image_path, xendivelTinyGifBinary());

    config([
        'xendivel.typesetsh.allowed_directories' => [$allowed_directory],
        'xendivel.typesetsh.allowed_protocols' => [],
    ]);

    $view_directory = resource_path('views/vendor/xendivel');
    $view_path = $view_directory.'/invoice.blade.php';
    $created_directory = ! is_dir($view_directory);

    if ($created_directory) {
        mkdir($view_directory, 0755, true);
    }

    file_put_contents($view_path, xendivelInvoiceImageFixtureTemplate());

    try {
        $path = Invoice::make(array_merge(xendivelInvoiceData(), [
            'image_src' => $image_path,
        ]))
            ->fileName('disallowed-directory-testing')
            ->paperSize('A4')
            ->save();

        $pdf = (string) file_get_contents($path);

        expect($pdf)
            ->toStartWith('%PDF')
            ->not->toContain('/Subtype/Image');
    } finally {
        if (file_exists($view_path)) {
            unlink($view_path);
        }

        if ($created_directory && is_dir($view_directory)) {
            rmdir($view_directory);
        }

        if (file_exists($image_path)) {
            unlink($image_path);
        }

        if (is_dir($blocked_directory)) {
            rmdir($blocked_directory);
        }

        if (is_dir($allowed_directory)) {
            rmdir($allowed_directory);
        }
    }
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

function xendivelFirstPdfMediaBox(string $pdf): array
{
    preg_match('/\/MediaBox\s*\[\s*([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)\s*\]/', $pdf, $matches);

    if ($matches === []) {
        throw new RuntimeException('Unable to read the first PDF MediaBox.');
    }

    return [
        (float) $matches[3] - (float) $matches[1],
        (float) $matches[4] - (float) $matches[2],
    ];
}

function xendivelTinyGifBinary(): string
{
    return (string) base64_decode('R0lGODdhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
}

function xendivelInvoiceImageFixtureTemplate(): string
{
    return <<<'BLADE'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Image Fixture</title>
</head>
<body>
    <p>Invoice image fixture</p>
    <img src="{{ $invoice_data['image_src'] ?? '' }}" width="12" height="12" alt="fixture-image">
</body>
</html>
BLADE;
}
