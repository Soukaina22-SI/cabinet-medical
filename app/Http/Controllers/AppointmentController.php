<?php
// app/Http/Controllers/AppointmentController.php
namespace App\Http\Controllers;

use App\Models\{Appointment, DoctorSchedule, Patient, User};
use App\Mail\{AppointmentConfirmed, AppointmentReminder};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    // ── List ──────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);
        $user  = auth()->user();

        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->isPatient()) {
            $patient = Patient::where('user_id', $user->id)->first();
            if ($patient) $query->where('patient_id', $patient->id);
            else $query->whereRaw('0=1');
        }

        if ($status = $request->get('status')) $query->where('status', $status);
        if ($date   = $request->get('date'))   $query->whereDate('appointment_date', $date);

        $appointments = $query->orderBy('appointment_date')->paginate(15);

        // Calendar — patients see only their own
        $calQuery = Appointment::with(['patient', 'doctor']);
        if ($user->isDoctor()) {
            $calQuery->where('doctor_id', $user->id);
        } elseif ($user->isPatient()) {
            $p = Patient::where('user_id', $user->id)->first();
            if ($p) $calQuery->where('patient_id', $p->id);
            else $calQuery->whereRaw('0=1');
        }

        $calendarAppointments = $calQuery
            ->whereHas('patient')
            ->whereHas('doctor')
            ->get()->map(fn($a) => [
            'id'    => $a->id,
            'title' => ($a->patient?->full_name ?? 'Patient') . ' — Dr. ' . ($a->doctor?->name ?? '—'),
            'start' => $a->appointment_date->toIso8601String(),
            'color' => match($a->status) {
                'confirmed' => '#22c55e',
                'cancelled' => '#ef4444',
                'completed' => '#94a3b8',
                default     => '#f59e0b',
            },
            'url' => route('appointments.show', $a->id),
        ]);

        return view('appointments.index', compact('appointments', 'calendarAppointments'));
    }

    // ── Create form ───────────────────────────────────────────
    public function create()
    {
        $doctors  = User::doctors()->active()->get();
        $patients = Patient::orderBy('last_name')->get();

        $selectedPatientId = null;
        if (auth()->user()->isPatient()) {
            $p = Patient::where('user_id', auth()->id())->first();
            $selectedPatientId = $p?->id;
        }

        return view('appointments.create', compact('doctors', 'patients', 'selectedPatientId'));
    }

    // ── Store ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'reason'           => 'nullable|string|max:500',
            'notes'            => 'nullable|string',
        ]);

        $appointment = Appointment::create($validated);
        $appointment->load(['patient', 'doctor']);

        $this->sendConfirmationEmail($appointment);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', '✅ Rendez-vous créé ! Un email de confirmation a été envoyé au patient.');
    }

    // ── Show ──────────────────────────────────────────────────
    public function show(Appointment $appointment)
    {
        // Patients can only see their own
        if (auth()->user()->isPatient()) {
            $patient = Patient::where('user_id', auth()->id())->first();
            if (!$patient || $appointment->patient_id !== $patient->id) abort(403);
        }

        $appointment->load(['patient', 'doctor', 'consultation.prescription.items']);
        return view('appointments.show', compact('appointment'));
    }

    // ── Edit form ─────────────────────────────────────────────
    public function edit(Appointment $appointment)
    {
        $doctors  = User::doctors()->active()->get();
        $patients = Patient::orderBy('last_name')->get();
        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'status'           => 'required|in:pending,confirmed,cancelled,completed',
            'reason'           => 'nullable|string|max:500',
            'notes'            => 'nullable|string',
        ]);

        $oldStatus = $appointment->status;
        $appointment->update($validated);
        $appointment->load(['patient', 'doctor']);

        if ($oldStatus !== 'confirmed' && $appointment->status === 'confirmed') {
            $this->sendConfirmationEmail($appointment);
            return redirect()->route('appointments.show', $appointment)
                ->with('success', '✅ RDV confirmé ! Email de confirmation envoyé au patient.');
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Rendez-vous mis à jour.');
    }

    // ── AJAX status update ────────────────────────────────────
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);

        $oldStatus = $appointment->status;
        $appointment->update(['status' => $request->status]);
        $appointment->load(['patient', 'doctor']);

        $emailSent = false;
        if ($oldStatus !== 'confirmed' && $request->status === 'confirmed') {
            $emailSent = $this->sendConfirmationEmail($appointment);
        }

        return response()->json([
            'success'    => true,
            'status'     => $appointment->status,
            'email_sent' => $emailSent,
            'message'    => $emailSent
                ? '✅ Statut mis à jour. Email de confirmation envoyé.'
                : 'Statut mis à jour.',
        ]);
    }

    // ── Send reminder manually ────────────────────────────────
    public function sendReminder(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);

        if (!$appointment->patient->email) {
            return back()->with('error', '❌ Ce patient n\'a pas d\'adresse email enregistrée.');
        }

        if (!$appointment->isConfirmed()) {
            return back()->with('error', '❌ Seuls les rendez-vous confirmés peuvent recevoir un rappel.');
        }

        try {
            Mail::to($appointment->patient->email)
                ->send(new AppointmentReminder($appointment));

            $appointment->update(['reminder_sent' => true]);

            return back()->with('success', '🔔 Email de rappel envoyé à ' . $appointment->patient->email);
        } catch (\Exception $e) {
            Log::error('Reminder email failed', [
                'appointment_id' => $appointment->id,
                'error'          => $e->getMessage(),
            ]);
            return back()->with('error', '❌ Échec de l\'envoi : ' . $e->getMessage());
        }
    }

    // ── Destroy ───────────────────────────────────────────────
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')
            ->with('success', 'Rendez-vous supprimé.');
    }

    // ── Available slots (AJAX) ────────────────────────────────
    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date'      => 'required|date|after_or_equal:today',
        ]);

        $date     = \Carbon\Carbon::parse($request->date);
        $schedule = DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $date->dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            return response()->json(['slots' => [], 'message' => 'Médecin non disponible ce jour.']);
        }

        $booked = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_date')
            ->map(fn($d) => $d->format('H:i'))
            ->toArray();

        $slots = [];
        $start = \Carbon\Carbon::parse($request->date . ' ' . $schedule->start_time);
        $end   = \Carbon\Carbon::parse($request->date . ' ' . $schedule->end_time);

        while ($start < $end) {
            $t = $start->format('H:i');
            if (!in_array($t, $booked) && $start->isAfter(now())) {
                $slots[] = $t;
            }
            $start->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }

    // ── Private helpers ───────────────────────────────────────
    private function sendConfirmationEmail(Appointment $appointment): bool
    {
        if (!$appointment->patient->email) return false;

        try {
            Mail::to($appointment->patient->email)
                ->send(new AppointmentConfirmed($appointment));
            return true;
        } catch (\Exception $e) {
            Log::error('Confirmation email failed', [
                'appointment_id' => $appointment->id,
                'error'          => $e->getMessage(),
            ]);
            return false;
        }
    }
}
