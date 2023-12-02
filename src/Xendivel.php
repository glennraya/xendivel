<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Concerns\Invoice;
use GlennRaya\Xendivel\Validations\CardValidationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Xendivel extends XenditApi
{
    // use Invoice;

    /**
     * Request payload when executing API call.
     */
    public static $payload;

    /**
     * Refund response from the API call.
     */
    public $refundResponse;

    public static $invoice;

    public static $invoice_storage_path = '/app/invoices/';

    /**
     * Make a payment request using the tokenized value of the card.
     *
     * @param  mixed  $payload  The tokenized data of the card and amount.
     */
    public static function payWithCard($payload): self
    {
        $payload = $payload->toArray();

        // Validate the payload.
        CardValidationService::validate($payload);

        $api_payload = [
            'amount' => $payload['amount'],
            'external_id' => config('xendivel.auto_external_id') === true
                ? Str::uuid()
                : $payload['external_id'],
            'token_id' => $payload['token_id'],
        ];

        // Merge these values below to the $api_payload if entered by the user.
        // List of optional fields
        $optionalFields = ['descriptor', 'currency', 'billing_details', 'metadata'];

        // Merge optional values to the $api_payload if they are set and not empty.
        foreach ($optionalFields as $field) {
            if (isset($payload[$field]) && $payload[$field] !== '') {
                $api_payload[$field] = $payload[$field];
            }
        }

        // Attempt to charge the card.
        $response = XenditApi::api('post', '/credit_card_charges', $api_payload);

        // Thrown an exception on failure.
        if ($response->failed()) {
            throw new Exception($response);
        }

        // Return the instance of the CardPayment class to enable method chaining.
        return new self();

    }

    /**
     * Send an invoice to the customer's e-mail address.
     */
    public function sendInvoiceTo(string $email): self
    {
        return $this;
    }

    /**
     * It will generate a copy of the invoice then saves it in
     * storage. Then it will download a copy of the invoice.
     *
     * After successful download, the copy of the invoice
     * will be deleted from storage, thereby saving
     * some space on the disk.
     *
     * @param  array  $invoice_data  The associative array of information to be displayed on the invoice.
     * @param  string  $filename  Optional. The new filename for the downloaded invoice.
     * @param  string  $paper_size  Optional. The paper size of the invoice.
     *
     * @throws Exception if the file does not exists.
     */
    public static function downloadInvoice(array $invoice_data = [], string $new_filename = null, string $paper_size = 'A4'): BinaryFileResponse
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
            ->save(config('xendivel.invoice_storage_path').$new_filename);

        $file_path = 'invoices/'.$new_filename;

        if (! Storage::exists($file_path)) {
            throw new Exception("The file does not exist at the location: {$file_path}.");
        }

        return response()->downloadAndDelete(
            storage_path('/app/'.$file_path),
            $new_filename,
            ['Content-Type: application/pdf']
        );
    }

    /**
     * Generate the invoice and save it to storage.
     *
     * @param  array  $invoice_data  The associative array of information to be displayed on the invoice.
     * @param  string  $new_filename  Optional. The new filename of the invoice.
     * @param  string  $size  Paper size, defaults to A4.
     */
    public static function generateInvoice(array $invoice_data, string $new_filename = null)
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
    public function saveInvoice(string $filename = null): string
    {
        $filename = $filename === null
            ? Str::uuid().'-invoice.pdf'
            : $filename.'-invoice.pdf';

        self::$invoice->save(config('xendivel.invoice_storage_path').$filename);

        return $filename;
    }

    /**
     * Request for a refund.
     */
    public function refund(): self
    {
        return $this;
    }

    /**
     * Send refund confirmation e-mail to customer.
     */
    public function sendRefundConfirmationEmail(string $email): self
    {
        return $this;
    }
}
