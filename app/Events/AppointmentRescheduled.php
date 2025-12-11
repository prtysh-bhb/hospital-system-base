<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentRescheduled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Appointment $appointment;
    public array $oldData; // Previous date/time
    public array $newData; // New date/time
    public $rescheduledBy; // User who rescheduled

    /**
     * Create a new event instance.
     */
    public function __construct(Appointment $appointment, array $oldData, array $newData, $rescheduledBy = null)
    {
        $this->appointment = $appointment;
        $this->oldData = $oldData;
        $this->newData = $newData;
        $this->rescheduledBy = $rescheduledBy;
    }
}
