<?php

namespace GlennRaya\Xendivel\Concerns;

use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

trait Invoice
{
    private static $paper_size;

    private static $filename;

    /**
     * Generate the invoice
     *
     * @param  array  $invoice_data - An associative array that contains data for the invoice. Default is empty array.
     */
    public static function createInvoice(array $invoice_data): self
    {
        // dd(self::$paper_size);
        $html = view('vendor.xendivel.views.invoice', [
            'invoice_data' => $invoice_data,
        ])->render();

        $new_filename = self::$filename === null ? Str::uuid().'-invoice.pdf' : self::$filename.'-invoice.pdf';

        Browsershot::html($html)
            ->newHeadless()
            ->showBackground()
            ->margins(4, 0, 4, 0)
            ->format('A0')
            ->save(storage_path('/app/invoices/'.$new_filename));

        return new self();
    }

    /**
     * Generate a new filename for the invoice.
     *
     * @param  string  $filename - Optional. The new filename of the invoice. Default is null.
     */
    public static function filename(string $filename = 'dddd')
    {
        self::$filename = $filename;
    }

    /**
     * Specifies the paper size of the invoice. Defaults to A4.
     *
     * @param  string  $paper_size - Optional. The paper size of the invoice. Default is A4.
     */
    public function paperSize(string $size = 'A4'): self
    {
        self::$paper_size = $size;

        return $this;
    }

    /**
     * Specifies the orientation of the invoice. Defaults to "portrait".
     * Possible values: portrait, landscape
     *
     * @param  string  $orientation - Optional. The orientation of the invoice. Default is portrait.
     */
    public function orientation(string $orientation = 'portrait'): self
    {
        return $this;
    }
}
