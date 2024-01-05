<?php

namespace GlennRaya\Xendivel;

use Exception;
use GlennRaya\Xendivel\Mail\InvoicePaid;
use GlennRaya\Xendivel\Mail\RefundConfirmation;
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

    public static $charge_type = '';

    /**
     * Refund response from the API call.
     *
     * @var array
     */
    public $refund_response = [];

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
     * Type of the email (invoice or refund confirmation).
     *
     * @var string
     */
    public $email_type;

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
     * @param  Illuminate\Http\Requests  $payload [required]  The tokenized data of the card and other data.
     */
    public static function payWithCard($payload): self
    {
        // Turn the request payload to an array.
        $payload = $payload->toArray();

        // Validate the payload.
        CardValidationService::validate($payload);

        $api_payload = [
            'amount' => $payload['amount'],
            'external_id' => config('xendivel.auto_id') === true
                ? Str::orderedUuid()
                : $payload['external_id'],
            'token_id' => $payload['token_id'],
            'authentication_id' => $payload['authentication_id'],
        ];

        // Merge these values below to the $api_payload if entered by the user.
        // List of optional fields
        $optional_fields = ['descriptor', 'currency', 'billing_details', 'metadata'];

        // Merge optional values to the $api_payload if they are set and not empty.
        foreach ($optional_fields as $field) {
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
     * Get the card or ewallet charge transaction by charge id.
     *
     * @param  string  $charge_id [required]  The charge ID of the of the payment (card or ewallet).
     * @param  string  $charge_type [required]   The type of payment method. Either card or ewallet.
     */
    public static function getPayment(string $id, string $charge_type): self
    {
        match ($charge_type) {
            'card' => $response = XenditApi::api('get', "credit_card_charges/$id", []),
            'ewallet' => $response = XenditApi::api('get', "ewallets/charges/$id", []),
        };

        if ($response->failed()) {
            throw new Exception($response);
        }

        self::$get_payment_response = json_decode($response);
        self::$charge_type = $charge_type;

        return new self();
    }

    /**
     * Get the status and details of a specific eWallet refund by its refund ID.
     *
     * @param string $charge_id [required]  The ID of the eWallet charge.
     * @param string $refund_id [required]  The ID of the eWallet refund.
     */
    public static function getEwalletRefund(string $charge_id, string $refund_id): self
    {
        $refund_details = XenditApi::api('get', "/ewallets/charges/$charge_id/refunds/$refund_id", []);

        if ($refund_details->failed()) {
            throw new Exception($refund_details);
        }

        return new self();
    }

    /**
     * Get the details of all eWallet refunds associated with a single eWallet charge identified by charge ID.
     *
     * @param string $charge_id [required]  The eWallet charge ID.
     */
    public static function getListOfEwalletRefunds(string $charge_id): self
    {
        $refund_lists = XenditApi::api('get', "/ewallets/charges/$charge_id/refunds", []);

        if ($refund_lists->failed()) {
            throw new Exception($refund_lists);
        }
        return new self();
    }

    /**
     * Make a payment request via eWallet (Gcash, Shopeepay, Maya, etc.)
     */
    public static function payWithEwallet($payload): self
    {
        if (config('xendivel.auto_id')
            ? $payload['reference_id'] = Str::orderedUuid()
            : $payload['reference_id']) {
        }

        $payload = $payload->toArray();

        $response = XenditApi::api('post', '/ewallets/charges', $payload);

        if ($response->failed()) {
            throw new Exception($response);
        }

        return new self();
    }

    /**
     * Request for a refund. Currently for cards and ewallet charge type.
     *
     * @param int $amount [required]  The amount to be refunded. Can be partial amount.
     * @param string $id [optional]  The external id provided by the user or auto provided.
     * @param string $reason [optional]  The reason for the refund.
     */
    public function refund(int $amount, ?string $id = null, ?string $reason = 'OTHERS'): self
    {
        if (config('xendivel.auto_id') === false && $id === '') {
            throw new Exception('Auto ID Error: The configuration file has "auto generate auto id" set to "false", yet no custom external ID for card charges or reference ID for ewallet charges was provided in the request. Xendit mandates the inclusion of an external/reference ID in the request parameters.');
        }

        $payment_id = self::$get_payment_response->id;

        $charge_id = config('xendivel.auto_id') === true
            ? Str::orderedUuid()
            : $id;

        if(self::$charge_type === 'card') {
            $payload = [
                'amount' => $amount,
                'external_id' => $charge_id,
                'idempotency' => Str::orderedUuid().'x-idempotency-key',
            ];
            $endpoint = "credit_card_charges/$payment_id/refunds";

        } else if(self::$charge_type === 'ewallet') {
            $payload = [
                'amount' => $amount,
                'reason' => Str::upper($reason)
            ];
            $endpoint = "ewallets/charges/$payment_id/refunds";

        }

        $response = XenditApi::api('post', $endpoint, $payload);

        if ($response->failed()) {
            throw new Exception($response);
        }

        $this->refund_response = $response;

        return $this;
    }

    /**
     * Void eWallet charge.
     *
     * @param string $id [required]  The ID of the eWallet charge.
     */
    public static function void(string $id): self
    {
        $response = XenditApi::api('post', "ewallets/charges/$id/void", []);

        if ($response->failed()) {
            throw new Exception($response);
        }

        return new self();
    }

    /**
     * Send an invoice to the specified e-mail address, typically the customer's e-mail.
     *
     * @param  string  $email  [required] The e-mail address where the invoice should be sent.
     * @param  array  $invoice_data  [required] The associative array of information to be displayed on the invoice.
     * @param  string  $template  [optional] The invoice blade template file.
     *
     * @throws Exception
     */
    public function emailInvoiceTo(string $email, array $invoice_data, string $template = 'invoice'): self
    {
        $this->invoice_pdf = Invoice::make($invoice_data, null, $template)->save();

        try {
            $this->mailer = Mail::to($email);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        $this->email_type = 'invoice';

        return $this;
    }

    /**
     * The subject of the invoice or refund confirmation email.
     *
     * @param  string|null  $subject  [optional] Defaults to the subject provided by the mail class.
     */
    public function subject(?string $subject = null): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * The message for the invoice or refund confirmation email.
     *
     * @param  string|null  $message  [optional]  A default message was provided in the template.
     */
    public function message(?string $message = null): self
    {
        $this->mailer_message = $message;

        return $this;
    }

    /**
     * Will send either invoice or refund confirmation emails.
     */
    public function send(): self
    {
        $mail = match ($this->email_type) {
            'invoice' => new InvoicePaid($this->invoice_pdf, $this->subject, $this->mailer_message),
            'refund_confirmation' => new RefundConfirmation($this->subject, $this->mailer_message),
        };

        // If the email type is for refund notification and the status
        // is "FAILED", return immediately and don't do anything.
        if($this->email_type === 'refund_confirmation' && $this->refund_response['status'] === 'FAILED') {
            return $this;
        }

        try {
            if (config('xendivel.queue_email')) {
                $this->mailer->queue($mail);
            } else {
                $this->mailer->send($mail);
            }
        } catch (Exception $exception) {
            throw new Exception('Encountered an error while sending the email: '.$exception->getMessage());
        }

        return $this;
    }

    /**
     * Send refund confirmation e-mail to customer.
     *
     * @param  string  $email [required]  The email address where the confirmation will be sent.
     */
    public function emailRefundConfirmationTo(string $email): self
    {
        try {
            $this->mailer = Mail::to($email);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        $this->email_type = 'refund_confirmation';

        return $this;
    }
}
