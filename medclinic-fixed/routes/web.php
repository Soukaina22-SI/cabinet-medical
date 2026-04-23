<?php
// routes/web.php — VERSION FINALE COMPLÈTE
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\{DashboardController, PatientController as AdminPatientController, UserController, StatisticsController};
use App\Http\Controllers\Doctor\ScheduleController;
use App\Http\Controllers\{AppointmentController, ConsultationController, ProfileController};

// Auth
Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // Profile (all roles)
    Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard',  [DashboardController::class,  'index'])->name('dashboard');
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Patients — Admin + Médecin + Secrétaire
    // (destroy réservé à l'admin uniquement via vérification dans le contrôleur)
    Route::middleware('role:admin,medecin,secretaire')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('patients', AdminPatientController::class);
    });

    // Doctor
    Route::middleware('role:medecin')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', fn() => view('doctor.dashboard'))->name('dashboard');
        Route::get('/schedule',  [ScheduleController::class, 'index'])->name('schedule');
        Route::put('/schedule',  [ScheduleController::class, 'update'])->name('schedule.update');
    });

    // Secretary
    Route::middleware('role:secretaire')->prefix('secretary')->name('secretary.')->group(function () {
        Route::get('/dashboard', fn() => view('secretary.dashboard'))->name('dashboard');
    });

    // Patient
    Route::middleware('role:patient')->prefix('patient')->name('patient.')->group(function () {
        Route::get('/dashboard', fn() => view('patient.dashboard'))->name('dashboard');
    });

    // Shared: Admin + Médecin + Secrétaire
    Route::middleware('role:admin,medecin,secretaire')->group(function () {
        Route::resource('appointments', AppointmentController::class);
        Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
        Route::get('appointments-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');

        Route::get('consultations',     [ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('appointments/{appointment}/consultation/create', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('appointments/{appointment}/consultation', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
        Route::get('consultations/{consultation}/prescription/pdf', [ConsultationController::class, 'downloadPrescription'])->name('prescriptions.download');

        Route::get('patients-search', [AdminPatientController::class, 'ajaxSearch'])->name('patients.ajax-search');
    });
});

// Root redirect
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return redirect()->route(match(auth()->user()->role) {
        'admin'      => 'admin.dashboard',
        'medecin'    => 'doctor.dashboard',
        'secretaire' => 'secretary.dashboard',
        default      => 'login',
    });
});
