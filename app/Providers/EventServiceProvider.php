<?php

namespace App\Providers;

use App\Events\BookingAccepted;
use App\Events\BookingDispatched;
use App\Events\BookingStatusUpdated;
use App\Listeners\NotifyCustomerOfAcceptance;
use App\Listeners\NotifyCustomerOfStatusUpdate;
use App\Listeners\NotifyProvidersOfNewJob;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        BookingDispatched::class => [
            NotifyProvidersOfNewJob::class,
        ],
        BookingAccepted::class => [
            NotifyCustomerOfAcceptance::class,
        ],
        BookingStatusUpdated::class => [
            NotifyCustomerOfStatusUpdate::class,
        ],
    ];
}
