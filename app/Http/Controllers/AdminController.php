<?php

// ============================================================
// app/Http/Controllers/AdminController.php
// ============================================================
namespace App\Http\Controllers;
 
use App\Models\User;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Services\StatistiqueService;
use Illuminate\Http\Request;
 
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
 
    public function dashboard(StatistiqueService $stats)
    {
        $data = [
            'total_patients'      => \App\Models\Patient::count(),
            'total_medecins'      => \App\Models\Medecin::count(),
            'rdvs_semaine'        => RendezVous::whereBetween('date_heure', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'consultations_mois'  => Consultation::whereMonth('date_heure', now()->month)->count(),
            'rdvs_par_mois'       => $stats->rdvsParMois(),
            'rdvs_par_statut'     => $stats->rdvsParStatut(),
            'consultations_par_medecin' => $stats->consultationsParMedecin(),
        ];
 
        return view('admin.dashboard', $data);
    }
 
    public function users()
    {
        $users = User::orderBy('role')->orderBy('nom')->paginate(20);
        return view('admin.users', compact('users'));
    }
 
    public function toggleUser(User $user)
    {
        $user->update(['actif' => !$user->actif]);
        return back()->with('success', 'Statut utilisateur modifié.');
    }
}