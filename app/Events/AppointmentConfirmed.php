<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Appointment $appointment;
    public $confirmedBy; // User who confirmed

    /**
     * Create a new event instance.
     */
    public function __construct(Appointment $appointment, $confirmedBy = null)
    {
        $this->appointment = $appointment;
        $this->confirmedBy = $confirmedBy;
    }
}
