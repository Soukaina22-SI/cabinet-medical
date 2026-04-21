
 
{{-- ============================================================ --}}
{{-- resources/views/admin/dashboard.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Tableau de Bord Admin')
@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 text-primary me-2"></i>Tableau de Bord</h4>
 
{{-- Stats cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-primary p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Total Patients</div>
                    <div class="fs-2 fw-bold text-primary">{{ $total_patients }}</div>
                </div>
                <i class="bi bi-person-heart fs-2 text-primary opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-success p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Médecins</div>
                    <div class="fs-2 fw-bold text-success">{{ $total_medecins }}</div>
                </div>
                <i class="bi bi-activity fs-2 text-success opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-warning p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">RDV cette semaine</div>
                    <div class="fs-2 fw-bold text-warning">{{ $rdvs_semaine }}</div>
                </div>
                <i class="bi bi-calendar3 fs-2 text-warning opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-info p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small">Consultations ce mois</div>
                    <div class="fs-2 fw-bold text-info">{{ $consultations_mois }}</div>
                </div>
                <i class="bi bi-clipboard2-pulse fs-2 text-info opacity-50"></i>
            </div>
        </div>
    </div>
</div>
 
{{-- Charts --}}
<div class="row g-3">
    <div class="col-md-8">
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Rendez-vous par mois ({{ now()->year }})</h6>
            <canvas id="chartRdvMois" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Statuts des RDV</h6>
            <canvas id="chartStatuts" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card p-3">
            <h6 class="fw-bold mb-3">Top médecins (consultations)</h6>
            <canvas id="chartMedecins" height="60"></canvas>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
// Chart RDV par mois
new Chart(document.getElementById('chartRdvMois'), {
    type: 'line',
    data: {
        labels: {!! json_encode($rdvs_par_mois['labels']) !!},
        datasets: [{
            label: 'Rendez-vous',
            data: {!! json_encode($rdvs_par_mois['data']) !!},
            borderColor: '#1a6fa0',
            backgroundColor: 'rgba(26,111,160,0.1)',
            fill: true,
            tension: 0.4,
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
 
// Chart statuts
new Chart(document.getElementById('chartStatuts'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($rdvs_par_statut['labels']) !!},
        datasets: [{ data: {!! json_encode($rdvs_par_statut['data']) !!}, backgroundColor: ['#ffc107','#198754','#dc3545','#6c757d'] }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
 
// Chart médecins
new Chart(document.getElementById('chartMedecins'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($consultations_par_medecin['labels']) !!},
        datasets: [{ label: 'Consultations', data: {!! json_encode($consultations_par_medecin['data']) !!}, backgroundColor: '#1a6fa0' }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
@endpush
 
 