{{-- resources/views/doctor/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Tableau de bord Médecin')

@section('content')
@php
    $doctor = auth()->user();

    $todayAppts = \App\Models\Appointment::with('patient')
        ->where('doctor_id', $doctor->id)
        ->today()
        ->whereIn('status', ['pending','confirmed'])
        ->whereHas('patient')
        ->orderBy('appointment_date')
        ->get();

    $upcomingAppts = \App\Models\Appointment::with('patient')
        ->where('doctor_id', $doctor->id)
        ->upcoming()
        ->whereNotIn('status', ['cancelled'])
        ->whereHas('patient')
        ->orderBy('appointment_date')
        ->limit(10)
        ->get();

    $totalConsultations = \App\Models\Consultation::where('doctor_id', $doctor->id)->count();
    $pendingCount       = \App\Models\Appointment::where('doctor_id', $doctor->id)->where('status','pending')->count();
    $todayCount         = \App\Models\Appointment::where('doctor_id', $doctor->id)->today()->count();
    $completedCount     = \App\Models\Appointment::where('doctor_id', $doctor->id)->where('status','completed')->count();
@endphp

{{-- Welcome bar --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Bonjour, Dr. {{ $doctor->name }} 👋</h5>
        <p class="text-muted small mb-0">{{ now()->translatedFormat('l d F Y') }}</p>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn btn-primary rounded-3">
        <i class="bi bi-plus-lg me-1"></i>Nouveau RDV
    </a>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible rounded-3 mb-3">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Quick stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Aujourd'hui</p>
                    <h3 class="fw-bold mb-0">{{ $todayCount }}</h3>
                    <small class="text-muted">rendez-vous</small>
                </div>
                <div class="stat-icon" style="background:#dbeafe;color:#2563eb">
                    <i class="bi bi-calendar-day"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">En attente</p>
                    <h3 class="fw-bold mb-0">{{ $pendingCount }}</h3>
                    <small class="text-muted">à confirmer</small>
                </div>
                <div class="stat-icon" style="background:#fef9c3;color:#ca8a04">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Consultations</p>
                    <h3 class="fw-bold mb-0">{{ $totalConsultations }}</h3>
                    <small class="text-muted">au total</small>
                </div>
                <div class="stat-icon" style="background:#d1fae5;color:#059669">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Terminés</p>
                    <h3 class="fw-bold mb-0">{{ $completedCount }}</h3>
                    <small class="text-muted">rendez-vous</small>
                </div>
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- ── RDV du jour ── --}}
    <div class="col-lg-5">
        <div class="table-card h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar-day text-primary me-2"></i>RDV d'aujourd'hui
                </h6>
                <span class="badge bg-primary rounded-pill">{{ $todayAppts->count() }}</span>
            </div>

            @forelse($todayAppts as $appt)
            <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                {{-- Avatar --}}
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                     style="width:40px;height:40px;background:#eff6ff;color:#3b82f6;font-size:.85rem">
                    {{ $appt->patient ? strtoupper(substr($appt->patient?->first_name ?? '?',0,1).substr($appt->patient?->last_name ?? '',0,1)) : '?' }}
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="fw-semibold small text-truncate">
                        {{ $appt->patient?->full_name ?? 'Patient inconnu' }}
                    </div>
                    <div class="text-muted" style="font-size:.78rem">
                        <i class="bi bi-clock me-1"></i>{{ $appt->appointment_date->format('H:i') }}
                        @if($appt->reason)
                            — {{ Str::limit($appt->reason, 25) }}
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0 d-flex flex-column align-items-end gap-1">
                    {!! $appt->status_badge !!}
                    <a href="{{ route('appointments.show', $appt) }}" class="btn btn-xs btn-light py-0 px-2" style="font-size:.72rem">
                        <i class="bi bi-eye me-1"></i>Voir
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-calendar-check fs-2 d-block mb-2 opacity-40"></i>
                <div class="small">Aucun rendez-vous aujourd'hui</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── RDV à venir ── --}}
    <div class="col-lg-7">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar3 text-success me-2"></i>Prochains rendez-vous
                </h6>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">
                    Tout voir <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingAppts as $appt)
                        <tr>
                            <td class="fw-semibold align-middle">
                                {{ $appt->patient?->full_name ?? '<span class="text-muted">—</span>' }}
                            </td>
                            <td class="align-middle">
                                <span class="fw-semibold">{{ $appt->appointment_date->format('d/m/Y') }}</span>
                                <span class="text-muted ms-1">{{ $appt->appointment_date->format('H:i') }}</span>
                            </td>
                            <td class="align-middle text-muted">
                                {{ $appt->reason ? Str::limit($appt->reason, 30) : '—' }}
                            </td>
                            <td class="align-middle">{!! $appt->status_badge !!}</td>
                            <td class="align-middle">
                                <div class="d-flex gap-1">
                                    <a href="{{ route('appointments.show', $appt) }}"
                                       class="btn btn-sm btn-light" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($appt->isConfirmed() && !$appt->consultation)
                                    <a href="{{ route('consultations.create', $appt) }}"
                                       class="btn btn-sm btn-success" title="Démarrer consultation">
                                        <i class="bi bi-clipboard2-pulse"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x d-block mb-2 fs-4 opacity-40"></i>
                                Aucun rendez-vous à venir
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Actions rapides ── --}}
        <div class="row g-2 mt-2">
            <div class="col-6">
                <a href="{{ route('appointments.create') }}"
                   class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-calendar-plus"></i>Nouveau RDV
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('doctor.schedule') }}"
                   class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-clock-history"></i>Mon planning
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
