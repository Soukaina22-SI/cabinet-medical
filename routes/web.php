<?php
// ============================================================
// routes/web.php
// ============================================================
 
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\OrdonnanceController;
use Illuminate\Support\Facades\Route;
 
// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::resource('patients', PatientController::class);
Route::middleware('auth')->group(function () {
 

    // ── Patient ──────────────────────────────
    Route::get('/', function () {
    return redirect()->route('login');
});
    Route::middleware('role:patient')->group(function () {
        Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    });
 
    // ── Médecin ──────────────────────────────
    Route::middleware('role:medecin')->group(function () {
        Route::get('/medecin/dashboard', fn() => view('medecin.dashboard'))->name('medecin.dashboard');
        Route::get('/consultations/{rendezvous}/create', [ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('/consultations/{rendezvous}', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
    });
 
    // ── Secrétaire ───────────────────────────
    Route::middleware('role:secretaire,admin')->group(function () {
        Route::get('/secretaire/dashboard', fn() => view('secretaire.dashboard'))->name('secretaire.dashboard');
        Route::resource('patients', PatientController::class);
    });
 
    // ── Admin ─────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::patch('/admin/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('admin.users.toggle');
    });
 
    // ── Commun ────────────────────────────────
    Route::resource('rendezvous', RendezVousController::class);
    Route::patch('/rendezvous/{rendezvous}/confirmer', [RendezVousController::class, 'confirmer'])->name('rendezvous.confirmer');
    Route::patch('/rendezvous/{rendezvous}/annuler', [RendezVousController::class, 'annuler'])->name('rendezvous.annuler');
    Route::get('/rendezvous/disponibilites', [RendezVousController::class, 'getDisponibilites'])->name('rendezvous.disponibilites');
 
    Route::get('/ordonnances/{ordonnance}/pdf', [OrdonnanceController::class, 'exportPdf'])->name('ordonnances.pdf');
});