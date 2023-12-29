<?php

namespace GlennRaya\Xendivel\Concerns;

use Exception;

trait InvoicePathResolver
{
    /**
     * Resolve the path where the invoices will be stored.
     * It will provide a default if none is provided.
     *
     * @param  string  $filename  Required. The filename of the invoice.
     * @return string  Returns the PDF filename and the full path where the invoice was stored.
     *
     * @throws Exception
     */
    public static function resolveInvoicePath(string $filename): string
    {
        $invoice_storage_path = config('xendivel.invoice_storage_path');

        // If somehow, the storage path for invoices is not defined in the config file
        // or was unspecified, Xendivel will create one in /storage/app/invoices.
        if ($invoice_storage_path === null || $invoice_storage_path === '') {
            storage_path('/app/invoices/'.$filename);
        } else {
            // If the directory where the invoices will be stored
            // doesn't exists, create one.
            if (! is_dir($invoice_storage_path)) {
                try {
                    mkdir($invoice_storage_path, 0755, true);
                } catch (Exception $e) {
                    throw new Exception("Xendivel is unable to create a directory for invoices in $invoice_storage_path. Please ensure that you have the necessary write permissions for this location.");
                }
            }
        }

        return $invoice_storage_path.$filename;
    }
}
