<?php
// app/Http/Controllers/PatientController.php
// Accessible par : Admin + Médecin + Secrétaire
namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with(['appointments', 'consultations']);

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $patients = $query->latest()->paginate(15);

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'cin'           => 'nullable|string|max:20|unique:patients,cin',
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email|max:150',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female',
            'address'       => 'nullable|string|max:255',
            'blood_type'    => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies'     => 'nullable|string|max:500',
            'medical_notes' => 'nullable|string',
        ], [
            'first_name.required'    => 'Le prénom est obligatoire.',
            'last_name.required'     => 'Le nom est obligatoire.',
            'phone.required'         => 'Le téléphone est obligatoire.',
            'date_of_birth.required' => 'La date de naissance est obligatoire.',
            'date_of_birth.before'   => 'La date de naissance doit être dans le passé.',
            'gender.required'        => 'Le genre est obligatoire.',
            'cin.unique'             => 'Ce numéro CIN est déjà utilisé.',
        ]);

        $patient = Patient::create($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', '✅ Patient ' . $patient->full_name . ' créé avec succès.');
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'appointments.doctor',
            'consultations.doctor',
            'consultations.prescription.items',
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'cin'           => 'nullable|string|max:20|unique:patients,cin,' . $patient->id,
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email|max:150',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female',
            'address'       => 'nullable|string|max:255',
            'blood_type'    => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies'     => 'nullable|string|max:500',
            'medical_notes' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient)
            ->with('success', '✅ Dossier de ' . $patient->full_name . ' mis à jour.');
    }

    public function destroy(Patient $patient)
    {
        $name = $patient->full_name;
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient ' . $name . ' supprimé.');
    }

    public function ajaxSearch(Request $request)
    {
        $patients = Patient::search($request->get('q', ''))
            ->select('id', 'first_name', 'last_name', 'cin', 'phone', 'date_of_birth')
            ->limit(10)->get()
            ->map(fn($p) => [
                'id'    => $p->id,
                'text'  => $p->full_name . ' — ' . ($p->cin ?? $p->phone),
                'age'   => $p->age,
                'phone' => $p->phone,
                'cin'   => $p->cin,
            ]);

        return response()->json(['results' => $patients]);
    }
}
