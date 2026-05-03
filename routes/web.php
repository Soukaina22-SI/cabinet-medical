<?php
// routes/web.php — SOLUTION FINALE: pas de nested groups
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\Admin\{DashboardController, PatientController, UserController, StatisticsController};
use App\Http\Controllers\Doctor\ScheduleController;
use App\Http\Controllers\{AppointmentController, ConsultationController, ProfileController};

// ── AUTH ──────────────────────────────────────────────────────
Route::get('/login',     [LoginController::class,    'showLogin'])->name('login');
Route::post('/login',    [LoginController::class,    'login']);
Route::post('/logout',   [LoginController::class,    'logout'])->name('logout');
Route::get('/register',  [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ── ROOT ──────────────────────────────────────────────────────
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return redirect()->route(match(auth()->user()->role) {
        'admin'      => 'admin.dashboard',
        'medecin'    => 'doctor.dashboard',
        'secretaire' => 'secretary.dashboard',
        'patient'    => 'patient.dashboard',
        default      => 'login',
    });
});

// ── PROFILE ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',          [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ── ADMIN ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',  [DashboardController::class,  'index'])->name('dashboard');
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
});

// ── DOCTOR ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:medecin'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', fn() => view('doctor.dashboard'))->name('dashboard');
    Route::get('/schedule',  [ScheduleController::class, 'index'])->name('schedule');
    Route::put('/schedule',  [ScheduleController::class, 'update'])->name('schedule.update');
});

// ── SECRETARY ─────────────────────────────────────────────────
Route::middleware(['auth', 'role:secretaire'])->prefix('secretary')->name('secretary.')->group(function () {
    Route::get('/dashboard', fn() => view('secretary.dashboard'))->name('dashboard');
});

// ── PATIENT ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', fn() => view('patient.dashboard'))->name('dashboard');
});

// ── PATIENTS CRUD (Admin + Médecin + Secrétaire) ──────────────
Route::middleware(['auth', 'role:admin,medecin,secretaire'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('patients',                [PatientController::class, 'index'])->name('patients.index');
    Route::get('patients/create',         [PatientController::class, 'create'])->name('patients.create');
    Route::post('patients',               [PatientController::class, 'store'])->name('patients.store');
    Route::get('patients/{patient}',      [PatientController::class, 'show'])->name('patients.show');
    Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('patients/{patient}',      [PatientController::class, 'update'])->name('patients.update');
});

// ── APPOINTMENTS + CONSULTATIONS ──────────────────────────────
// RÈGLE CRITIQUE: routes spécifiques AVANT routes avec {paramètre}

// 1️⃣ Sans paramètre {appointment} — Staff + Patient
Route::middleware(['auth', 'role:admin,medecin,secretaire,patient'])->group(function () {
    Route::get('appointments',       [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments-slots', [AppointmentController::class, 'availableSlots'])->name('appointments.available-slots');
    Route::post('appointments',      [AppointmentController::class, 'store'])->name('appointments.store');
});

// 2️⃣ /appointments/create — Staff + Patient
Route::middleware(['auth', 'role:admin,medecin,secretaire,patient'])
    ->get('appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');

// 3️⃣ Consultations (pas de {appointment}) — Staff
Route::middleware(['auth', 'role:admin,medecin,secretaire'])->group(function () {
    Route::get('consultations',                                          [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('patients-search',                                        [PatientController::class,      'ajaxSearch'])->name('patients.ajax-search');
    Route::get('consultations/{consultation}',                           [ConsultationController::class, 'show'])->name('consultations.show');
    Route::get('consultations/{consultation}/prescription/pdf',          [ConsultationController::class, 'downloadPrescription'])->name('prescriptions.download');
});

// 4️⃣ Routes avec {appointment}/sous-chemin — AVANT {appointment} seul
Route::middleware(['auth', 'role:admin,medecin,secretaire'])->group(function () {
    // !! consultation/create doit être ICI avant appointments/{appointment} !!
    Route::get('appointments/{appointment}/consultation/create',         [ConsultationController::class, 'create'])->name('consultations.create');
    Route::post('appointments/{appointment}/consultation',               [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('appointments/{appointment}/edit',                        [AppointmentController::class,  'edit'])->name('appointments.edit');
    Route::put('appointments/{appointment}',                             [AppointmentController::class,  'update'])->name('appointments.update');
    Route::delete('appointments/{appointment}',                          [AppointmentController::class,  'destroy'])->name('appointments.destroy');
    Route::patch('appointments/{appointment}/status',                    [AppointmentController::class,  'updateStatus'])->name('appointments.update-status');
    Route::post('appointments/{appointment}/send-reminder',              [AppointmentController::class,  'sendReminder'])->name('appointments.send-reminder');
});

// 5️⃣ appointments/{appointment} show — EN DERNIER (Staff + Patient)
Route::middleware(['auth', 'role:admin,medecin,secretaire,patient'])
    ->get('appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');