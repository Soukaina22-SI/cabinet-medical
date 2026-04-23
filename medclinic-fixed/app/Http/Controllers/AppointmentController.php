<?php
// app/Http/Controllers/AppointmentController.php
namespace App\Http\Controllers;

use App\Models\{Appointment, Patient, User};
use App\Mail\AppointmentConfirmed;
use App\Mail\AppointmentReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);

        // Role-based filtering
        $user = auth()->user();
        if ($user->isDoctor()) {
            $query->where('doctor_id', $user->id);
        } elseif ($user->isPatient()) {
            $patient = Patient::where('user_id', $user->id)->first();
            if ($patient) $query->where('patient_id', $patient->id);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->get('date')) {
            $query->whereDate('appointment_date', $date);
        }

        $appointments = $query->orderBy('appointment_date')->paginate(15);

        // For the calendar view
        $calendarAppointments = Appointment::with(['patient', 'doctor'])
            ->when($user->isDoctor(), fn($q) => $q->where('doctor_id', $user->id))
            ->get()
            ->map(fn($a) => [
                'id'    => $a->id,
                'title' => $a->patient->full_name . ' — Dr. ' . $a->doctor->name,
                'start' => $a->appointment_date->toIso8601String(),
                'color' => match($a->status) {
                    'confirmed' => '#198754',
                    'cancelled' => '#dc3545',
                    'completed' => '#6c757d',
                    default     => '#ffc107',
                },
                'url' => route('appointments.show', $a->id),
            ]);

        return view('appointments.index', compact('appointments', 'calendarAppointments'));
    }

    public function create()
    {
        $doctors  = User::doctors()->active()->get();
        $patients = Patient::orderBy('last_name')->get();
        return view('appointments.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:now',
            'reason'           => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $appointment = Appointment::create($validated);

        // Send confirmation email
        if ($appointment->patient->email) {
            Mail::to($appointment->patient->email)
                ->send(new AppointmentConfirmed($appointment));
        }

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Rendez-vous créé et email de confirmation envoyé.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor', 'consultation.prescription.items']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $doctors  = User::doctors()->active()->get();
        $patients = Patient::orderBy('last_name')->get();
        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'status'           => 'required|in:pending,confirmed,cancelled,completed',
            'reason'           => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Rendez-vous mis à jour.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,completed']);
        $appointment->update(['status' => $request->status]);

        return response()->json(['success' => true, 'status' => $appointment->status]);
    }

    // Returns available time slots for a doctor on a given date
    public function availableSlots(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date'      => 'required|date',
        ]);

        $date      = \Carbon\Carbon::parse($request->date);
        $dayOfWeek = $date->dayOfWeek;

        $schedule = \App\Models\DoctorSchedule::where('doctor_id', $request->doctor_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            return response()->json(['slots' => []]);
        }

        // Generate 30-min slots
        $slots = [];
        $start = \Carbon\Carbon::parse($request->date . ' ' . $schedule->start_time);
        $end   = \Carbon\Carbon::parse($request->date . ' ' . $schedule->end_time);

        $booked = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_date')
            ->map(fn($d) => $d->format('H:i'))
            ->toArray();

        while ($start < $end) {
            $timeStr = $start->format('H:i');
            if (!in_array($timeStr, $booked) && $start > now()) {
                $slots[] = $timeStr;
            }
            $start->addMinutes(30);
        }

        return response()->json(['slots' => $slots]);
    }
}
