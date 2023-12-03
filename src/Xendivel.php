<?php

namespace GlennRaya\Xendivel;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use GlennRaya\Xendivel\Mail\InvoicePaid;
use GlennRaya\Xendivel\Validations\CardValidationService;

class Xendivel extends XenditApi
{
    /**
     * Request payload when executing API call.
     */
    public static $payload;

    /**
     * Refund response from the API call.
     */
    public $refundResponse;

    public $invoice_pdf;

    public $mailer;

    public $mailer_message;

    public $subject;

    public $message;

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

        // Return the instance of the Xendivel class to enable method chaining.
        return new self();

    }

    /**
     * Send an invoice to the specified e-mail address, typically the customer's e-mail.
     *
     * @param  string  $email  Required. The e-mail address where the invoice should be sent.
     * @param  array  $invoice_data  Required. The associative array of information to be displayed on the invoice.
     */
    public function emailInvoiceTo(string $email, array $invoice_data): self
    {
        $this->invoice_pdf = Invoice::make($invoice_data)->save();

        if(config('xendivel.queue_invoice_email')) {
            $this->mailer = Mail::to($email);
        } else {
            $this->mailer = Mail::to($email);
        }

        return $this;
    }

    /**
     * The subject of the invoice email.
     *
     * @param  string|null  $subject  Optional. Defaults to 'Invoice Paid'
     */
    public function subject(string $subject = null): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * The message for the invoice email.
     *
     * @param  string|null  $message  Optional. A default thank you message was provided.
     * @return self
     */
    public function message(string $message = null): self
    {
        $this->mailer_message = $message;
        return $this;
    }

    /**
     * Will send the invoice email either queued or will immediately send.
     */
    public function send(): self
    {
        if(config('xendivel.queue_invoice_email')) {
            $this->mailer->queue(new InvoicePaid($this->invoice_pdf, $this->subject, $this->message));
        } else {
            $this->mailer->send(new InvoicePaid($this->invoice_pdf, $this->subject, $this->message));
        }

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
