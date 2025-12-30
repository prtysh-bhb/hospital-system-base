<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\DoctorsController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Doctor\CalendarController;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\SpecialtiesController;
use App\Http\Controllers\Frontdesk\HistoryController;
use App\Http\Controllers\Patient\DashboardController;
use App\Http\Controllers\Doctor\DoctorLeaveController;
use App\Http\Controllers\Patient\PatientAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Public\BookAppointmentController;
use App\Http\Controllers\Doctor\DoctorAppointmentController;
use App\Http\Controllers\Frontdesk\AddAppointmentController;
use App\Http\Controllers\Frontdesk\DoctorScheduleController;
use App\Http\Controllers\Frontdesk\FrontDashboardController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\Frontdesk\PatientController as FrontPatientController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home/Welcome Page - Role Selection
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('redirect.to.dashboard');
    Route::post('/login', 'login')->name('login.post');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/check-auth', 'checkAuth')->name('check.auth');

    // Forgot Password
    Route::get('/forgot-password', 'forgot_password')->name('forgot-password');
    Route::post('/forgot-password', 'sendResetLink')->name('forgot-password.send');

    // Reset Password
    Route::get('/reset-password/{token}', 'reset_password_form')->name('password.reset');
    Route::post('/reset-password', 'reset_password')->name('password.update');
});

// Public Booking Route (Single route with ?step=1,2,3,4 parameter)
Route::get('booking', [BookAppointmentController::class, 'index'])->name('booking');
Route::post('booking', [BookAppointmentController::class, 'store'])->name('booking.store');
Route::get('/get-time-slots', [BookAppointmentController::class, 'getSlots'])->name('get.time.slots');
Route::get('/get-doctor-leave-dates', [BookAppointmentController::class, 'getDoctorLeaveDates'])->name('get.doctor.leave.dates');
Route::get('/check-doctor-leave', [BookAppointmentController::class, 'checkDoctorLeave'])->name('check.doctor.leave');
Route::get('/download-appointment', [BookAppointmentController::class, 'downloadPDFAppointment'])->name('download.appointment');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-details', [AdminDashboardController::class, 'getDashboardDetails'])->name('dashboard.details');

    Route::get('specialities', [SpecialtiesController::class, 'index'])->name('specialities');
    Route::get('specialities/{id}', [SpecialtiesController::class, 'view'])->name('specialities-view');
    Route::get('specialities-list', [SpecialtiesController::class, 'getList'])->name('specialities-list');
    Route::post('getmodel', [SpecialtiesController::class, 'getmodel'])->name('specialities-getmodel');
    Route::post('store', [SpecialtiesController::class, 'store'])->name('specialities-store');
    Route::post('specialities', [SpecialtiesController::class, 'toggleStatus'])->name('specialities-toggleStatus');
    Route::delete('specialities/{id}', [SpecialtiesController::class, 'destroy'])->name('specialities-destroy');

    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('appointmentslist', [AppointmentController::class, 'getAppointments'])->name('appointments-list');
    Route::get('appointments/add', [AppointmentController::class, 'addAppointments'])->name('add-appointment');
    Route::get('appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('get-available-slots');
    Route::post('appointments/store', [AppointmentController::class, 'storeAppointment'])->name('store-appointment');
    Route::post('appointments-modal', [AppointmentController::class, 'getAppointmentsmodal'])->name('getappointment-modal');
    Route::put('appointments/update', [AppointmentController::class, 'updateAppointment'])->name('update-appointment');
    Route::delete('appointments/delete', [AppointmentController::class, 'deleteAppointment'])->name('delete-appointment');
    Route::get('appointments/{id}', [AppointmentController::class, 'viewAppointment'])->name('view-appointment');

    Route::get('/doctors', [DoctorsController::class, 'index'])->name('doctors');
    Route::get('/doctors/add', [DoctorsController::class, 'create'])->name('doctors.add');
    Route::post('/doctors/add', [DoctorsController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{id}', [DoctorsController::class, 'show'])->name('doctors.show');
    Route::get('/doctors/{id}/edit', [DoctorsController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{id}', [DoctorsController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{id}', [DoctorsController::class, 'destroy'])->name('doctors.destroy');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patient-view');
    Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patient-edit');
    Route::post('/patients/{id}', [PatientController::class, 'update'])->name('patient-update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patient-delete');

    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/month', [AdminCalendarController::class, 'getMonthView'])->name('calendar.month');
    Route::get('/calendar/appointments', [AdminCalendarController::class, 'getDateAppointments'])->name('calendar.appointments');
    Route::get('/calendar/week', [AdminCalendarController::class, 'getWeekView'])->name('calendar.week');
    Route::get('/calendar/day', [AdminCalendarController::class, 'getDayView'])->name('calendar.day');
    Route::get('/appointments/{id}/details', [AdminCalendarController::class, 'getAppointmentDetails'])->name('appointments.details');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings/update', [SettingController::class, 'update'])->name('settings.update');

    // Admin Leave Management
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves');
});

// Doctor Routes
Route::prefix('doctor')->name('doctor.')->middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    Route::get('appointments', [DoctorAppointmentController::class, 'index'])->name('appointments');
    Route::get('appointment-details/{id}', [DoctorAppointmentController::class, 'doctorAppointmentDetails'])->name('appointment-details');
    Route::get('appointment-data', [DoctorAppointmentController::class, 'doctorAppointmentData'])->name('appointments.data');
    Route::get('appointments/{id}/details-json', [DoctorAppointmentController::class, 'getAppointmentDetailsJson'])->name('appointments.details.json');
    Route::post('appointments/{id}/complete', [DoctorAppointmentController::class, 'completeAppointment'])->name('appointments.complete');
    Route::post('appointments/{id}/notes', [DoctorAppointmentController::class, 'saveConsultationNotes'])->name('appointments.notes');
    Route::post('appointments/{id}/vital-signs', [DoctorAppointmentController::class, 'saveVitalSigns'])->name('appointments.vitals');
    Route::post('appointments/{id}/prescription', [DoctorAppointmentController::class, 'savePrescription'])->name('appointments.prescription');
    Route::post('appointments/{id}/follow-up', [DoctorAppointmentController::class, 'scheduleFollowUp'])->name('appointments.followup');
    Route::get('appointments/available-slots', [DoctorAppointmentController::class, 'getAvailableSlots'])->name('appointments.available-slots');
    Route::post('appointments/{id}/reschedule', [DoctorAppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('appointments/{id}/cancel', [DoctorAppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('appointments/{id}/update-status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.update-status');

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/data', [CalendarController::class, 'getCalendarData'])->name('calendar.data');
    Route::get('/calendar/schedule', [CalendarController::class, 'getWeeklySchedule'])->name('calendar.schedule');
    Route::get('/calendar/appointments', [CalendarController::class, 'getDateAppointments'])->name('calendar.appointments');
    Route::post('/calendar/schedule/update', [CalendarController::class, 'updateSchedule'])->name('calendar.schedule.update');

    Route::get('/leaves', [DoctorLeaveController::class, 'index'])->name('leaves');
    Route::post('/leaves', [DoctorLeaveController::class, 'store'])->name('leaves.store');
});

// Front Desk Routes
Route::prefix('frontdesk')->name('frontdesk.')->middleware(['auth', 'role:frontdesk'])->group(function () {
    Route::get('/dashboard', [FrontDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [FrontDashboardController::class, 'getDashboardStats'])->name('dashboard.stats');

    Route::get('/add-appointment', [AddAppointmentController::class, 'index'])->name('add-appointment');
    Route::get('/add-appointment/search-patient', [AddAppointmentController::class, 'searchPatient'])->name('add-appointment.search-patient');
    Route::get('/add-appointment/doctors', [AddAppointmentController::class, 'getDoctors'])->name('add-appointment.doctors');
    Route::get('/add-appointment/available-slots', [AddAppointmentController::class, 'getAvailableSlots'])->name('add-appointment.available-slots');
    Route::post('/add-appointment/store', [AddAppointmentController::class, 'store'])->name('add-appointment.store');
    Route::get('/doctor-schedule', [DoctorScheduleController::class, 'index'])->name('doctor-schedule');

    Route::get('/patients', [FrontPatientController::class, 'index'])->name('patients');
    Route::get('/patients/{id}', [FrontPatientController::class, 'show'])->name('patients.show');
    Route::put('/patients/{id}', [FrontPatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [FrontPatientController::class, 'destroy'])->name('patients.destroy');

    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export/csv', [HistoryController::class, 'exportCsv'])->name('history.export');
    Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
});

// Patient Routes
// Patient Authentication (separate from staff login)
Route::prefix('patient')->name('patient.')->group(function () {
    Route::get('/login', [PatientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [PatientAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [PatientAuthController::class, 'logout'])->name('logout');
});

// Patient Protected Routes
Route::prefix('patient')->name('patient.')->middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/cancel-appointment', [DashboardController::class, 'cancelAppointment'])->name('cancel-appointment');
    Route::post('/reschedule-appointment', [DashboardController::class, 'rescheduleAppointment'])->name('reschedule-appointment');
    Route::get('/available-time-slots', [DashboardController::class, 'getAvailableTimeSlots'])->name('available-time-slots');
    Route::get('/medical-history', [DashboardController::class, 'getMedicalHistory'])->name('medical-history');
    Route::get('/prescription/{id}/download', [DashboardController::class, 'downloadPrescription'])->name('prescription.download');
    Route::post('appointments/store', [DashboardController::class, 'storeAppointment'])->name('store.appointment');
});