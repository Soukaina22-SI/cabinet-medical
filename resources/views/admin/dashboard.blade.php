{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Tableau de bord Admin')

@section('breadcrumb')
    <li class="breadcrumb-item active">Tableau de bord</li>
@endsection

@section('content')

{{-- ── Stat Cards ──────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Total Patients</p>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_patients']) }}</h3>
                    <small class="text-success"><i class="bi bi-arrow-up-short"></i>+{{ $stats['new_patients_month'] }} ce mois</small>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Médecins Actifs</p>
                    <h3 class="fw-bold mb-0">{{ $stats['total_doctors'] }}</h3>
                    <small class="text-muted">Équipe médicale</small>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#059669">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">RDV Aujourd'hui</p>
                    <h3 class="fw-bold mb-0">{{ $stats['today_appointments'] }}</h3>
                    <small class="text-warning"><i class="bi bi-clock"></i>
                        {{ $stats['pending_appointments'] }} en attente
                    </small>
                </div>
                <div class="stat-icon" style="background:#fef9c3;color:#ca8a04">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Consultations</p>
                    <h3 class="fw-bold mb-0">{{ number_format($stats['total_consultations']) }}</h3>
                    <small class="text-muted">Total effectuées</small>
                </div>
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Charts Row ──────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Line chart: RDV last 30 days --}}
    <div class="col-lg-8">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold mb-0">Rendez-vous (30 derniers jours)</h6>
                    <p class="text-muted small mb-0">Activité journalière</p>
                </div>
                <span class="badge bg-primary-subtle text-primary">Mensuel</span>
            </div>
            <canvas id="appointmentsChart" height="100"></canvas>
        </div>
    </div>

    {{-- Doughnut: statuses --}}
    <div class="col-lg-4">
        <div class="stat-card h-100">
            <div class="mb-3">
                <h6 class="fw-bold mb-0">Statuts des RDV</h6>
                <p class="text-muted small mb-0">Répartition globale</p>
            </div>
            <canvas id="statusChart" height="180"></canvas>
            <div id="statusLegend" class="mt-3"></div>
        </div>
    </div>

</div>

<div class="row g-3 mb-4">

    {{-- Bar chart: consultations per doctor --}}
    <div class="col-lg-5">
        <div class="stat-card">
            <div class="mb-3">
                <h6 class="fw-bold mb-0">Consultations par médecin</h6>
                <p class="text-muted small mb-0">Top 5 médecins</p>
            </div>
            <canvas id="doctorChart" height="160"></canvas>
        </div>
    </div>

    {{-- Recent appointments table --}}
    <div class="col-lg-7">
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h6 class="fw-bold mb-0">Rendez-vous récents</h6>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Médecin</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAppointments as $appt)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:32px;height:32px;background:#dbeafe;color:#1d4ed8;font-size:.75rem;font-weight:700">
                                        {{ $appt->patient ? strtoupper(substr($appt->patient?->first_name ?? '?',0,1).substr($appt->patient?->last_name ?? '',0,1)) : '?' }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $appt->patient?->full_name ?? 'Patient inconnu' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><small>Dr. {{ $appt->doctor?->name ?? '—' }}</small></td>
                            <td><small>{{ $appt->appointment_date->format('d/m/Y H:i') }}</small></td>
                            <td>{!! $appt->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>Aucun rendez-vous
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
const chartDefaults = {
    font: { family: "'Segoe UI', sans-serif" },
    color: '#64748b',
};
Chart.defaults.font.family = chartDefaults.font.family;

// ── Line Chart: appointments per day ─────────────────────────
const apptData = @json($appointmentsPerDay);
new Chart(document.getElementById('appointmentsChart'), {
    type: 'line',
    data: {
        labels: apptData.map(d => {
            const dt = new Date(d.date);
            return dt.toLocaleDateString('fr-FR', { day:'2-digit', month:'short' });
        }),
        datasets: [{
            label: 'Rendez-vous',
            data: apptData.map(d => d.count),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,.08)',
            borderWidth: 2.5,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#3b82f6',
            pointRadius: 3,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});

// ── Doughnut: statuses ───────────────────────────────────────
const statusData = @json($appointmentStatuses);
const statusColors = { pending:'#fbbf24', confirmed:'#22c55e', cancelled:'#ef4444', completed:'#94a3b8' };
const statusLabels = { pending:'En attente', confirmed:'Confirmés', cancelled:'Annulés', completed:'Terminés' };

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusData.map(s => statusLabels[s.status] || s.status),
        datasets: [{
            data: statusData.map(s => s.count),
            backgroundColor: statusData.map(s => statusColors[s.status] || '#ccc'),
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } } }
    }
});

// ── Bar Chart: consultations per doctor ──────────────────────
const docData = @json($consultationsPerDoctor);
new Chart(document.getElementById('doctorChart'), {
    type: 'bar',
    data: {
        labels: docData.map(d => 'Dr. ' + d.doctor?.name?.split(' ')[0]),
        datasets: [{
            label: 'Consultations',
            data: docData.map(d => d.count),
            backgroundColor: ['#3b82f6','#22c55e','#f59e0b','#ef4444','#8b5cf6'],
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0 } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
