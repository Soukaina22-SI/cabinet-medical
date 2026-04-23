<?php
// app/Http/Controllers/ConsultationController.php
namespace App\Http\Controllers;

use App\Models\{Appointment, Consultation, Patient, Prescription, PrescriptionItem, User};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Consultation::with(['patient', 'doctor', 'prescription.items']);
        if (auth()->user()->isDoctor()) { $query->where('doctor_id', auth()->id()); }
        if ($s = $request->search) { $query->whereHas('patient', fn($q) => $q->search($s)); }
        if ($d = $request->doctor_id) { $query->where('doctor_id', $d); }
        if ($dt = $request->date) { $query->whereDate('created_at', $dt); }
        $consultations = $query->latest()->paginate(15);
        $doctors = \App\Models\User::doctors()->get();
        return view('consultations.index', compact('consultations', 'doctors'));
    }

    public function create(Appointment $appointment)
    {
        $this->authorize('create-consultation'); // only doctors

        if ($appointment->consultation) {
            return redirect()->route('consultations.show', $appointment->consultation);
        }

        return view('consultations.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'symptoms'       => 'required|string',
            'diagnosis'      => 'required|string',
            'notes'          => 'nullable|string',
            'weight'         => 'nullable|numeric',
            'height'         => 'nullable|numeric',
            'blood_pressure' => 'nullable|string',
            'temperature'    => 'nullable|numeric',
        ]);

        $consultation = Consultation::create(array_merge($validated, [
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'doctor_id'      => auth()->id(),
        ]));

        // Handle prescription items
        if ($request->has('medications')) {
            $prescription = Prescription::create([
                'consultation_id' => $consultation->id,
                'patient_id'      => $appointment->patient_id,
                'doctor_id'       => auth()->id(),
            ]);

            foreach ($request->medications as $med) {
                if (!empty($med['name'])) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medication_name' => $med['name'],
                        'dosage'          => $med['dosage'],
                        'frequency'       => $med['frequency'],
                        'duration'        => $med['duration'],
                        'instructions'    => $med['instructions'] ?? null,
                    ]);
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'));
            $path = 'prescriptions/prescription_' . $prescription->id . '.pdf';
            \Storage::put('public/' . $path, $pdf->output());
            $prescription->update(['pdf_path' => $path]);
        }

        // Mark appointment as completed
        $appointment->update(['status' => 'completed']);

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consultation enregistrée avec succès.');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load(['patient', 'doctor', 'appointment', 'prescription.items']);
        return view('consultations.show', compact('consultation'));
    }

    public function downloadPrescription(Consultation $consultation)
    {
        $prescription = $consultation->prescription;

        if (!$prescription) {
            return back()->with('error', 'Aucune ordonnance pour cette consultation.');
        }

        $pdf = Pdf::loadView('prescriptions.pdf', compact('prescription'))
            ->setPaper('a4');

        return $pdf->download('ordonnance_' . $consultation->id . '.pdf');
    }
}
