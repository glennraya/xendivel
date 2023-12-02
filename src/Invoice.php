<?php

namespace GlennRaya\Xendivel;

use Exception;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Invoice
{
    public static $invoice;

    public static $invoice_storage_path = '/app/invoices/';

    /**
     * Generate the invoice and save it to storage.
     *
     * @param  array  $invoice_data  The associative array of information to be displayed on the invoice.
     * @param  string  $new_filename  Optional. The new filename of the invoice.
     * @param  string  $size  Paper size, defaults to A4.
     */
    public static function make(array $invoice_data, string $new_filename = null)
    {
        $html = view('vendor.xendivel.views.invoice', [
            'invoice_data' => $invoice_data,
        ])->render();

        $new_filename = $new_filename === null || $new_filename === ''
            ? Str::uuid().'-invoice.pdf'
            : $new_filename.'-invoice.pdf';

        self::$invoice = Browsershot::html($html)
            ->newHeadless()
            ->showBackground()
            ->margins(4, 0, 4, 0);

        return new self();
    }

    /**
     * It will generate a copy of the invoice then temporarily saves
     * it in storage. Then it will download a copy of the invoice.
     *
     * After a successful download, the copy of the invoice
     * will be deleted from storage, thereby saving
     * some space on the disk.
     *
     * @param  array  $invoice_data  Required. The associative array of information to be displayed on the invoice.
     * @param  string|null  $filename  Optional. The new filename for the downloaded invoice. Defaults to UUID v4.
     * @param  string  $paper_size  Optional. The paper size of the invoice. Defaults to A4.
     * @param  string  $orientation  Optional. The orientation of the invoice (portrait, landscape).
     * @throws Exception  if the file does not exists.
     */
    public static function download(array $invoice_data, string $new_filename = null, string $paper_size = 'A4', string $orientation = 'portrait'): BinaryFileResponse
    {
        $html = view('vendor.xendivel.views.invoice', [
            'invoice_data' => $invoice_data,
        ])->render();

        $new_filename = $new_filename === null || $new_filename === ''
            ? Str::uuid().'-invoice.pdf'
            : $new_filename.'-invoice.pdf';

        Browsershot::html($html)
            ->newHeadless()
            ->showBackground()
            ->margins(4, 0, 4, 0)
            ->format($paper_size)
            ->landscape($orientation === 'landscape' ? true : false)
            ->save(config('xendivel.invoice_storage_path').$new_filename);

        $file_path = config('xendivel.invoice_storage_path').$new_filename;

        if (! file_exists(config('xendivel.invoice_storage_path').$new_filename)) {
            throw new Exception("The file does not exist at the location: {$file_path}.");
        }

        return response()->downloadAndDelete($file_path, $new_filename, ['Content-Type: application/pdf']);
    }

    /**
     * Set the orientation of the invoice (portrait, landscape).
     *
     * @param  string  $orientation  The orientation of the invoice (portrait or landscape).
     */
    public function orientation(string $orientation = 'portrait'): self
    {
        self::$invoice->landscape($orientation === 'landscape' ? true : false);

        return $this;
    }

    /**
     * Set the paper size of the invoice.
     *
     * @param  string  $paper_size  By default sets to A4.
     */
    public function paperSize($paper_size = 'A4'): self
    {
        self::$invoice->format($paper_size);

        return $this;
    }

    /**
     * Save the invoice to storage.
     *
     * @param  string|null  $filename  Optional. The filename of the invoice. If not specified defaults to UUID v4.
     */
    public function save(string $filename = null): string
    {
        $filename = $filename === null
            ? Str::uuid().'-invoice.pdf'
            : $filename.'-invoice.pdf';

        self::$invoice->save(config('xendivel.invoice_storage_path').$filename);

        return $filename;
    }
}
