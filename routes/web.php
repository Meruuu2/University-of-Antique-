<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\FormSubmissionController as AdminFormSubmissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\College\DashboardController as CollegeDashboardController;
use App\Http\Controllers\College\FacilityController as CollegeFacilityController;
use App\Http\Controllers\College\FormController as CollegeFormController;
use App\Http\Controllers\Org\DashboardController as OrgDashboardController;
use App\Http\Controllers\Org\FormController as OrgFormController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GsuFormController;
use App\Http\Controllers\PublicCalendarController;

// Authentication routes (simple web login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Notifications (for all authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
});

// home route for viewer/general: public read-only calendar
Route::get('/', [PublicCalendarController::class, 'index'])
    ->name('home');

// Simple health check (for debugging)
Route::get('/healthz', function () {
    return response()->json([
        'status' => 'ok',
        'time'   => now()->toDateTimeString(),
    ]);
});

// Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Facilities
    Route::get('/admin/facilities', [AdminFacilityController::class, 'index'])->name('admin.facilities.index');
    Route::get('/admin/facilities/create', [AdminFacilityController::class, 'create'])->name('admin.facilities.create');
    Route::post('/admin/facilities', [AdminFacilityController::class, 'store'])->name('admin.facilities.store');
    Route::get('/admin/facilities/{facility}/edit', [AdminFacilityController::class, 'edit'])->name('admin.facilities.edit');
    Route::put('/admin/facilities/{facility}', [AdminFacilityController::class, 'update'])->name('admin.facilities.update');
    Route::delete('/admin/facilities/{facility}', [AdminFacilityController::class, 'destroy'])->name('admin.facilities.destroy');

    // GSU Forms (accessible by admin/GSU staff)
    Route::get('/forms/facilities', [GsuFormController::class, 'showFacilities'])->name('forms.facilities.show');
    Route::post('/forms/facilities/download', [GsuFormController::class, 'downloadFacilities'])->name('forms.facilities.download');
    Route::get('/forms/repair', [GsuFormController::class, 'showRepair'])->name('forms.repair.show');
    Route::post('/forms/repair/download', [GsuFormController::class, 'downloadRepair'])->name('forms.repair.download');

    // Facilities form submissions (GSU review)
    Route::get('/admin/forms/facilities', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'index'])
        ->name('admin.forms.facilities.index');
    Route::get('/admin/forms/facilities/{submission}', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'show'])
        ->name('admin.forms.facilities.show');
    Route::post('/admin/forms/facilities/{submission}/approve', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'approve'])
        ->name('admin.forms.facilities.approve');
    Route::post('/admin/forms/facilities/{submission}/disapprove', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'disapprove'])
        ->name('admin.forms.facilities.disapprove');
    Route::post('/admin/forms/facilities/{submission}/set-booking', [\App\Http\Controllers\Admin\FormSubmissionController::class, 'setBooking'])
        ->name('admin.forms.facilities.set-booking');

    // Generate PDF for approved facilities requests
    Route::get(
        '/admin/forms/facilities/{submission}/pdf',
        [\App\Http\Controllers\Admin\FacilitiesFormPdfController::class, 'generate']
    )->name('admin.forms.facilities.pdf');

    // User management
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');

    // Direct booking (GSU high-priority)
    Route::get('/admin/bookings/create-direct', [\App\Http\Controllers\Admin\BookingController::class, 'createDirect'])
        ->name('admin.bookings.create-direct');
    Route::post('/admin/bookings/store-direct', [\App\Http\Controllers\Admin\BookingController::class, 'storeDirect'])
        ->name('admin.bookings.store-direct');


    // Booking management (GSU can override & reschedule)
    Route::get('/admin/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])
        ->name('admin.bookings.index');
    Route::get('/admin/bookings/{booking}/edit', [\App\Http\Controllers\Admin\BookingController::class, 'edit'])
        ->name('admin.bookings.edit');
    Route::post('/admin/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'update'])
        ->name('admin.bookings.update');
    Route::post('/admin/bookings/{booking}/cancel', [\App\Http\Controllers\Admin\BookingController::class, 'cancel'])
        ->name('admin.bookings.cancel');
    Route::get('/admin/calendar', [\App\Http\Controllers\Admin\BookingController::class, 'calendar'])
        ->name('admin.calendar');
    Route::get('/admin/overview', [\App\Http\Controllers\Admin\BookingController::class, 'overview'])
        ->name('admin.overview');
});

// College Staff
Route::middleware(['auth', 'role:college_staff'])->group(function () {
    Route::get('/college/dashboard', [CollegeDashboardController::class, 'index'])->name('college.dashboard');

    // Facilities
    Route::get('/college/facilities', [CollegeFacilityController::class, 'index'])->name('college.facilities.index');
    Route::get('/college/facilities/create', [CollegeFacilityController::class, 'create'])->name('college.facilities.create');
    Route::post('/college/facilities', [CollegeFacilityController::class, 'store'])->name('college.facilities.store');
    Route::get('/college/facilities/{facility}/edit', [CollegeFacilityController::class, 'edit'])->name('college.facilities.edit');
    Route::put('/college/facilities/{facility}', [CollegeFacilityController::class, 'update'])->name('college.facilities.update');
    Route::delete('/college/facilities/{facility}', [CollegeFacilityController::class, 'destroy'])->name('college.facilities.destroy');

    // Bookings list (existing page, untouched)
    Route::get('/college/bookings', function () {
        return view('college.bookings.index');
    })->name('college.bookings.index');

    // College booking calendar (read-only)
    Route::get('/college/calendar', [\App\Http\Controllers\College\BookingController::class, 'calendar'])
        ->name('college.calendar');

    // Facilities Utilization Form (College → GSU)
    Route::get('/college/requests/facilities', [\App\Http\Controllers\College\FormController::class, 'createFacilities'])
        ->name('college.requests.facilities.create');
    Route::post('/college/requests/facilities', [\App\Http\Controllers\College\FormController::class, 'storeFacilities'])
        ->name('college.requests.facilities.store');

    // My Facilities Requests (College)
    Route::get('/college/requests', [\App\Http\Controllers\College\FormController::class, 'indexFacilities'])
        ->name('college.requests.index');

    Route::get(
        '/college/requests/facilities/{submission}',
        [\App\Http\Controllers\College\FormController::class, 'showFacilities']
    )->name('college.requests.facilities.show');
});

// Org Staff
Route::middleware(['auth', 'role:org_staff'])->group(function () {
    Route::get('/org/dashboard', [OrgDashboardController::class, 'index'])->name('org.dashboard');

    // Booking calendar (read-only for this org staff)
    Route::get('/org/bookings', [\App\Http\Controllers\Org\BookingController::class, 'calendar'])
        ->name('org.bookings.index');

    // Facilities Utilization Form (Org → GSU)
    Route::get('/org/requests/facilities', [OrgFormController::class, 'createFacilities'])
        ->name('org.requests.facilities.create');
    Route::post('/org/requests/facilities', [OrgFormController::class, 'storeFacilities'])
        ->name('org.requests.facilities.store');
    Route::get('/org/requests/facilities/index', [OrgFormController::class, 'indexFacilities'])
        ->name('org.requests.facilities.index');
});

// TEMP: create test users for login (remove after you test)
use App\Models\User;
use Illuminate\Support\Facades\Hash;

Route::get('/make-admin', function () {
    $user = User::updateOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin User',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]
    );

    return $user;
});

Route::get('/make-college-staff', function () {
    $user = User::updateOrCreate(
        ['email' => 'college@example.com'],
        [
            'name' => 'College Staff User',
            'password' => Hash::make('password123'),
            'role' => 'college_staff',
            'college_name' => 'College of Engineering',
        ]
    );

    return $user;
});

Route::get('/make-org-staff', function () {
    $user = User::updateOrCreate(
        ['email' => 'org@example.com'],
        [
            'name' => 'Organization Staff User',
            'password' => Hash::make('password123'),
            'role' => 'org_staff',
            'organization_name' => 'Student Council',
        ]
    );

    return $user;
});

Route::get('/make-user', function () {
    $user = User::updateOrCreate(
        ['email' => 'user@example.com'],
        [
            'name' => 'Regular User',
            'password' => Hash::make('password123'),
            'role' => 'viewer',
        ]
    );

    return $user;
});


