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
        ->orderBy('appointment_date')
        ->get();
    $upcomingAppts = \App\Models\Appointment::with('patient')
        ->where('doctor_id', $doctor->id)
        ->upcoming()
        ->orderBy('appointment_date')
        ->limit(10)
        ->get();
    $totalConsultations = \App\Models\Consultation::where('doctor_id', $doctor->id)->count();
    $pendingCount = \App\Models\Appointment::where('doctor_id', $doctor->id)->where('status','pending')->count();
@endphp

{{-- Welcome --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Bonjour, Dr. {{ $doctor->name }} 👋</h5>
        <p class="text-muted small mb-0">{{ now()->translatedFormat('l d F Y') }}</p>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn btn-primary rounded-3">
        <i class="bi bi-plus-lg me-1"></i>Nouveau RDV
    </a>
</div>

{{-- Quick stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Aujourd'hui</p>
                    <h3 class="fw-bold mb-0">{{ $todayAppts->count() }}</h3>
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
                    <small class="text-muted">total effectuées</small>
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
                    <p class="text-muted small mb-1">Spécialité</p>
                    <h6 class="fw-bold mb-0 small">{{ $doctor->speciality ?? 'Généraliste' }}</h6>
                    <small class="text-muted">votre domaine</small>
                </div>
                <div class="stat-icon" style="background:#ede9fe;color:#7c3aed">
                    <i class="bi bi-award"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Today's appointments --}}
    <div class="col-lg-6">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar-day text-primary me-2"></i>Planning d'aujourd'hui
                </h6>
                <span class="badge bg-primary-subtle text-primary">{{ $todayAppts->count() }} RDV</span>
            </div>
            <div class="p-2">
                @forelse($todayAppts as $appt)
                <a href="{{ route('appointments.show', $appt) }}"
                   class="d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none text-dark hover-bg mb-1"
                   style="transition:background .15s" onmouseover="this.style.background='#f8fafc'"
                   onmouseout="this.style.background=''">
                    <div class="text-center rounded-2 px-2 py-1" style="background:#eff6ff;min-width:52px">
                        <div class="fw-bold text-primary">{{ $appt->appointment_date->format('H:i') }}</div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $appt->patient->full_name }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $appt->reason ?? 'Consultation générale' }}</div>
                    </div>
                    <div>{!! $appt->status_badge !!}</div>
                </a>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-sun fs-3 d-block mb-2"></i>
                    <small>Pas de rendez-vous aujourd'hui</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming appointments --}}
    <div class="col-lg-6">
        <div class="table-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar-week text-success me-2"></i>Prochains RDV
                </h6>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-light">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 small">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingAppts as $appt)
                        <tr>
                            <td class="fw-semibold">{{ $appt->patient->full_name }}</td>
                            <td>
                                <span class="fw-semibold">{{ $appt->appointment_date->format('d/m') }}</span>
                                <span class="text-muted ms-1">{{ $appt->appointment_date->format('H:i') }}</span>
                            </td>
                            <td>{!! $appt->status_badge !!}</td>
                            <td>
                                <a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Aucun RDV à venir</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
