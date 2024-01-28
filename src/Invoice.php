<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Concerns\InvoicePathResolver;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Invoice
{
    use InvoicePathResolver;

    private static $invoice;

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
        $template = self::$template;

        self::$invoice_data = $invoice_data;

        if (! is_dir(resource_path('views/vendor/xendivel'))) {
            $template = 'xendivel::invoice';
        } else {

            file_exists(resource_path('views/vendor/xendivel')."/$template.blade.php")
                ? $template = 'vendor.xendivel.'.$template
                : throw new Exception("The $template.blade.php doesn't exists in 'resources/views/vendor/xendivel'");
        }

        try {

            $html = view($template, [
                'invoice_data' => $invoice_data,
            ])->render();

        } catch (Exception $e) {
            throw new Exception(
                $template === null
                ? "The invoice template can't be located. Be sure that you published Xendivel's assets by running: php artisan vendor:publish --tag=xendivel."
                : $e->getMessage()
            );
        }

        self::$invoice = Browsershot::html($html)
            ->newHeadless()
            ->showBackground()
            ->margins(4, 0, 4, 0);

        return new self();
    }

    /**
     * Download the invoice.
     *
     * After a successful download, the copy of the invoice
     * will be deleted from storage, thereby saving
     * some space on the disk.
     *
     * @throws Exception if the file does not exists.
     */
    public function download(): BinaryFileResponse
    {
        // The filename defaults to UUID v4 if none was provided.
        $new_filename = self::$filename === null || self::$filename === ''
            ? Str::uuid().'-invoice.pdf'
            : self::$filename.'-invoice.pdf';

        self::$invoice->format(self::$paper_size);
        self::$invoice->landscape(self::$orientation === 'landscape' ? true : false);

        self::$invoice->save(
            self::resolveInvoicePath($new_filename)
        );

        $invoice_path = self::resolveInvoicePath($new_filename);

        // Throw an exception if the invoice file is not in storage.
        if (! file_exists($invoice_path)) {
            throw new Exception("The file does not exist at the location: $invoice_path.");
        }

        // Download the invoice if everything is ok, and will automatically
        // delete the temporary file after successful download.
        return response()->downloadAndDelete(
            self::resolveInvoicePath($new_filename), $new_filename, ['Content-Type: application/pdf']
        );
    }

    /**
     * Specify a different template for the invoice.
     *
     * @param  string|null  $template  [optional]  The filename for the invoice template.
     */
    public function template(?string $template = null): self
    {
        self::$template = $template;

        self::make(self::$invoice_data);

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
        $filename = self::$filename === null || self::$filename === '' || self::$filename === ' '
            ? Str::uuid().'-invoice.pdf'
            : self::$filename.'-invoice.pdf';

        self::$invoice->format(self::$paper_size);
        self::$invoice->landscape(self::$orientation === 'landscape' ? true : false);

        self::$invoice->save(
            self::resolveInvoicePath($filename)
        );

        return self::resolveInvoicePath($filename);
    }
}
