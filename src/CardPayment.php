<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Validations\CardValidationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CardPayment extends Xendivel
{
    /**
     * Request payload when executing API call.
     */
    public static $payload;

    /**
     * Response from Xendit API.
     */
    public static $api_response;

    /**
     * Refund response from the API call.
     */
    public $refundResponse;

    public $invoice;

    /**
     * Make a payment request using the tokenized value of the
     * card. Then, return the CardPayment class to create
     * new instance class for method chaining.
     */
    public static function makePayment($payload): self
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
        $response = Xendivel::api('post', '/credit_card_charges', $api_payload);
        $response_body = $response->getBody();
        $data = json_decode($response_body, true);

        // Merge the data from the user request payload and the response from
        // the Xendit API call. This will be used to create the invoice.
        self::$api_response = array_merge($data, $payload);

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
     * Download a copy of the invoice.
     *
     * @param  string $file - The filename of the invoice in storage.
     * @param  string $filename - Optional second param for the new filename.
     */
    public static function downloadInvoice(string $file, string $filename = null): StreamedResponse
    {
        $file_path = 'invoices/'.$file;

        if(! Storage::exists($file_path)){
            throw new Exception("The file does not exist at the location: {$file_path}.");
        }

        return Storage::download('invoices/'.$file, $filename, ['Content-Type: application/pdf']);
    }

    /**
     * Generate the invoice and save it to storage.
     */
    public function generateInvoice(): self
    {
        // Pass the $api_response data to the invoice view to create the invoice.
        $html = view('vendor.xendivel.views.invoice', [
            "invoice_data" => self::$api_response,
        ])->render();

        $invoice = Str::uuid() . '-invoice.pdf';
        $this->invoice = $invoice;
        Browsershot::html($html)
            ->newHeadless()
            ->showBackground()
            ->margins(12, 0, 12, 0)
            ->save(storage_path('/app/invoices/'.$invoice));

        return $this;
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
