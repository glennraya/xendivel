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
     * Handle the webhook from Xendit API. The webhook data from the API
     * will be an array containing the status of the request. You can
     * now perform any tasks that you require here.
     */
    public function handle(eWalletEvents $event): void
    {
        // You can inspect the returned data from the webhoook in your logs file
        // storage/logs/laravel.log
        logger('Webhook data received: ', $event->webhook_data);
    }
}
