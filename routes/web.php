<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - SNSU Faculty Consultation & Appointment System
|--------------------------------------------------------------------------
*/

// 1. Landing Page (Welcome / Role Selection)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 2. Dynamic Login Routes
Route::get('/login/{role?}', [AuthController::class, 'showLogin'])->name('login');

// 3. Login POST Handler
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// 4. Registration Routes
Route::get('/register/{role?}', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register/{role?}', [AuthController::class, 'register'])->name('register.post');

// 5. Faculty Routes (Protected with auth and faculty role middleware)
Route::middleware(['auth', 'role:faculty'])->group(function () {
    Route::get('/faculty/dashboard', [FacultyController::class, 'dashboard'])->name('faculty.dashboard');
    
    Route::get('/faculty/profile-settings', [FacultyController::class, 'profileSettings'])->name('faculty.profile-settings');
    Route::get('/faculty/profile_settings', function () { return redirect()->route('faculty.profile-settings'); });
    Route::get('/faculty/profile', function () { return redirect()->route('faculty.profile-settings'); });
    Route::post('/faculty/profile/update', [ProfileController::class, 'facultyUpdate'])->name('faculty.profile.update');

    Route::get('/faculty/my-schedule', [FacultyController::class, 'mySchedule'])->name('faculty.my-schedule');
    Route::get('/faculty/my_schedule', function () { return redirect()->route('faculty.my-schedule'); });
    Route::post('/faculty/my-schedule/save', [FacultyController::class, 'saveSchedule'])->name('faculty.my-schedule.save');

    Route::get('/faculty/consultation-requests', [AppointmentController::class, 'facultyConsultationRequests'])->name('faculty.consultation-requests');
    Route::get('/faculty/consultation_requests', function () { return redirect()->route('faculty.consultation-requests'); });
    Route::post('/faculty/appointments/{id}/status', [AppointmentController::class, 'updateStatusByFaculty'])->name('faculty.appointments.update-status');

    Route::get('/faculty/student-management', [FacultyController::class, 'studentManagement'])->name('faculty.student-management');
    Route::get('/faculty/student_management', function () { return redirect()->route('faculty.student-management'); });

    Route::get('/faculty/announcements', [AnnouncementController::class, 'facultyIndex'])->name('faculty.announcements');
    Route::post('/faculty/announcements/store', [AnnouncementController::class, 'facultyStore'])->name('faculty.announcements.store');
    Route::post('/faculty/announcements/quick-post', [AnnouncementController::class, 'facultyQuickPost'])->name('faculty.announcements.quick-post');
    Route::post('/faculty/announcements/{id}/delete', [AnnouncementController::class, 'facultyDelete'])->name('faculty.announcements.delete');

    Route::get('/faculty/support', [SupportTicketController::class, 'facultyIndex'])->name('faculty.support');
    Route::post('/faculty/support/submit', [SupportTicketController::class, 'facultySubmit'])->name('faculty.support.submit');
});

// 6. Admin Dashboard & Sub-module Routes (Protected with auth and admin role middleware)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/user-management', [AdminController::class, 'userManagement'])->name('user-management');
    Route::post('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');

    Route::get('/faculty-directory', [AdminController::class, 'facultyDirectory'])->name('faculty-directory');
    Route::get('/student-roster', [AdminController::class, 'studentRoster'])->name('student-roster');

    Route::get('/all-appointments', [AppointmentController::class, 'adminAllAppointments'])->name('all-appointments');
    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatusByAdmin'])->name('appointments.update-status');

    Route::get('/support-tickets', [SupportTicketController::class, 'adminIndex'])->name('support-tickets');
    Route::post('/support-tickets/{id}/status', [SupportTicketController::class, 'adminUpdateStatus'])->name('support-tickets.update-status');
});

// 7. Student Module Routes (Protected with auth and student role middleware)
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    Route::get('/faculty-availability', [StudentController::class, 'facultyAvailability'])->name('faculty-availability');
    Route::get('/faculty_availability', function () { return redirect()->route('student.faculty-availability'); });

    Route::get('/book-an-appointment', [AppointmentController::class, 'create'])->name('book-appointment');
    Route::get('/book_an_appointment', function () { return redirect()->route('student.book-appointment'); });
    Route::post('/book-an-appointment', [AppointmentController::class, 'store'])->name('book-appointment.post');

    Route::get('/my-appointments', [AppointmentController::class, 'myAppointments'])->name('my-appointments');
    Route::get('/my_appointments', function () { return redirect()->route('student.my-appointments'); });

    Route::get('/consultation-logs', [AppointmentController::class, 'consultationLogs'])->name('consultation-logs');
    Route::get('/consultation_logs', function () { return redirect()->route('student.consultation-logs'); });

    Route::get('/announcements', [AnnouncementController::class, 'studentIndex'])->name('announcements');

    Route::get('/support', [SupportTicketController::class, 'studentIndex'])->name('support');
    Route::post('/support/submit', [SupportTicketController::class, 'studentSubmit'])->name('support.submit');

    Route::post('/profile/update', [ProfileController::class, 'studentUpdate'])->name('profile.update');
});

// 8. Logout & Auth Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/superadmin/login', function () {
    return view('auth.superadmin-login');
})->name('superadmin.login');

