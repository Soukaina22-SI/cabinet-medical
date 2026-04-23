{{-- resources/views/patient/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Mon Espace Patient')

@section('content')
@php
    $user = auth()->user();
    $patient = \App\Models\Patient::where('user_id', $user->id)->first();
    $upcomingAppts = $patient ? \App\Models\Appointment::with('doctor')
        ->where('patient_id', $patient->id)->upcoming()
        ->orderBy('appointment_date')->limit(5)->get() : collect();
    $consultations = $patient ? \App\Models\Consultation::with(['doctor','prescription.items'])
        ->where('patient_id', $patient->id)->latest()->limit(5)->get() : collect();
@endphp

<div class="mb-4">
    <h5 class="fw-bold mb-0">Bonjour, {{ $user->name }} 👋</h5>
    <p class="text-muted small mb-0">Bienvenue dans votre espace santé personnel</p>
</div>

@if(!$patient)
<div class="alert alert-info rounded-3">
    <i class="bi bi-info-circle me-2"></i>
    Votre dossier patient n'a pas encore été créé. Contactez l'accueil pour le faire.
</div>
@else

{{-- Quick stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#dbeafe;color:#2563eb">
                <i class="bi bi-calendar3"></i>
            </div>
            <h3 class="fw-bold">{{ $upcomingAppts->count() }}</h3>
            <p class="text-muted small">RDV à venir</p>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#d1fae5;color:#059669">
                <i class="bi bi-clipboard2-pulse"></i>
            </div>
            <h3 class="fw-bold">{{ $patient->consultations->count() }}</h3>
            <p class="text-muted small">Consultations</p>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card text-center">
            <div class="stat-icon mx-auto mb-2" style="background:#fef9c3;color:#ca8a04">
                <i class="bi bi-file-earmark-medical"></i>
            </div>
            <h3 class="fw-bold">{{ $patient->prescriptions->count() }}</h3>
            <p class="text-muted small">Ordonnances</p>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Patient info card --}}
    <div class="col-lg-4">
        <div class="stat-card">
            <h6 class="fw-semibold mb-3 text-primary">
                <i class="bi bi-person-circle me-2"></i>Ma fiche médicale
            </h6>
            <table class="table table-sm table-borderless small">
                <tr><td class="text-muted">Nom complet</td><td class="fw-semibold">{{ $patient->full_name }}</td></tr>
                <tr><td class="text-muted">Âge</td><td>{{ $patient->age }} ans</td></tr>
                <tr><td class="text-muted">Téléphone</td><td>{{ $patient->phone }}</td></tr>
                <tr>
                    <td class="text-muted">Groupe sanguin</td>
                    <td>
                        @if($patient->blood_type)
                            <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                        @else — @endif
                    </td>
                </tr>
                @if($patient->allergies)
                <tr>
                    <td class="text-muted">Allergies</td>
                    <td class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>{{ $patient->allergies }}</td>
                </tr>
                @endif
            </table>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary w-100 mt-2">
                <i class="bi bi-calendar-plus me-1"></i>Prendre un rendez-vous
            </a>
        </div>
    </div>

    <div class="col-lg-8">

        {{-- Upcoming appointments --}}
        <div class="table-card mb-3">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar3 text-primary me-2"></i>Mes prochains rendez-vous</h6>
            </div>
            @forelse($upcomingAppts as $appt)
            <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="rounded-2 text-center p-2" style="background:#eff6ff;min-width:52px">
                    <div class="fw-bold text-primary">{{ $appt->appointment_date->format('d') }}</div>
                    <div class="text-muted" style="font-size:.7rem">{{ $appt->appointment_date->format('M') }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small">Dr. {{ $appt->doctor->name }}</div>
                    <div class="text-muted small">
                        {{ $appt->appointment_date->format('H:i') }} — {{ $appt->doctor->speciality ?? 'Généraliste' }}
                    </div>
                </div>
                {!! $appt->status_badge !!}
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                <small>Aucun rendez-vous à venir</small><br>
                <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-primary mt-2">Prendre un RDV</a>
            </div>
            @endforelse
        </div>

        {{-- Recent consultations --}}
        <div class="table-card">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold mb-0"><i class="bi bi-clipboard2-pulse text-success me-2"></i>Mes dernières consultations</h6>
            </div>
            @forelse($consultations as $c)
            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold small">Dr. {{ $c->doctor->name }}</span>
                    <small class="text-muted">{{ $c->created_at->format('d/m/Y') }}</small>
                </div>
                <p class="small text-muted mb-1">{{ Str::limit($c->diagnosis, 80) }}</p>
                @if($c->prescription)
                <a href="{{ route('prescriptions.download', $c) }}" class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:.75rem">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Ordonnance PDF
                </a>
                @endif
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-clipboard2 fs-3 d-block mb-2"></i>
                <small>Aucune consultation enregistrée</small>
            </div>
            @endforelse
        </div>

    </div>
</div>
@endif
@endsection
