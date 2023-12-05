<?php

namespace GlennRaya\Xendivel\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class InvoicePaid extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  GlennRaya\Xendivel\Invoice  $invoice_pdf [required] The invoice PDF.
     * @param  mixed|null  $subject [optional] The subject of the email.
     * @param  mixed|null  $message [optional] The email message.
     * @return void
     */
    public function __construct(protected $invoice_pdf, public $subject = null, public $message = null)
    {
        $this->invoice_pdf = $invoice_pdf;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject === null ? 'Invoice Paid' : $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $template = '';
        if (! is_dir(resource_path('views/vendor/xendivel'))) {
            $template = 'xendivel::emails.invoices.paid';
        } else {
            $template = 'vendor.xendivel.views.emails.invoices.paid';
        }

        try {
            return new Content(
                markdown: $template,
                with: [
                    'message' => $this->message,
                ]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $filename = now()->timestamp.'-'.Str::random().'-'.config('app.name').'-invoice.pdf';

        return [
            Attachment::fromPath($this->invoice_pdf)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}
