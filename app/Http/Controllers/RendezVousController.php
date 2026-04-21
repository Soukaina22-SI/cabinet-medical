<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use App\Models\RendezVous;
use App\Mail\ConfirmationRDV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RendezVousController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isPatient()) {
            $rdvs = RendezVous::with(['medecin.user'])
                ->where('patient_id', $user->patient->id)
                ->orderByDesc('date_heure')
                ->paginate(10);

        } elseif ($user->isMedecin()) {
            $rdvs = RendezVous::with(['patient.user'])
                ->where('medecin_id', $user->medecin->id)
                ->whereDate('date_heure', '>=', now())
                ->orderBy('date_heure')
                ->paginate(10);

        } else {
            $rdvs = RendezVous::with(['patient.user', 'medecin.user'])
                ->orderByDesc('date_heure')
                ->paginate(15);
        }

        return view('rendezvous.index', compact('rdvs'));
    }

    public function create()
    {
        $medecins = Medecin::with('user')->get();
        return view('rendezvous.create', compact('medecins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date_heure' => 'required|date|after:now',
            'motif'      => 'required|string|max:500',
        ]);

        $conflit = RendezVous::where('medecin_id', $data['medecin_id'])
            ->where('date_heure', $data['date_heure'])
            ->whereIn('statut', ['en_attente', 'confirme'])
            ->exists();

        if ($conflit) {
            return back()
                ->withErrors(['date_heure' => "Ce créneau vient d'être pris. Veuillez en choisir un autre."])
                ->withInput();
        }

        $patient = Auth::user()->patient;

        $rdv = RendezVous::create([
            'patient_id' => $patient->id,
            'medecin_id' => $data['medecin_id'],
            'date_heure' => $data['date_heure'],
            'motif'      => $data['motif'],
            'statut'     => 'en_attente',
        ]);

        try {
            Mail::to(Auth::user()->email)->send(new ConfirmationRDV($rdv));
        } catch (\Exception $e) {
            // Ne pas bloquer si mail non configuré
        }

        return redirect()->route('rendezvous.index')
            ->with('success', 'Rendez-vous pris avec succès ! Un email de confirmation vous a été envoyé.');
    }

    public function show(RendezVous $rendezVous)
    {
        $user = Auth::user();

        $canView = $user->isAdmin()
            || $user->isSecretaire()
            || ($user->isMedecin() && $user->medecin->id === $rendezVous->medecin_id)
            || ($user->isPatient() && $user->patient->id === $rendezVous->patient_id);

        if (! $canView) {
            abort(403, 'Accès non autorisé.');
        }

        $rendezVous->load(['patient.user', 'medecin.user', 'consultation']);

        return view('rendezvous.show', compact('rendezVous'));
    }

    public function confirmer(RendezVous $rendezVous)
    {
        $rendezVous->confirmer();
        return back()->with('success', 'Rendez-vous confirmé.');
    }

    public function annuler(Request $request, RendezVous $rendezVous)
    {
        $rendezVous->annuler();
        return back()->with('success', 'Rendez-vous annulé.');
    }

    /**
     * AJAX — créneaux disponibles
     * GET /rendezvous/disponibilites?medecin_id=1&date=2025-05-10
     */
    public function getDisponibilites(Request $request)
    {
        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date'       => 'required|date|after_or_equal:today',
        ]);

        $medecin = Medecin::findOrFail($request->medecin_id);
        $slots   = $medecin->getDisponibilites($request->date);

        return response()->json($slots);
    }
}