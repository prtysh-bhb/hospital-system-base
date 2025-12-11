<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Appointment $appointment;
    public string $reason;
    public $cancelledBy; // User who cancelled

    /**
     * Create a new event instance.
     */
    public function __construct(Appointment $appointment, string $reason = '', $cancelledBy = null)
    {
        $this->appointment = $appointment;
        $this->reason = $reason;
        $this->cancelledBy = $cancelledBy;
    }
}
