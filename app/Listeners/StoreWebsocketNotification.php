<?php

namespace App\Listeners;

use App\Services\WebsocketNotificationService;

class StoreWebsocketNotification
{
    private $notifications;

    public function __construct(WebsocketNotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle($event)
    {
        if (request()->is('test/socket/*')) {
            return;
        }

        $this->notifications->record($event);
    }
}