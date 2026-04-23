<?php
// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Consultation, Patient, User};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_patients'      => Patient::count(),
            'total_doctors'       => User::doctors()->count(),
            'today_appointments'  => Appointment::today()->count(),
            'total_consultations' => Consultation::count(),
            'pending_appointments'=> Appointment::where('status', 'pending')->count(),
            'new_patients_month'  => Patient::whereMonth('created_at', now()->month)->count(),
        ];

        // Chart: appointments per day (last 30 days)
        $appointmentsPerDay = Appointment::select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('appointment_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Chart: consultations per doctor (top 5)
        $consultationsPerDoctor = Consultation::select(
                'doctor_id',
                DB::raw('COUNT(*) as count')
            )
            ->with('doctor:id,name')
            ->groupBy('doctor_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Chart: appointment statuses
        $appointmentStatuses = Appointment::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        // Recent appointments
        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'appointmentsPerDay', 'consultationsPerDoctor',
            'appointmentStatuses', 'recentAppointments'
        ));
    }
}
