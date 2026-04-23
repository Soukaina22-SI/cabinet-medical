<?php
// app/Http/Controllers/Admin/PatientController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'cin'           => 'nullable|string|unique:patients',
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female',
            'address'       => 'nullable|string',
            'blood_type'    => 'nullable|string',
            'allergies'     => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ]);

        $patient = Patient::create($validated);

        return redirect()->route('admin.patients.show', $patient)
            ->with('success', 'Patient créé avec succès.');
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'appointments.doctor',
            'consultations.doctor',
            'consultations.prescription.items',
            'prescriptions.items',
        ]);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'cin'           => 'nullable|string|unique:patients,cin,' . $patient->id,
            'phone'         => 'required|string|max:20',
            'email'         => 'nullable|email',
            'date_of_birth' => 'required|date|before:today',
            'gender'        => 'required|in:male,female',
            'address'       => 'nullable|string',
            'blood_type'    => 'nullable|string',
            'allergies'     => 'nullable|string',
            'medical_notes' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('admin.patients.show', $patient)
            ->with('success', 'Patient mis à jour avec succès.');
    }

    public function destroy(Patient $patient)
    {
        // Seul l'admin peut supprimer un patient
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Seul un administrateur peut supprimer un patient.');
        }

        $patient->delete();
        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient supprimé.');
    }

    /**
     * AJAX search — returns JSON for live search in appointment forms.
     */
    public function ajaxSearch(\Illuminate\Http\Request $request)
    {
        $term     = $request->get('q', '');
        $patients = Patient::search($term)
            ->select('id', 'first_name', 'last_name', 'cin', 'phone', 'date_of_birth')
            ->limit(10)
            ->get()
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
