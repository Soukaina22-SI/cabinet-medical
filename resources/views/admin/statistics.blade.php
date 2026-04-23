{{-- resources/views/admin/statistics.blade.php --}}
@extends('layouts.app')
@section('title', 'Statistiques')

@section('breadcrumb')
    <li class="breadcrumb-item active">Statistiques</li>
@endsection

@section('content')

{{-- Period selector --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Statistiques & Rapports</h5>
        <p class="text-muted small mb-0">Depuis le {{ $startDate->format('d/m/Y') }}</p>
    </div>
    <div class="btn-group" role="group">
        @foreach(['week' => 'Cette semaine', 'month' => 'Ce mois', 'year' => 'Cette année'] as $val => $label)
        <a href="{{ route('admin.statistics', ['period' => $val]) }}"
           class="btn btn-sm {{ $period === $val ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#dbeafe;color:#2563eb">
                <i class="bi bi-person-plus"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $kpis['new_patients'] }}</h3>
            <p class="text-muted small mb-0">Nouveaux patients</p>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#fef9c3;color:#ca8a04">
                <i class="bi bi-calendar3"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $kpis['total_appointments'] }}</h3>
            <p class="text-muted small mb-0">Total rendez-vous</p>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#d1fae5;color:#059669">
                <i class="bi bi-check-circle"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $kpis['confirmed_appointments'] }}</h3>
            <p class="text-muted small mb-0">RDV honorés</p>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#ede9fe;color:#7c3aed">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $kpis['total_consultations'] }}</h3>
            <p class="text-muted small mb-0">Consultations</p>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#fee2e2;color:#dc2626">
                <i class="bi bi-x-circle"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $kpis['cancellation_rate'] }}%</h3>
            <p class="text-muted small mb-0">Taux d'annulation</p>
        </div>
    </div>
</div>

{{-- Charts Row 1 --}}
<div class="row g-3 mb-4">

    {{-- Stacked line chart --}}
    <div class="col-lg-8">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-0">Évolution des rendez-vous</h6>
                    <p class="text-muted small mb-0">Confirmés vs Annulés</p>
                </div>
            </div>
            <canvas id="evolutionChart" height="100"></canvas>
        </div>
    </div>

    {{-- Pie: status breakdown --}}
    <div class="col-lg-4">
        <div class="stat-card h-100">
            <h6 class="fw-bold mb-3">Répartition des statuts</h6>
            <canvas id="statusPieChart" height="200"></canvas>
        </div>
    </div>

</div>

{{-- Charts Row 2 --}}
<div class="row g-3 mb-4">

    {{-- Bar: by hour --}}
    <div class="col-lg-5">
        <div class="stat-card">
            <h6 class="fw-bold mb-1">Affluence par heure</h6>
            <p class="text-muted small mb-3">Heures de pointe des RDV</p>
            <canvas id="hourChart" height="160"></canvas>
        </div>
    </div>

    {{-- Line: new patients monthly --}}
    <div class="col-lg-7">
        <div class="stat-card">
            <h6 class="fw-bold mb-1">Nouveaux patients (12 mois)</h6>
            <p class="text-muted small mb-3">Évolution de l'acquisition patient</p>
            <canvas id="patientsChart" height="120"></canvas>
        </div>
    </div>

</div>

{{-- Doctor performance table --}}
<div class="table-card">
    <div class="p-3 border-bottom">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-trophy text-warning me-2"></i>Performance des médecins
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Médecin</th>
                    <th>Spécialité</th>
                    <th>RDV</th>
                    <th>Consultations</th>
                    <th>Taux de complétion</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointmentsPerDoctor as $idx => $doctor)
                <tr>
                    <td>
                        @if($idx === 0) 🥇
                        @elseif($idx === 1) 🥈
                        @elseif($idx === 2) 🥉
                        @else {{ $idx + 1 }}
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $doctor->avatar_url }}" class="rounded-circle" width="32" height="32">
                            <span class="fw-semibold small">Dr. {{ $doctor->name }}</span>
                        </div>
                    </td>
                    <td><small class="text-muted">{{ $doctor->speciality ?? '—' }}</small></td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary">{{ $doctor->total_rdv }}</span>
                    </td>
                    <td>
                        <span class="badge bg-success-subtle text-success">{{ $doctor->total_consult }}</span>
                    </td>
                    <td>
                        @php
                            $rate = $doctor->total_rdv > 0
                                ? round(($doctor->total_consult / $doctor->total_rdv) * 100)
                                : 0;
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px">
                                <div class="progress-bar bg-{{ $rate >= 70 ? 'success' : ($rate >= 40 ? 'warning' : 'danger') }}"
                                     style="width:{{ $rate }}%"></div>
                            </div>
                            <small class="text-muted">{{ $rate }}%</small>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">
                    Aucune donnée pour cette période
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── 1. Evolution chart (stacked line) ────────────────────────
const evoData = @json($appointmentsPerDay);
new Chart(document.getElementById('evolutionChart'), {
    type: 'line',
    data: {
        labels: evoData.map(d => {
            const dt = new Date(d.date);
            return dt.toLocaleDateString('fr-FR', { day:'2-digit', month:'short' });
        }),
        datasets: [
            {
                label: 'Total',
                data: evoData.map(d => d.total),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,.06)',
                borderWidth: 2, tension: 0.4, fill: true,
            },
            {
                label: 'Confirmés',
                data: evoData.map(d => d.confirmed),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34,197,94,.06)',
                borderWidth: 2, tension: 0.4, fill: true,
            },
            {
                label: 'Annulés',
                data: evoData.map(d => d.cancelled),
                borderColor: '#ef4444',
                borderWidth: 2, tension: 0.4, borderDash: [4, 4],
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// ── 2. Status pie ────────────────────────────────────────────
const statusData = @json($statusBreakdown);
const statusColors = { pending:'#fbbf24', confirmed:'#22c55e', cancelled:'#ef4444', completed:'#94a3b8' };
const statusLabels = { pending:'En attente', confirmed:'Confirmés', cancelled:'Annulés', completed:'Terminés' };
new Chart(document.getElementById('statusPieChart'), {
    type: 'pie',
    data: {
        labels: statusData.map(s => statusLabels[s.status] || s.status),
        datasets: [{
            data: statusData.map(s => s.count),
            backgroundColor: statusData.map(s => statusColors[s.status] || '#ccc'),
            borderWidth: 2, borderColor: '#fff', hoverOffset: 6,
        }]
    },
    options: {
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } } }
    }
});

// ── 3. Hour bar chart ────────────────────────────────────────
const hourData  = @json($appointmentsByHour);
const allHours  = Array.from({length: 10}, (_, i) => i + 8); // 8-17
const hourCounts = allHours.map(h => {
    const found = hourData.find(d => parseInt(d.hour) === h);
    return found ? found.count : 0;
});
new Chart(document.getElementById('hourChart'), {
    type: 'bar',
    data: {
        labels: allHours.map(h => `${h}:00`),
        datasets: [{
            label: 'RDV',
            data: hourCounts,
            backgroundColor: hourCounts.map(c => {
                const max = Math.max(...hourCounts);
                const alpha = 0.3 + (c / (max || 1)) * 0.7;
                return `rgba(59,130,246,${alpha})`;
            }),
            borderRadius: 6, borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// ── 4. New patients monthly ──────────────────────────────────
const ptData = @json($newPatientsPerMonth);
new Chart(document.getElementById('patientsChart'), {
    type: 'bar',
    data: {
        labels: ptData.map(d => {
            const [y, m] = d.month.split('-');
            return new Date(y, m-1).toLocaleDateString('fr-FR', { month:'short', year:'2-digit' });
        }),
        datasets: [{
            label: 'Nouveaux patients',
            data: ptData.map(d => d.count),
            backgroundColor: '#8b5cf6',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
