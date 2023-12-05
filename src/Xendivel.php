<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Mail\InvoicePaid;
use GlennRaya\Xendivel\Validations\CardValidationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Xendivel extends XenditApi
{
    /**
     * Request payload when executing API call.
     *
     * @var mixed
     */
    public static $payload;

    /**
     * Refund response from the API call.
     *
     * @var array
     */
    public $refundResponse;

    /**
     * An instance of the Invoice class.
     *
     * @var GlennRaya\Xendivel\Invoice
     */
    public $invoice_pdf;

    /**
     * An instance of the Mail facade.
     *
     * @var Illuminate\Support\Facades\Mail
     */
    public $mailer;

    /**
     * The message of the email for the invoice.
     *
     * @var string
     */
    public $mailer_message;

    /**
     * The subject of the invoice email.
     *
     * @var string
     */
    public $subject;

    /**
     * Make a payment request with the tokenized value of the card.
     *
     * @param  mixed  $payload  [required]  The tokenized data of the card and other data.
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
     * Request for a refund.
     *
     * @param  array  $payload [required]  The request payload for the refund.
     */
    public static function refund(array $payload)
    {
        $response = XenditApi::api(
            'post',
            "/credit_card_charges/{$payload['payment_id']}/refunds",
            $payload
        );

        if($response->failed()) {
            throw new Exception($response);
        }

        return $response;
        // return new self();
        // return $payload;
    }

    /**
     * Send an invoice to the specified e-mail address, typically the customer's e-mail.
     *
     * @param  string  $email  [required] The e-mail address where the invoice should be sent.
     * @param  array  $invoice_data  [required] The associative array of information to be displayed on the invoice.
     * @param  string  $template  [optional] The invoice blade template file.
     */
    public function emailInvoiceTo(string $email, array $invoice_data, string $template = 'invoice'): self
    {
        $this->invoice_pdf = Invoice::make($invoice_data, null, $template)->save();

        if (config('xendivel.queue_invoice_email')) {
            $this->mailer = Mail::to($email);
        } else {
            $this->mailer = Mail::to($email);
        }

        return $this;
    }

    /**
     * The subject of the invoice email.
     *
     * @param  string|null  $subject  [optional] Defaults to 'Invoice Paid'
     */
    public function subject(string $subject = null): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * The message for the invoice email.
     *
     * @param  string|null  $message  [optional]  A default thank you message was provided.
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
        if (config('xendivel.queue_email')) {
            $this->mailer->queue(new InvoicePaid($this->invoice_pdf, $this->subject, $this->mailer_message));
        } else {
            $this->mailer->send(new InvoicePaid($this->invoice_pdf, $this->subject, $this->mailer_message));
        }

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
