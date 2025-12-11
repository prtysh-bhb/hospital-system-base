<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentBooked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Appointment $appointment;
    public string $bookedVia; // 'public', 'admin', 'frontdesk'

    /**
     * Create a new event instance.
     */
    public function __construct(Appointment $appointment, string $bookedVia = 'public')
    {
        $this->appointment = $appointment;
        $this->bookedVia = $bookedVia;
    }
}
