# Appointment Rescheduling - UI Implementation Guide

**Feature**: Appointment Rescheduling with WhatsApp Notification Trigger
**Branch**: `claude/code-quality-refactoring-01K5XSFVHSRrKhTzccotQdk4`
**Status**: ✅ Backend Complete | ⏳ Frontend UI Needed

---

## ✅ **What's Already Built (Backend)**

### Services
- ✅ `AppointmentRescheduleService.php` - Full reschedule logic
- ✅ Event system integration (`AppointmentRescheduled` event)
- ✅ WhatsApp notification trigger (placeholder ready)
- ✅ Audit logging for rescheduling
- ✅ Slot validation (prevents double-booking)

### Controller Methods
- ✅ `showRescheduleForm($id)` - Load appointment for rescheduling
- ✅ `getRescheduleSlotsAvailable()` - Get available time slots
- ✅ `reschedule()` - Process reschedule request

### Routes
- ✅ `GET /admin/appointments/{id}/reschedule` - Reschedule form
- ✅ `GET /admin/appointments/reschedule/slots` - Available slots API
- ✅ `POST /admin/appointments/reschedule` - Reschedule action

---

## 🎨 **UI Implementation Options**

### **Option 1: Modal Approach** (Recommended)
Add a "Reschedule" button on the appointments page that opens a modal.

### **Option 2: Dedicated Page**
Create a separate reschedule page with full form.

### **Option 3: Inline Edit**
Add reschedule functionality directly in the appointments list.

---

## 📝 **Option 1: Modal Implementation** (Step-by-Step)

### **Step 1: Add Reschedule Button to Appointments List**

File: `resources/views/admin/appointments.blade.php`

```blade
<!-- In your appointments table, add this button in the actions column -->
<button onclick="openRescheduleModal({{ $appointment->id }})"
        class="btn btn-sm btn-warning"
        title="Reschedule">
    <i class="fas fa-calendar-alt"></i> Reschedule
</button>
```

---

### **Step 2: Create Reschedule Modal**

Add this modal at the end of `resources/views/admin/appointments.blade.php`:

```blade
<!-- Reschedule Appointment Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-alt"></i> Reschedule Appointment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="rescheduleForm">
                    <input type="hidden" id="reschedule_appointment_id" name="appointment_id">
                    <input type="hidden" id="reschedule_doctor_id" name="doctor_id">

                    <!-- Current Appointment Info -->
                    <div class="alert alert-info">
                        <h6><strong>Current Appointment:</strong></h6>
                        <p id="current_appointment_info"></p>
                    </div>

                    <!-- New Date Selection -->
                    <div class="form-group">
                        <label for="new_appointment_date">
                            <i class="fas fa-calendar"></i> New Appointment Date *
                        </label>
                        <input type="date"
                               class="form-control"
                               id="new_appointment_date"
                               name="appointment_date"
                               min="{{ date('Y-m-d') }}"
                               required>
                        <small class="form-text text-muted">
                            Select a new date for the appointment
                        </small>
                    </div>

                    <!-- Time Slot Selection -->
                    <div class="form-group">
                        <label for="new_appointment_time">
                            <i class="fas fa-clock"></i> New Appointment Time *
                        </label>
                        <div id="time_slots_container">
                            <p class="text-muted">Please select a date first</p>
                        </div>
                    </div>

                    <!-- Reason for Rescheduling (Optional) -->
                    <div class="form-group">
                        <label for="reschedule_reason">
                            <i class="fas fa-comment"></i> Reason for Rescheduling (Optional)
                        </label>
                        <textarea class="form-control"
                                  id="reschedule_reason"
                                  name="reason"
                                  rows="3"
                                  placeholder="Why are you rescheduling this appointment?"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-warning" onclick="submitReschedule()">
                    <i class="fas fa-save"></i> Reschedule Appointment
                </button>
            </div>
        </div>
    </div>
</div>
```

---

### **Step 3: Add JavaScript Functions**

Add this JavaScript at the end of `resources/views/admin/appointments.blade.php`:

```javascript
<script>
// Open reschedule modal
function openRescheduleModal(appointmentId) {
    // Fetch appointment details
    $.ajax({
        url: `/admin/appointments/${appointmentId}/reschedule`,
        method: 'GET',
        success: function(response) {
            if (response.status === 200) {
                const appointment = response.data;

                // Populate modal
                $('#reschedule_appointment_id').val(appointment.id);
                $('#reschedule_doctor_id').val(appointment.doctor_id);

                // Show current appointment info
                const currentInfo = `
                    <strong>Patient:</strong> ${appointment.patient.first_name} ${appointment.patient.last_name}<br>
                    <strong>Doctor:</strong> Dr. ${appointment.doctor.first_name} ${appointment.doctor.last_name}<br>
                    <strong>Current Date:</strong> ${appointment.appointment_date}<br>
                    <strong>Current Time:</strong> ${appointment.appointment_time}
                `;
                $('#current_appointment_info').html(currentInfo);

                // Reset form
                $('#rescheduleForm')[0].reset();
                $('#time_slots_container').html('<p class="text-muted">Please select a date first</p>');

                // Show modal
                $('#rescheduleModal').modal('show');
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.msg || 'Failed to load appointment details';
            toastr.error(errorMsg);
        }
    });
}

// When date changes, load available slots
$('#new_appointment_date').on('change', function() {
    const appointmentId = $('#reschedule_appointment_id').val();
    const doctorId = $('#reschedule_doctor_id').val();
    const date = $(this).val();

    if (!date) return;

    // Show loading
    $('#time_slots_container').html('<p class="text-muted"><i class="fas fa-spinner fa-spin"></i> Loading available slots...</p>');

    // Fetch available slots
    $.ajax({
        url: '{{ route("admin.appointments.reschedule-slots") }}',
        method: 'GET',
        data: {
            appointment_id: appointmentId,
            doctor_id: doctorId,
            date: date
        },
        success: function(response) {
            if (response.success && response.slots && response.slots.length > 0) {
                let slotsHtml = '<div class="row">';

                response.slots.forEach(slot => {
                    slotsHtml += `
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="appointment_time"
                                       id="slot_${slot.time}"
                                       value="${slot.time}"
                                       required>
                                <label class="form-check-label" for="slot_${slot.time}">
                                    ${slot.time}
                                </label>
                            </div>
                        </div>
                    `;
                });

                slotsHtml += '</div>';
                $('#time_slots_container').html(slotsHtml);
            } else {
                $('#time_slots_container').html('<p class="text-danger">No available slots for this date</p>');
            }
        },
        error: function() {
            $('#time_slots_container').html('<p class="text-danger">Failed to load time slots</p>');
        }
    });
});

// Submit reschedule
function submitReschedule() {
    const appointmentId = $('#reschedule_appointment_id').val();
    const appointmentDate = $('#new_appointment_date').val();
    const appointmentTime = $('input[name="appointment_time"]:checked').val();

    // Validation
    if (!appointmentDate) {
        toastr.error('Please select a new date');
        return;
    }

    if (!appointmentTime) {
        toastr.error('Please select a time slot');
        return;
    }

    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rescheduling...';
    btn.disabled = true;

    // Submit reschedule request
    $.ajax({
        url: '{{ route("admin.appointments.reschedule") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            appointment_id: appointmentId,
            appointment_date: appointmentDate,
            appointment_time: appointmentTime
        },
        success: function(response) {
            if (response.status === 200) {
                toastr.success(response.msg || 'Appointment rescheduled successfully');

                // Close modal
                $('#rescheduleModal').modal('hide');

                // Reload appointments list
                location.reload(); // Or use your existing function to reload table
            }
        },
        error: function(xhr) {
            const errorMsg = xhr.responseJSON?.msg || 'Failed to reschedule appointment';
            toastr.error(errorMsg);
        },
        complete: function() {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
}

// Close modal cleanup
$('#rescheduleModal').on('hidden.bs.modal', function() {
    $('#rescheduleForm')[0].reset();
});
</script>
```

---

## 🔔 **WhatsApp Notification Trigger**

When reschedule is successful, the event `AppointmentRescheduled` is automatically fired!

**Check logs after rescheduling:**
```bash
tail -f storage/logs/laravel.log
```

**You'll see:**
```
[INFO] Appointment Rescheduled Notification
  - appointment_id: 123
  - appointment_number: APT-20251211-000123
  - patient: John Doe
  - old_date: 2025-12-15
  - old_time: 09:00:00
  - new_date: 2025-12-20
  - new_time: 14:30:00
  - rescheduled_by: Admin User
```

**To enable WhatsApp:**
1. Uncomment code in `app/Listeners/SendAppointmentNotification.php`
2. Add Twilio credentials to `.env`
3. Install: `composer require twilio/sdk`

---

## 🧪 **Testing the Reschedule Feature**

### Test Case 1: Successful Reschedule
1. Navigate to `/admin/appointments`
2. Click "Reschedule" on any pending appointment
3. Select new date
4. Select new time slot
5. Click "Reschedule Appointment"
6. ✅ Check logs for event firing
7. ✅ Verify appointment updated in database
8. ✅ Verify status changed to "pending"

### Test Case 2: Cannot Reschedule Completed
1. Try to reschedule a "completed" appointment
2. ✅ Should show error: "Cannot reschedule completed appointments"

### Test Case 3: Cannot Reschedule Past Appointments
1. Try to reschedule a past appointment
2. ✅ Should show error: "Cannot reschedule past appointments"

### Test Case 4: Slot Validation
1. Try to reschedule to a fully booked time slot
2. ✅ Should show error: "Time slot not available"

---

## 📊 **Database Verification**

After rescheduling, check:

```sql
-- View updated appointment
SELECT * FROM appointments WHERE id = 123;

-- View audit log
SELECT * FROM audit_logs
WHERE action = 'appointment_rescheduled'
ORDER BY created_at DESC
LIMIT 5;
```

---

## 🎨 **Styling Tips**

### Colors
- Use **warning/yellow** theme for reschedule actions
- Use **info/blue** for current appointment display
- Use **success/green** after successful reschedule

### Icons (Font Awesome)
- `fa-calendar-alt` - Reschedule button
- `fa-clock` - Time slots
- `fa-calendar` - Date picker
- `fa-comment` - Reason textarea

---

## 🚀 **Optional Enhancements**

### 1. Add Reschedule to Front Desk Portal
Copy the same implementation to `resources/views/frontdesk/history.blade.php`

### 2. Add Patient-Initiated Rescheduling
Allow patients to reschedule their own appointments (with approval workflow)

### 3. Add Bulk Rescheduling
For when a doctor is unavailable (already built in backend!)

### 4. Email Notifications
Add email along with WhatsApp notification

---

## 📝 **API Endpoints Summary**

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/admin/appointments/{id}/reschedule` | Get appointment details |
| GET | `/admin/appointments/reschedule/slots` | Get available time slots |
| POST | `/admin/appointments/reschedule` | Submit reschedule request |

---

## ✅ **Checklist for Implementation**

- [ ] Add "Reschedule" button to appointments list
- [ ] Create reschedule modal HTML
- [ ] Add JavaScript for modal interactions
- [ ] Add date change handler
- [ ] Add time slot selection
- [ ] Add submit handler
- [ ] Test all scenarios
- [ ] Verify event logs
- [ ] Style modal to match your theme
- [ ] Add to Front Desk portal (if needed)

---

**Once UI is implemented: Your reschedule feature with WhatsApp trigger is 100% ready!** 🎉
