<?php
// app/Http/Controllers/ConsultationController.php
namespace App\Http\Controllers;

use App\Models\{Appointment, Consultation, Patient, Prescription, PrescriptionItem, User};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with(['patient', 'doctor', 'prescription.items']);

        // الطبيب يرى فقط consultations الخاصة به
        if (auth()->user()->isDoctor()) {
            $query->where('doctor_id', auth()->id());
        }

        // فلتر البحث بالمريض — لكن لا يتجاوز فلتر الطبيب
        if ($s = $request->search) {
            $query->whereHas('patient', fn($q) => $q->search($s));
        }

        // فلتر الطبيب — فقط للأدمين والسكرتير (الطبيب مقيّد أصلاً)
        if ($d = $request->doctor_id && !auth()->user()->isDoctor()) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($dt = $request->date) {
            $query->whereDate('created_at', $dt);
        }

        $consultations = $query->latest()->paginate(15);

        // الطبيب لا يحتاج قائمة الأطباء (فلتر مخفي عنه)
        $doctors = auth()->user()->isDoctor()
            ? collect()
            : User::doctors()->get();

        return view('consultations.index', compact('consultations', 'doctors'));
    }

    public function create(Appointment $appointment)
    {
        // ✅ تحقق مباشر بدل authorize() الذي يحتاج Policy
        if (!auth()->user()->isDoctor()) {
            abort(403, 'Seul un médecin peut créer une consultation.');
        }

        if ($appointment->consultation) {
            return redirect()->route('consultations.show', $appointment->consultation);
        }

        return view('consultations.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        // ✅ تحقق مباشر بدل authorize()
        if (!auth()->user()->isDoctor()) {
            abort(403, 'Seul un médecin peut enregistrer une consultation.');
        }

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
            $pdf  = Pdf::loadView('prescriptions.pdf', compact('prescription'));
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