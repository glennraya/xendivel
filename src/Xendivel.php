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
     * Payment response from the API call.
     *
     * @var object
     */
    public static $get_payment_response;

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
     * Get the charge transaction by charge id or external id.
     *
     * @param  string  $charge_id  [required]  The ID of the of the payment after CAPTURED/AUTHORIZED.
     */
    public static function getPayment(string $id): self
    {
        $response = XenditApi::api('get', "credit_card_charges/{$id}", []);

        if ($response->failed()) {
            throw new Exception($response);
        }

        self::$get_payment_response = json_decode($response);

        return new self();
    }

    /**
     * Request for a refund.
     *
     * @param  int  $amount [required]  The amount to be refunded. Can be partial amount.
     * @param  string  $external_id [optional]  The external id provided by the user or auto provided.
     */
    public function refund(int $amount, string $external_id = '')
    {
        if(config('xendivel.auto_external_id') === false && $external_id === '') {
            throw new Exception('External ID Error: The auto generate external id is set to "false" in your config file, but did not provide your own in the request. Xendit requires external id as part of your parameters on this request.');
        }

        $external_id = config('xendivel.auto_external_id') === true
            ? Str::uuid()
            : $external_id;

        $payload = [
            'amount' => $amount,
            'external_id' => $external_id,
            'idempotency' => Str::uuid().'x-idempotency-key',
        ];

        $payment_id = self::$get_payment_response->id;

        $response = XenditApi::api(
            'post',
            "credit_card_charges/{$payment_id}/refunds",
            $payload
        );

        if ($response->failed()) {
            throw new Exception($response);
        }

        return $this;
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
