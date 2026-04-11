<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Concerns\InvoicePathResolver;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Typesetsh\Pdf\Document as PdfDocument;
use Typesetsh\UriResolver;
use Typesetsh\UriResolver\Data as DataUriResolver;
use Typesetsh\UriResolver\File as FileUriResolver;
use Typesetsh\UriResolver\Http as HttpUriResolver;
use function Typesetsh\createPdf;

class Invoice
{
    use InvoicePathResolver;

    private static $filename = null;

    private static $template = 'invoice';

    private static $paper_size = 'Letter';

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
        $paper_size = self::resolvePaperSize();
        $orientation = self::resolveOrientation();

        try {
            $html = view($template, [
                'invoice_data' => self::$invoice_data,
                'paper_size' => $paper_size,
                'orientation' => $orientation,
            ])->render();

            $result = createPdf($html, self::buildUriResolver());

            self::applyPageOrientation($result->pdf, $orientation);

            return $result->asString();
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

    private static function resolvePaperSize(): string
    {
        $paper_size = trim((string) (self::$paper_size ?: 'Letter'));

        return preg_match('/^[a-zA-Z0-9 ._-]+$/', $paper_size) === 1 ? $paper_size : 'Letter';
    }

    private static function resolveOrientation(): string
    {
        return strtolower(trim((string) self::$orientation)) === 'landscape' ? 'landscape' : 'portrait';
    }

    private static function buildUriResolver(): UriResolver
    {
        $cache_dir = self::resolveTypesetCacheDir();
        $schemes = [
            'data' => new DataUriResolver($cache_dir),
        ];

        $allowed_protocols = self::resolveTypesetAllowedProtocols();
        if ($allowed_protocols !== []) {
            $http = new HttpUriResolver(
                $cache_dir,
                self::resolveTypesetTimeout(),
                self::resolveTypesetDownloadLimit()
            );

            foreach ($allowed_protocols as $protocol) {
                $schemes[$protocol] = $http;
            }
        }

        $allowed_directories = self::resolveTypesetAllowedDirectories();
        if ($allowed_directories !== []) {
            $schemes['file'] = new FileUriResolver($allowed_directories);
        }

        return new UriResolver($schemes, self::resolveTypesetBaseDir());
    }

    /**
     * @return list<string>
     */
    private static function resolveTypesetAllowedDirectories(): array
    {
        $directories = config('xendivel.typesetsh.allowed_directories', [public_path()]);
        if (! is_array($directories)) {
            return [];
        }

        $resolved_directories = [];
        foreach ($directories as $directory) {
            if (! is_string($directory)) {
                continue;
            }

            $directory = trim($directory);
            if ($directory === '') {
                continue;
            }

            $resolved_directories[] = $directory;
        }

        return array_values(array_unique($resolved_directories));
    }

    /**
     * @return list<string>
     */
    private static function resolveTypesetAllowedProtocols(): array
    {
        $protocols = config('xendivel.typesetsh.allowed_protocols', ['http', 'https']);
        if (! is_array($protocols)) {
            return [];
        }

        $resolved_protocols = [];
        foreach ($protocols as $protocol) {
            if (! is_string($protocol)) {
                continue;
            }

            $protocol = strtolower(trim($protocol));
            if ($protocol === '') {
                continue;
            }

            $resolved_protocols[] = $protocol;
        }

        return array_values(array_unique($resolved_protocols));
    }

    private static function resolveTypesetBaseDir(): string
    {
        $base_dir = config('xendivel.typesetsh.base_dir', '');
        if (! is_string($base_dir)) {
            return '';
        }

        return trim($base_dir);
    }

    private static function resolveTypesetCacheDir(): ?string
    {
        $cache_dir = config('xendivel.typesetsh.cache_dir', storage_path('framework/cache/typesetsh'));
        if (! is_string($cache_dir)) {
            return null;
        }

        $cache_dir = trim($cache_dir);

        return $cache_dir === '' ? null : $cache_dir;
    }

    private static function resolveTypesetTimeout(): int
    {
        $timeout = (int) config('xendivel.typesetsh.timeout', 15);

        return $timeout > 0 ? $timeout : 15;
    }

    private static function resolveTypesetDownloadLimit(): int
    {
        $download_limit = (int) config('xendivel.typesetsh.download_limit', 1024 * 1024 * 5);

        return $download_limit > 0 ? $download_limit : (1024 * 1024 * 5);
    }

    private static function applyPageOrientation(PdfDocument $document, string $orientation): void
    {
        self::applyPageOrientationToPages($document->Catalog->Pages->Kids ?? [], $orientation);
    }

    private static function applyPageOrientationToPages(iterable $pages, string $orientation): void
    {
        foreach ($pages as $page) {
            if (($page->Type ?? null) === '/Pages') {
                self::applyPageOrientationToPages($page->Kids ?? [], $orientation);

                continue;
            }

            if (($page->Type ?? null) !== '/Page') {
                continue;
            }

            self::applyPageOrientationToPage($page, $orientation);
        }
    }

    private static function applyPageOrientationToPage(object $page, string $orientation): void
    {
        foreach (['MediaBox', 'BleedBox', 'TrimBox', 'CropBox', 'ArtBox'] as $box) {
            $bounds = $page->{$box} ?? null;

            if (! is_array($bounds) || count($bounds) < 4) {
                continue;
            }

            $x = (float) $bounds[0];
            $y = (float) $bounds[1];
            $width = (float) $bounds[2] - $x;
            $height = (float) $bounds[3] - $y;

            if ($width <= 0 || $height <= 0) {
                continue;
            }

            $must_swap = $orientation === 'landscape'
                ? $height > $width
                : $width > $height;

            if (! $must_swap) {
                continue;
            }

            $page->{$box} = [$x, $y, $x + $height, $y + $width];
        }
    }
}
