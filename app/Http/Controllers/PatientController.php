<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function dashboard()
    {
        $patient = auth()->user()->patient;

        $rdvs = $patient->rendezvous()
            ->with('medecin.user')
            ->orderByDesc('date_heure')
            ->take(5)
            ->get();

        $consultations = $patient->consultations()
            ->with(['medecin.user', 'ordonnance'])
            ->orderByDesc('date_heure')
            ->take(5)
            ->get();

        return view('patient.dashboard', compact('patient', 'rdvs', 'consultations'));
    }

    public function index(Request $request)
    {
        $query = Patient::with('user');

        if ($search = $request->search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $patients = $query->paginate(15)->withQueryString();
        return view('patients.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'user',
            'dossierMedical',
            'consultations.medecin.user',
            'consultations.ordonnance',
        ]);
        return view('patients.show', compact('patient'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        // Pour la secrétaire qui crée un patient manuellement
        $data = $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => 'required|email|unique:users',
            'telephone'      => 'nullable|string|max:20',
            'date_naissance' => 'required|date',
            'sexe'           => 'required|in:M,F',
            'cin'            => 'nullable|string|max:20',
        ]);

        $user = \App\Models\User::create([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password'  => \Illuminate\Support\Facades\Hash::make('password'),
            'role'      => 'patient',
        ]);

        $patient = $user->patient()->create([
            'date_naissance' => $data['date_naissance'],
            'sexe'           => $data['sexe'],
            'cin'            => $data['cin'] ?? null,
        ]);

        $patient->dossierMedical()->create([]);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient créé avec succès.');
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string',
            'mutuelle'  => 'nullable|string',
        ]);

        $patient->user->update([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'telephone' => $data['telephone'] ?? null,
        ]);

        $patient->update([
            'adresse'  => $data['adresse'] ?? null,
            'mutuelle' => $data['mutuelle'] ?? null,
        ]);

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Fiche patient mise à jour.');
    }

    public function destroy(Patient $patient)
    {
        $patient->user->delete(); // cascade supprime le patient
        return redirect()->route('patients.index')
            ->with('success', 'Patient supprimé.');
    }
}