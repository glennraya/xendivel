<?php

namespace App\Listeners;

use App\Events\eWalletEvents;

class eWalletWebhookListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handles the incoming webhook from Xendit API.
     *
     * This method processes webhook notifications sent by Xendit API. The data received from the webhook
     * is expected to be an array, containing relevant information from the API response. This method
     * serves as a central point to implement various related tasks such as:
     *
     * - Saving transactional data or updates to the database.
     * - Triggering additional processes based on the webhook data (e.g., email notifications).
     * - Interacting with other internal or external APIs based on the received data.
     * - Performing validations and logging for audit or debugging purposes.
     *
     * It's crucial to ensure that this method handles the data securely and efficiently, maintaining
     * the integrity and performance of the application.
     */
    public function handle(eWalletEvents $event)
    {
        // You can inspect the returned data from the webhoook in your logs file
        // storage/logs/laravel.log
        logger('Webhook data received: ', $event->webhook_data);
    }
}
