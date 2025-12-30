<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\AppointmentHistory;
use Illuminate\Support\Facades\Auth;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $actorId = null;
        try {
            if (function_exists('logger')) {
                logger()->info('Appointment created - creating history', [
                    'appointment_id' => $appointment->id,
                    'appointment_date' => $appointment->appointment_date,
                    'appointment_time' => $appointment->appointment_time,
                ]);
            }
            $actorId = Auth::id();
        } catch (\Exception $e) {
            $actorId = null;
        }

        try {
            $apptDate = $appointment->appointment_date;
            $apptTime = $appointment->appointment_time;

            if (empty($apptDate)) {
                $apptDate = now()->toDateString();
            }
            if (empty($apptTime)) {
                $apptTime = '00:00:00';
            }

            if ($appointment->status === 'cancelled' && ! empty($appointment->cancellation_reason)) {
                $note = $appointment->cancellation_reason;
            } elseif (! empty($appointment->notes)) {
                $note = $appointment->notes;
            } else {
                $note = null;
            }

            AppointmentHistory::create([
                'appointment_id' => $appointment->id,
                'created_by' => $actorId,
                'status' => $appointment->status ?? 'pending',
                'note' => $note,
                'appointment_date' => $apptDate,
                'appointment_time' => $apptTime,
            ]);
        } catch (\Exception $e) {
            if (function_exists('logger')) {
                logger()->error('Failed creating appointment history on create: '.$e->getMessage(), [
                    'appointment_id' => $appointment->id,
                ]);
            } else {
                \Log::error('Failed creating appointment history on create: '.$e->getMessage(), [
                    'appointment_id' => $appointment->id,
                ]);
            }
        }
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        $changed = $appointment->getChanges();

        // fields we care about
        $fields = ['status', 'appointment_date', 'appointment_time', 'notes'];

        $intersect = array_intersect(array_keys($changed), $fields);
        if (empty($intersect)) {
            return;
        }

        if (function_exists('logger')) {
            logger()->info('Appointment updated - creating history', [
                'appointment_id' => $appointment->id,
                'changes' => $intersect,
                'all_changes' => $changed,
            ]);
        }

        // attempt to get the actor id
        $actorId = null;
        try {
            $actorId = Auth::id();
        } catch (\Exception $e) {
            $actorId = null;
        }

        // create a single history record summarizing the change
        try {
            $apptDate = $appointment->appointment_date;
            $apptTime = $appointment->appointment_time;

            // normalize fallbacks to satisfy DB non-null columns
            if (empty($apptDate)) {
                $apptDate = now()->toDateString();
            }
            if (empty($apptTime)) {
                $apptTime = '00:00:00';
            }
            if ($appointment->status === 'cancelled' && ! empty($appointment->cancellation_reason)) {
                $note = $appointment->cancellation_reason;
            } elseif (! empty($appointment->notes)) {
                $note = $appointment->notes;
            } else {
                $note = null;
            }

            AppointmentHistory::create([
                'appointment_id' => $appointment->id,
                'created_by' => $actorId,
                'status' => $appointment->status,
                'note' => $note,
                'appointment_date' => $apptDate,
                'appointment_time' => $apptTime,
            ]);
        } catch (\Exception $e) {
            // Don't let history insert block the appointment update — log for investigation
            if (function_exists('logger')) {
                logger()->error('Failed creating appointment history: '.$e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'changes' => $changed,
                ]);
            } else {
                \Log::error('Failed creating appointment history: '.$e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'changes' => $changed,
                ]);
            }
        }
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
