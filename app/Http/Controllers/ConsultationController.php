<?php
// ============================================================
// app/Http/Controllers/ConsultationController.php
// ============================================================
namespace App\Http\Controllers;
 
use App\Models\Consultation;
use App\Models\RendezVous;
use App\Models\Ordonnance;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class ConsultationController extends Controller
{
    public function create(RendezVous $rendezVous)
    {
        $this->authorize('manage', $rendezVous);
        return view('consultations.create', compact('rendezVous'));
    }
 
    public function store(Request $request, RendezVous $rendezVous)
    {
        $data = $request->validate([
            'diagnostic'   => 'required|string',
            'compte_rendu' => 'required|string',
            'prix'         => 'required|numeric|min:0',
            'medicaments'  => 'nullable|array',
            'medicaments.*.nom'      => 'required_with:medicaments|string',
            'medicaments.*.dosage'   => 'required_with:medicaments|string',
            'medicaments.*.duree'    => 'required_with:medicaments|string',
            'medicaments.*.posologie'=> 'required_with:medicaments|string',
            'instructions' => 'nullable|string',
        ]);
 
        $consultation = Consultation::create([
            'rendezvous_id' => $rendezVous->id,
            'medecin_id'    => $rendezVous->medecin_id,
            'patient_id'    => $rendezVous->patient_id,
            'date_heure'    => now(),
            'diagnostic'    => $data['diagnostic'],
            'compte_rendu'  => $data['compte_rendu'],
            'prix'          => $data['prix'],
        ]);
 
        $rendezVous->terminer();
 
        // Créer ordonnance si médicaments fournis
        if (!empty($data['medicaments'])) {
            $ordonnance = Ordonnance::create([
                'consultation_id' => $consultation->id,
                'medicaments'     => $data['medicaments'],
                'instructions'    => $data['instructions'] ?? null,
            ]);
        }
 
        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consultation enregistrée avec succès.');
    }
 
    public function show(Consultation $consultation)
    {
        $consultation->load(['patient.user', 'medecin.user', 'ordonnance']);
        return view('consultations.show', compact('consultation'));
    }
}
 