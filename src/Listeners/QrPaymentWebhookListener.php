<?php

namespace App\Listeners;

use App\Events\QrPaymentEvents;

class QrPaymentWebhookListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handles the incoming QR payment webhook from Xendit API.
     *
     * This method processes the QR payment callback sent by Xendit when a customer scans
     * and pays a QR code. The data received from the webhook is expected to be an array,
     * containing relevant information from the API response. This method serves as a
     * central point to implement various related tasks such as:
     *
     * - Saving transactional data or updates to the database.
     * - Triggering additional processes based on the webhook data (e.g., email notifications).
     * - Interacting with other internal or external APIs based on the received data.
     * - Performing validations and logging for audit or debugging purposes.
     *
     * It's crucial to ensure that this method handles the data securely and efficiently,
     * maintaining the integrity and performance of the application.
     */
    public function handle(QrPaymentEvents $event)
    {
        // You can inspect the returned data from the webhook in your logs file
        // storage/logs/laravel.log
        logger('QR payment webhook data received: ', $event->webhook_data);

        // Xendit's Payments API nests the payment under a "data" key. The QR
        // payment is settled once "data.status" is "SUCCEEDED". Use this to mark
        // the order as paid, send a receipt, fulfill the purchase, etc.
        $status = $event->webhook_data['data']['status'] ?? $event->webhook_data['status'] ?? null;

        if ($status === 'SUCCEEDED') {
            // Handle the successful QR payment here. Useful fields on the payload:
            //   $event->webhook_data['data']['payment_request_id']  (pr-...)
            //   $event->webhook_data['data']['reference_id']        (your reference_id)
            //   $event->webhook_data['data']['channel_code']        (e.g. QRPH)
        }
    }
}
