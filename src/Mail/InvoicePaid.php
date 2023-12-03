<?php

namespace GlennRaya\Xendivel\Mail;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class InvoicePaid extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected string $invoice_pdf, public $subject = null, public $message = null)
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
        return new Content(
            markdown: 'vendor.xendivel.views.emails.invoices.paid',
            with: [
                'message' => $this->message,
            ]
        );
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
            Attachment::fromPath(config('xendivel.invoice_storage_path').$this->invoice_pdf)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}
