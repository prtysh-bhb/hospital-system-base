<?php

namespace App\Providers;

use App\Events\AppointmentBooked;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentRescheduled;
use App\Events\UserPasswordGenerated;
use App\Listeners\LogAppointmentActivity;
use App\Listeners\SendAppointmentNotification;
use App\Listeners\SendPasswordEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Appointment Events
        AppointmentBooked::class => [
            SendAppointmentNotification::class . '@handleBooked',
            LogAppointmentActivity::class . '@handleBooked',
        ],
        AppointmentConfirmed::class => [
            SendAppointmentNotification::class . '@handleConfirmed',
            LogAppointmentActivity::class . '@handleConfirmed',
        ],
        AppointmentCancelled::class => [
            SendAppointmentNotification::class . '@handleCancelled',
            LogAppointmentActivity::class . '@handleCancelled',
        ],
        AppointmentRescheduled::class => [
            SendAppointmentNotification::class . '@handleRescheduled',
            LogAppointmentActivity::class . '@handleRescheduled',
        ],

        // User Password Events
        UserPasswordGenerated::class => [
            SendPasswordEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
