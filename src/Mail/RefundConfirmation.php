<?php

namespace GlennRaya\Xendivel\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  mixed|null  $subject  [optional] The subject of the email.
     * @param  mixed|null  $message  [optional] The email message.
     * @return void
     */
    public function __construct(public $subject = null, public $message = null)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject === null ? 'Refund Confirmation' : $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $template = '';

        if (! is_dir(resource_path('views/vendor/xendivel'))) {
            $template = 'xendivel::emails.invoices.refund-confirmation';
        } else {
            $template = 'vendor.xendivel.views.emails.invoices.refund-confirmation';
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
        return [];
    }
}
