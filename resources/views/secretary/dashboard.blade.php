{{-- resources/views/secretary/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Tableau de bord Secrétaire')

@section('content')
@php
    $todayAppts = \App\Models\Appointment::with(['patient','doctor'])
        ->today()->orderBy('appointment_date')->get();
    $pendingAppts = \App\Models\Appointment::with(['patient','doctor'])
        ->where('status','pending')->orderBy('appointment_date')->limit(15)->get();
    $doctors = \App\Models\User::doctors()->active()->withCount(['appointments' => fn($q) => $q->today()])->get();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Accueil & Secrétariat</h5>
        <p class="text-muted small mb-0">{{ now()->translatedFormat('l d F Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.patients.create') }}" class="btn btn-outline-primary rounded-3">
            <i class="bi bi-person-plus me-1"></i>Nouveau Patient
        </a>
        <a href="{{ route('appointments.create') }}" class="btn btn-primary rounded-3">
            <i class="bi bi-calendar-plus me-1"></i>Nouveau RDV
        </a>
    </div>
</div>

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#dbeafe;color:#2563eb">
                <i class="bi bi-calendar-check"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $todayAppts->count() }}</h3>
            <p class="text-muted small mb-0">RDV aujourd'hui</p>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#fef9c3;color:#ca8a04">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $pendingAppts->count() }}</h3>
            <p class="text-muted small mb-0">En attente de confirmation</p>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#d1fae5;color:#059669">
                <i class="bi bi-people"></i>
            </div>
            <h3 class="fw-bold mb-0">{{ $doctors->count() }}</h3>
            <p class="text-muted small mb-0">Médecins disponibles</p>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Pending confirmations --}}
    <div class="col-lg-7">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex justify-content-between">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history text-warning me-2"></i>À confirmer
                </h6>
                <span class="badge bg-warning-subtle text-warning">{{ $pendingAppts->count() }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead><tr><th>Patient</th><th>Médecin</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($pendingAppts as $appt)
                        <tr>
                            <td class="fw-semibold">{{ $appt->patient?->full_name ?? 'Patient inconnu' }}</td>
                            <td>Dr. {{ $appt->doctor?->name ?? '—' }}</td>
                            <td>{{ $appt->appointment_date->format('d/m H:i') }}</td>
                            <td>
                                <button class="btn btn-xs btn-success px-2 py-0"
                                        onclick="quickConfirm({{ $appt->id }})">
                                    <i class="bi bi-check"></i>
                                </button>
                                <button class="btn btn-xs btn-outline-danger px-2 py-0 ms-1"
                                        onclick="quickCancel({{ $appt->id }})">
                                    <i class="bi bi-x"></i>
                                </button>
                                <a href="{{ route('appointments.show', $appt) }}"
                                   class="btn btn-xs btn-light px-2 py-0 ms-1">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">
                            <i class="bi bi-check-circle text-success me-1"></i>Aucun RDV en attente
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Doctors availability today --}}
    <div class="col-lg-5">
        <div class="table-card">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-badge text-success me-2"></i>Médecins — Aujourd'hui
                </h6>
            </div>
            <div class="p-3">
                @foreach($doctors as $doc)
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <img src="{{ $doc->avatar_url }}" class="rounded-circle" width="40" height="40" alt="">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Dr. {{ $doc->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $doc->speciality ?? 'Généraliste' }}</div>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">
                        {{ $doc->appointments_count }} RDV
                    </span>
                    <a href="{{ route('appointments.create') }}?doctor_id={{ $doc->id }}"
                       class="btn btn-sm btn-light" title="Créer RDV">
                        <i class="bi bi-plus"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
async function quickConfirm(id) {
    await changeStatus(id, 'confirmed');
}
async function quickCancel(id) {
    if (!confirm('Annuler ce rendez-vous ?')) return;
    await changeStatus(id, 'cancelled');
}
async function changeStatus(id, status) {
    const res = await fetch(`/appointments/${id}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: JSON.stringify({ status })
    });
    if (res.ok) location.reload();
}
</script>
@endpush
