<?php

use GlennRaya\Xendivel\Invoice;
use GlennRaya\Xendivel\Mail\InvoicePaid;
use GlennRaya\Xendivel\Mail\RefundConfirmation;
use GlennRaya\Xendivel\Xendivel;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->invoiceStoragePath = sys_get_temp_dir().'/xendivel-mail-test-invoices/';

    if (! is_dir($this->invoiceStoragePath)) {
        mkdir($this->invoiceStoragePath, 0755, true);
    }

    foreach (glob($this->invoiceStoragePath.'*.pdf') ?: [] as $file) {
        unlink($file);
    }

    config([
        'xendivel.invoice_storage_path' => $this->invoiceStoragePath,
        'xendivel.queue_email' => false,
    ]);

    Invoice::make(xendivelMailInvoiceData())
        ->fileName(null)
        ->paperSize('Letter')
        ->orientation('portrait')
        ->template('invoice');
});

afterEach(function () {
    foreach (glob($this->invoiceStoragePath.'*.pdf') ?: [] as $file) {
        unlink($file);
    }
});

it('sends paid invoice emails with a generated pdf attachment', function () {
    Mail::fake();

    (new Xendivel)
        ->emailInvoiceTo('victoria@example.com', xendivelMailInvoiceData())
        ->subject('Payment received')
        ->message('Thanks for your payment.')
        ->send();

    Mail::assertSent(InvoicePaid::class, function (InvoicePaid $mail) {
        return $mail->hasTo('victoria@example.com')
            && $mail->subject === 'Payment received'
            && $mail->message === 'Thanks for your payment.'
            && count($mail->attachments()) === 1;
    });

    expect(glob($this->invoiceStoragePath.'*.pdf') ?: [])->toHaveCount(1);
});

it('queues invoice emails when configured', function () {
    config(['xendivel.queue_email' => true]);

    Mail::fake();

    (new Xendivel)
        ->emailInvoiceTo('victoria@example.com', xendivelMailInvoiceData())
        ->send();

    Mail::assertQueued(InvoicePaid::class);
    Mail::assertNothingSent();
});

it('sends refund confirmation emails for successful refunds', function () {
    Mail::fake();

    $xendivel = new Xendivel;
    $xendivel->refund_response = ['status' => 'SUCCEEDED'];

    $xendivel
        ->emailRefundConfirmationTo('victoria@example.com')
        ->subject('Refund complete')
        ->message('Your refund is on the way.')
        ->send();

    Mail::assertSent(RefundConfirmation::class, function (RefundConfirmation $mail) {
        return $mail->hasTo('victoria@example.com')
            && $mail->subject === 'Refund complete'
            && $mail->message === 'Your refund is on the way.';
    });
});

it('does not send failed refund confirmation emails', function () {
    Mail::fake();

    $xendivel = new Xendivel;
    $xendivel->refund_response = ['status' => 'FAILED'];

    $xendivel
        ->emailRefundConfirmationTo('victoria@example.com')
        ->send();

    Mail::assertNothingSent();
    Mail::assertNothingQueued();
});

function xendivelMailInvoiceData(): array
{
    return [
        'invoice_number' => 1000023,
        'card_type' => 'VISA',
        'masked_card_number' => '400000XXXXXX0002',
        'merchant' => [
            'name' => 'Xendivel LLC',
            'address' => '152 Maple Avenue Greenfield, New Liberty, Arcadia USA 54331',
            'phone' => '+63 971-444-1234',
            'email' => 'xendivel@example.com',
        ],
        'customer' => [
            'name' => 'Victoria Marini',
            'address' => '4457 Pine Circle, Rivertown, Westhaven, 98765, Silverland',
            'email' => 'victoria@example.com',
            'phone' => '+63 909-098-654',
        ],
        'items' => [
            ['item' => 'iPhone 15 Pro Max', 'price' => 1099, 'quantity' => 1],
        ],
        'tax_rate' => .12,
        'tax_id' => '123-456-789',
        'footer_note' => 'Thank you for your recent purchase with us.',
    ];
}
