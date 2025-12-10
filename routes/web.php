<?php

use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AppointmentController;
use App\Http\Controllers\admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\admin\docktorsController;
use App\Http\Controllers\admin\PetientController;
use App\Http\Controllers\admin\specialitiesController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\doctor\CalendarController;
use App\Http\Controllers\doctor\DoctorAppointmentController;
use App\Http\Controllers\doctor\DoctorDashboarController;
use App\Http\Controllers\frontdesk\AddApoimnetController;
use App\Http\Controllers\frontdesk\DoctoreScheduleController;
use App\Http\Controllers\frontdesk\FrontDashboardController;
use App\Http\Controllers\frontdesk\HistoryController;
use App\Http\Controllers\frontdesk\PatientController;
use App\Http\Controllers\public\BookAppointmentController;

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
Route::get('/download-appointment', [BookAppointmentController::class, 'downloadPDFAppointment'])->name('download.appointment');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-details', [AdminDashboardController::class, 'getDashboardDetails'])->name('dashboard.details');

    Route::get('specialities', [specialitiesController::class, 'index'])->name('specialities');
    Route::get('specialities/{id}', [specialitiesController::class, 'view'])->name('specialities-view');
    Route::get('specialities-list', [specialitiesController::class, 'getList'])->name('specialities-list');
    Route::post('getmodel', [specialitiesController::class, 'getmodel'])->name('specialities-getmodel');
    Route::post('store', [specialitiesController::class, 'store'])->name('specialities-store');
    Route::post('specialities', [specialitiesController::class, 'toggleStatus'])->name('specialities-toggleStatus');
    Route::delete('specialities/{id}', [specialitiesController::class, 'destroy'])->name('specialities-destroy');

    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments');
    Route::get('appointmentslist', [AppointmentController::class, 'getAppointments'])->name('appointments-list');
    Route::get('appointments/add', [AppointmentController::class, 'addAppointments'])->name('add-appointment');
    Route::get('appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('get-available-slots');
    Route::post('appointments/store', [AppointmentController::class, 'storeAppointment'])->name('store-appointment');
    Route::post('appointments-modal', [AppointmentController::class, 'getAppointmentsmodal'])->name('getappointment-modal');
    Route::put('appointments/update', [AppointmentController::class, 'updateAppointment'])->name('update-appointment');
    Route::delete('appointments/delete', [AppointmentController::class, 'deleteAppointment'])->name('delete-appointment');
    Route::get('appointments/{id}', [AppointmentController::class, 'viewAppointment'])->name('view-appointment');

    Route::get('/doctors', [docktorsController::class, 'index'])->name('doctors');
    Route::get('/doctors/add', [docktorsController::class, 'create'])->name('doctors.add');
    Route::post('/doctors/add', [docktorsController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{id}', [docktorsController::class, 'show'])->name('doctors.show');
    Route::get('/doctors/{id}/edit', [docktorsController::class, 'edit'])->name('doctors.edit');
    Route::put('/doctors/{id}', [docktorsController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{id}', [docktorsController::class, 'destroy'])->name('doctors.destroy');

    Route::get('/patients', [PetientController::class, 'index'])->name('patients');
    Route::get('/patients/{id}', [PetientController::class, 'show'])->name('patient-view');
    Route::get('/patients/{id}/edit', [PetientController::class, 'edit'])->name('patient-edit');
    Route::post('/patients/{id}', [PetientController::class, 'update'])->name('patient-update');
    Route::delete('/patients/{id}', [PetientController::class, 'destroy'])->name('patient-delete');

    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/appointments', [AdminCalendarController::class, 'getDateAppointments'])->name('calendar.appointments');
    Route::get('/calendar/week', [AdminCalendarController::class, 'getWeekView'])->name('calendar.week');
    Route::get('/calendar/day', [AdminCalendarController::class, 'getDayView'])->name('calendar.day');
    Route::get('/appointments/{id}/details', [AdminCalendarController::class, 'getAppointmentDetails'])->name('appointments.details');
});

// Doctor Routes
Route::prefix('doctor')->name('doctor.')->middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('dashboard', [DoctorDashboarController::class, 'index'])->name('dashboard');

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

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('/calendar/data', [CalendarController::class, 'getCalendarData'])->name('calendar.data');
    Route::get('/calendar/schedule', [CalendarController::class, 'getWeeklySchedule'])->name('calendar.schedule');
    Route::get('/calendar/appointments', [CalendarController::class, 'getDateAppointments'])->name('calendar.appointments');
    Route::post('/calendar/schedule/update', [CalendarController::class, 'updateSchedule'])->name('calendar.schedule.update');
});

// Front Desk Routes
Route::prefix('frontdesk')->name('frontdesk.')->middleware(['auth', 'role:frontdesk'])->group(function () {
    Route::get('/dashboard', [FrontDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [FrontDashboardController::class, 'getDashboardStats'])->name('dashboard.stats');

    Route::get('/add-appointment', [AddApoimnetController::class, 'index'])->name('add-appointment');
    Route::get('/add-appointment/search-patient', [AddApoimnetController::class, 'searchPatient'])->name('add-appointment.search-patient');
    Route::get('/add-appointment/doctors', [AddApoimnetController::class, 'getDoctors'])->name('add-appointment.doctors');
    Route::get('/add-appointment/available-slots', [AddApoimnetController::class, 'getAvailableSlots'])->name('add-appointment.available-slots');
    Route::post('/add-appointment/store', [AddApoimnetController::class, 'store'])->name('add-appointment.store');

    Route::get('/doctor-schedule', [DoctoreScheduleController::class, 'index'])->name('doctor-schedule');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients');
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export/csv', [HistoryController::class, 'exportCsv'])->name('history.export');
    Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
});

// Patient Routes
Route::prefix('patient')->name('patient.')->middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/dashboard', function () {
        return view('patient.dashboard');
    })->name('dashboard');
});
