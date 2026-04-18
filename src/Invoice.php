<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Concerns\InvoicePathResolver;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class Invoice
{
    use InvoicePathResolver;

    private const DEFAULT_PAPER_SIZE = 'A4';

    private const SUPPORTED_PAPER_SIZES = [
        'letter' => [
            'paper_size' => 'Letter',
            'width' => '8.5in',
            'height' => '11in',
        ],
        'a4' => [
            'paper_size' => 'A4',
            'width' => '210mm',
            'height' => '297mm',
        ],
        'legal' => [
            'paper_size' => 'Legal',
            'width' => '8.5in',
            'height' => '14in',
        ],
    ];

    private const PAGE_SIZE_UNITS_TO_POINTS = [
        'in' => 72.0,
        'mm' => 72.0 / 25.4,
    ];

    private static $filename = null;

    private static $template = 'invoice';

    private static $paper_size = 'A4';

    private static $orientation = 'portrait';

    private static $invoice_data = [];

    /**
     * Generate the invoice and save it to storage.
     *
     * @param  array  $invoice_data  [required]  The associative array of information to be displayed on the invoice.
     * @param  string  $filename  [optional]  The filename of the invoice. Will defaults to UUID v4 filename.
     * @param  string  $template  [optional]  The invoice blade template file.
     */
    public static function make(array $invoice_data)
    {
        self::$invoice_data = $invoice_data;
        self::resolveTemplate();

        return new self;
    }

    /**
     * Download the invoice.
     *
     * @throws Exception
     */
    public function download(): StreamedResponse
    {
        $filename = self::resolveFilename();
        $pdf = self::renderPdf();

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    /**
     * Specify a different template for the invoice.
     *
     * @param  string|null  $template  [optional]  The filename for the invoice template.
     */
    public function template(?string $template = null): self
    {
        self::$template = $template;

        self::resolveTemplate();

        return $this;
    }

    /**
     * Specify a custom filename for the invoice
     *
     * @param  string|null  $filename  The custom filename for the invoice.
     */
    public function fileName(?string $filename = null): self
    {
        self::$filename = $filename;

        return $this;
    }

    /**
     * Set the orientation of the invoice (portrait, landscape).
     *
     * @param  string  $orientation  The orientation of the invoice (portrait or landscape).
     */
    public function orientation(?string $orientation = null): self
    {
        self::$orientation = $orientation;

        return $this;
    }

    /**
     * Set the paper size of the invoice.
     *
     * @param  string  $paper_size  By default sets to A4.
     */
    public function paperSize(?string $paper_size = null): self
    {
        self::$paper_size = $paper_size;

        return $this;
    }

    /**
     * Save the invoice to storage.
     *
     * @param  string|null  $filename  [optional]. The filename of the invoice. If not specified defaults to UUID v4.
     */
    public function save(): string
    {
        $filename = self::resolveFilename();
        $invoice_path = self::resolveInvoicePath($filename);

        if (file_put_contents($invoice_path, self::renderPdf()) === false) {
            throw new Exception("Xendivel is unable to save the invoice at $invoice_path.");
        }

        return $invoice_path;
    }

    /**
     * @throws Exception
     */
    private static function renderPdf(): string
    {
        $template = self::resolveTemplate();
        $orientation = self::resolveOrientation();
        $page_size = self::resolvePageSize($orientation);

        try {
            $html = view($template, [
                'invoice_data' => self::$invoice_data,
                'paper_size' => $page_size['paper_size'],
                'page_size_css' => $page_size['page_size_css'],
                'orientation' => $orientation,
            ])->render();
            $html = self::injectPageSizeOverride($html, $page_size['page_size_css']);

            return self::buildBrowsershot($html, $page_size['paper_size'], $orientation)->pdf();
        } catch (Throwable $e) {
            throw new Exception(
                $template === null
                    ? "The invoice template can't be located. Be sure that you published Xendivel's assets by running: php artisan vendor:publish --tag=xendivel."
                    : $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * @throws Exception
     */
    private static function resolveTemplate(): string
    {
        $template = self::$template;

        if (! is_dir(resource_path('views/vendor/xendivel'))) {
            return 'xendivel::invoice';
        }

        if (file_exists(resource_path('views/vendor/xendivel')."/$template.blade.php")) {
            return 'vendor.xendivel.'.$template;
        }

        throw new Exception("The $template.blade.php doesn't exists in 'resources/views/vendor/xendivel'");
    }

    private static function resolveFilename(): string
    {
        return self::$filename === null || trim((string) self::$filename) === ''
            ? Str::uuid().'-invoice.pdf'
            : self::$filename.'-invoice.pdf';
    }

    /**
     * @return array{paper_size: string, page_size_css: string}
     */
    private static function resolvePageSize(string $orientation): array
    {
        $paper_size = self::normalizePageSizeInput(self::$paper_size);

        return self::resolveNamedPageSize($paper_size, $orientation)
            ?? self::resolveNamedPageSize(self::DEFAULT_PAPER_SIZE, $orientation);
    }

    private static function resolveOrientation(): string
    {
        return strtolower(trim((string) self::$orientation)) === 'landscape' ? 'landscape' : 'portrait';
    }

    private static function normalizePageSizeInput(?string $paper_size): string
    {
        $paper_size = trim((string) ($paper_size ?? ''));
        $paper_size = preg_replace('/\s+/', ' ', $paper_size) ?? '';

        return $paper_size === '' ? self::DEFAULT_PAPER_SIZE : $paper_size;
    }

    /**
     * @return array{paper_size: string, page_size_css: string}|null
     */
    private static function resolveNamedPageSize(string $paper_size, string $orientation): ?array
    {
        $parts = explode(' ', $paper_size);
        if (count($parts) > 2) {
            return null;
        }

        $page_size = self::SUPPORTED_PAPER_SIZES[strtolower($parts[0] ?? '')] ?? null;
        if ($page_size === null) {
            return null;
        }

        if (isset($parts[1]) && ! in_array(strtolower($parts[1]), ['portrait', 'landscape'], true)) {
            return null;
        }

        [$width, $height] = self::orientDimensions(
            $page_size['width'],
            $page_size['height'],
            $orientation
        );

        return [
            'paper_size' => $page_size['paper_size'],
            'page_size_css' => $width.' '.$height,
        ];
    }

    /**
     * @return array{string, string}
     */
    private static function orientDimensions(string $width, string $height, string $orientation): array
    {
        $width_points = self::dimensionToPoints($width);
        $height_points = self::dimensionToPoints($height);

        if ($width_points === null || $height_points === null) {
            return [$width, $height];
        }

        $must_swap = $orientation === 'landscape'
            ? $width_points < $height_points
            : $width_points > $height_points;

        return $must_swap ? [$height, $width] : [$width, $height];
    }

    private static function dimensionToPoints(string $dimension): ?float
    {
        if (! preg_match('/\A([0-9]+(?:\.[0-9]+)?)(in|mm)\z/i', $dimension, $matches)) {
            return null;
        }

        $unit = strtolower($matches[2]);
        if (! array_key_exists($unit, self::PAGE_SIZE_UNITS_TO_POINTS)) {
            return null;
        }

        return (float) $matches[1] * self::PAGE_SIZE_UNITS_TO_POINTS[$unit];
    }

    private static function injectPageSizeOverride(string $html, string $page_size_css): string
    {
        $style = <<<HTML

    <style data-xendivel-page-size>
        @page {
            size: {$page_size_css};
        }

        @page xendivel-invoice {
            size: {$page_size_css};
        }
    </style>
HTML;

        if (stripos($html, '</head>') !== false) {
            return preg_replace('/<\/head>/i', $style."\n</head>", $html, 1) ?? $html;
        }

        return $style."\n".$html;
    }

    private static function buildBrowsershot(string $html, string $paper_size, string $orientation): Browsershot
    {
        $browsershot = Browsershot::html($html)
            ->newHeadless()
            ->showBackground()
            ->format($paper_size)
            ->emulateMedia('print')
            ->timeout(self::resolveBrowsershotTimeout());

        if ($orientation === 'landscape') {
            $browsershot->landscape();
        }

        self::applyBrowsershotRuntimeOptions($browsershot);

        return $browsershot;
    }

    private static function applyBrowsershotRuntimeOptions(Browsershot $browsershot): void
    {
        $settings = [
            'node_binary' => 'setNodeBinary',
            'npm_binary' => 'setNpmBinary',
            'chrome_path' => 'setChromePath',
            'node_module_path' => 'setNodeModulePath',
            'include_path' => 'setIncludePath',
            'content_url' => 'setContentUrl',
        ];

        foreach ($settings as $config_key => $method) {
            $value = self::resolveBrowsershotStringConfig($config_key);
            if ($value === null) {
                continue;
            }

            $browsershot->{$method}($value);
        }

        if (self::resolveBrowsershotBooleanConfig('no_sandbox')) {
            $browsershot->noSandbox();
        }
    }

    private static function resolveBrowsershotTimeout(): int
    {
        $timeout = (int) config('xendivel.browsershot.timeout', 60);

        return $timeout > 0 ? $timeout : 60;
    }

    private static function resolveBrowsershotStringConfig(string $key): ?string
    {
        $value = config("xendivel.browsershot.$key");
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private static function resolveBrowsershotBooleanConfig(string $key): bool
    {
        $value = config("xendivel.browsershot.$key", false);

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $value;
    }
}
