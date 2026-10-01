<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\NewReferral;
use App\Events\SocketReco;
use App\Events\SocketReferralAccepted;
use App\Events\SocketReferralAdmitted;
use App\Events\SocketReferralArrived;
use App\Events\SocketReferralDeparted;
use App\Events\SocketReferralDischarged;
use App\Events\SocketReferralNotArrived;
use App\Events\SocketReferralRejected;
use App\Events\SocketReferralUpdate;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        NewReferral::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReco::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralAccepted::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralAdmitted::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralArrived::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralDeparted::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralDischarged::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralNotArrived::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralRejected::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
        SocketReferralUpdate::class => [
            'App\Listeners\StoreWebsocketNotification',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
