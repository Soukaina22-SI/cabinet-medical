<?php
// app/Http/Controllers/Admin/StatisticsController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Consultation, Patient, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // week | month | year

        $startDate = match($period) {
            'week'  => now()->startOfWeek(),
            'year'  => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        // ── KPIs ──────────────────────────────────────────────
        $kpis = [
            'new_patients'        => Patient::where('created_at', '>=', $startDate)->count(),
            'total_appointments'  => Appointment::where('appointment_date', '>=', $startDate)->count(),
            'confirmed_appointments' => Appointment::where('appointment_date', '>=', $startDate)
                ->whereIn('status', ['confirmed', 'completed'])->count(),
            'total_consultations' => Consultation::where('created_at', '>=', $startDate)->count(),
            'cancellation_rate'   => 0,
        ];

        if ($kpis['total_appointments'] > 0) {
            $cancelled = Appointment::where('appointment_date', '>=', $startDate)
                ->where('status', 'cancelled')->count();
            $kpis['cancellation_rate'] = round(($cancelled / $kpis['total_appointments']) * 100, 1);
        }

        // ── Appointments per day ───────────────────────────────
        $appointmentsPerDay = Appointment::select(
                DB::raw('DATE(appointment_date) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(status = "confirmed" OR status = "completed") as confirmed'),
                DB::raw('SUM(status = "cancelled") as cancelled')
            )
            ->where('appointment_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Appointments per doctor ────────────────────────────
        $appointmentsPerDoctor = User::doctors()
            ->withCount(['appointments as total_rdv' => fn($q) =>
                $q->where('appointment_date', '>=', $startDate)])
            ->withCount(['consultations as total_consult' => fn($q) =>
                $q->where('created_at', '>=', $startDate)])
            ->having('total_rdv', '>', 0)
            ->orderByDesc('total_rdv')
            ->get();

        // ── Appointments by hour of day ────────────────────────
        $appointmentsByHour = Appointment::select(
                DB::raw('HOUR(appointment_date) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->where('appointment_date', '>=', $startDate)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // ── New patients per month (last 12 months) ─────────────
        $newPatientsPerMonth = Patient::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Appointments by status ─────────────────────────────
        $statusBreakdown = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->where('appointment_date', '>=', $startDate)
            ->groupBy('status')
            ->get();

        return view('admin.statistics', compact(
            'kpis', 'appointmentsPerDay', 'appointmentsPerDoctor',
            'appointmentsByHour', 'newPatientsPerMonth', 'statusBreakdown',
            'period', 'startDate'
        ));
    }
}
