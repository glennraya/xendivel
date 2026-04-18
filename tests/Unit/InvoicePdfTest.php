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

    config([
        'xendivel.invoice_storage_path' => $this->invoiceStoragePath,
        'xendivel.browsershot.timeout' => 30,
    ]);

    Invoice::make(xendivelInvoiceData())
        ->fileName(null)
        ->paperSize('A4')
        ->orientation('portrait')
        ->template('invoice');
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
            'page_size_css' => '297mm 210mm',
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
        ->toContain('size: 297mm 210mm;')
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
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'A4', 'landscape'),
        xendivelPdfPoints(297, 'mm'),
        xendivelPdfPoints(210, 'mm')
    );
});

it('generates a portrait pdf when portrait orientation is requested', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'A4', 'portrait'),
        xendivelPdfPoints(210, 'mm'),
        xendivelPdfPoints(297, 'mm')
    );
});

it('forces landscape orientation even when a published template is portrait', function () {
    xendivelSkipIfBrowsershotRuntimeUnavailable($this);

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

it('injects api page size over a published template page size', function () {
    xendivelSkipIfBrowsershotRuntimeUnavailable($this);

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
            size: Letter portrait;
            margin: 0;
        }
    </style>
</head>
<body>Page size override fixture</body>
</html>
BLADE);

    try {
        $path = Invoice::make(xendivelInvoiceData())
            ->fileName('page-size-override-testing')
            ->paperSize('A4')
            ->orientation('landscape')
            ->save();

        xendivelExpectPdfMediaBoxSize(
            xendivelFirstPdfMediaBox((string) file_get_contents($path)),
            xendivelPdfPoints(297, 'mm'),
            xendivelPdfPoints(210, 'mm')
        );
    } finally {
        if (file_exists($view_path)) {
            unlink($view_path);
        }

        if ($created_directory && is_dir($view_directory)) {
            rmdir($view_directory);
        }
    }
});

it('normalizes named browsershot page sizes to pdf dimensions', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'A4', 'landscape'),
        xendivelPdfPoints(297, 'mm'),
        xendivelPdfPoints(210, 'mm')
    );
});

it('normalizes legal to portrait dimensions when portrait is requested', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'Legal', 'portrait'),
        xendivelPdfPoints(8.5, 'in'),
        xendivelPdfPoints(14, 'in')
    );
});

it('normalizes letter to landscape dimensions when landscape is requested', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'Letter', 'landscape'),
        xendivelPdfPoints(11, 'in'),
        xendivelPdfPoints(8.5, 'in')
    );
});

it('ignores orientation embedded in paper size when invoice orientation is explicit', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'A4 landscape', 'portrait'),
        xendivelPdfPoints(210, 'mm'),
        xendivelPdfPoints(297, 'mm')
    );
});

it('falls back to a4 page size for invalid paper size input', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'A4; background: red', 'portrait'),
        xendivelPdfPoints(210, 'mm'),
        xendivelPdfPoints(297, 'mm')
    );
});

it('falls back to a4 page size when unsupported named sizes are requested', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, 'A5', 'portrait'),
        xendivelPdfPoints(210, 'mm'),
        xendivelPdfPoints(297, 'mm')
    );
});

it('defaults to a4 page size when paper size is empty', function () {
    xendivelExpectPdfMediaBoxSize(
        xendivelGeneratedInvoiceMediaBox($this, '', 'portrait'),
        xendivelPdfPoints(210, 'mm'),
        xendivelPdfPoints(297, 'mm')
    );
});

it('saves a generated invoice pdf to storage', function () {
    xendivelSkipIfBrowsershotRuntimeUnavailable($this);

    $path = Invoice::make(xendivelInvoiceData())
        ->fileName('saved-testing')
        ->paperSize('A4')
        ->save();

    expect($path)->toBe($this->invoiceStoragePath.'saved-testing-invoice.pdf')
        ->and(file_exists($path))->toBeTrue()
        ->and(substr((string) file_get_contents($path), 0, 4))->toBe('%PDF');
});

it('streams generated invoice pdf downloads', function () {
    xendivelSkipIfBrowsershotRuntimeUnavailable($this);

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
    preg_match('/\/MediaBox\s*\[\s*([-0-9.]+)\s+([-0-9.]+)\s+([-0-9.]+)\s+([-0-9.]+)\s*\]/', $pdf, $matches);

    if ($matches === []) {
        throw new RuntimeException('Unable to read the first PDF MediaBox.');
    }

    return [
        (float) $matches[3] - (float) $matches[1],
        (float) $matches[4] - (float) $matches[2],
    ];
}

function xendivelGeneratedInvoiceMediaBox(PHPUnit\Framework\TestCase $test, ?string $paper_size, string $orientation): array
{
    xendivelSkipIfBrowsershotRuntimeUnavailable($test);

    $path = Invoice::make(xendivelInvoiceData())
        ->fileName('page-size-'.uniqid())
        ->paperSize($paper_size)
        ->orientation($orientation)
        ->template('invoice')
        ->save();

    return xendivelFirstPdfMediaBox((string) file_get_contents($path));
}

function xendivelExpectPdfMediaBoxSize(array $media_box, float $expected_width, float $expected_height, float $delta = 1.0): void
{
    expect($media_box[0])->toBeGreaterThan($expected_width - $delta)
        ->and($media_box[0])->toBeLessThan($expected_width + $delta)
        ->and($media_box[1])->toBeGreaterThan($expected_height - $delta)
        ->and($media_box[1])->toBeLessThan($expected_height + $delta);
}

function xendivelPdfPoints(float $value, string $unit): float
{
    return match ($unit) {
        'in' => $value * 72,
        'mm' => $value * 72 / 25.4,
    };
}
