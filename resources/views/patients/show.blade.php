{{-- resources/views/admin/patients/show.blade.php --}}
@extends('layouts.app')
@section('title', $patient->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
    <li class="breadcrumb-item active">{{ $patient->full_name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('patients.index') }}" class="btn btn-sm btn-light rounded-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4"
             style="width:56px;height:56px;background:{{ $patient->gender==='male'?'#dbeafe':'#fce7f3' }};
                    color:{{ $patient->gender==='male'?'#1d4ed8':'#be185d' }}">
            {{ strtoupper(substr($patient->first_name,0,1).substr($patient->last_name,0,1)) }}
        </div>
        <div>
            <h5 class="fw-bold mb-0">{{ $patient->full_name }}</h5>
            <p class="text-muted small mb-0">
                {{ $patient->age }} ans •
                {{ $patient->gender === 'male' ? 'Masculin' : 'Féminin' }} •
                CIN: {{ $patient->cin ?? '—' }}
            </p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('appointments.create') }}?patient_id={{ $patient->id }}"
           class="btn btn-success rounded-3">
            <i class="bi bi-calendar-plus me-1"></i> Nouveau RDV
        </a>
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline-primary rounded-3">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
    </div>
</div>

<div class="row g-3">

    {{-- Patient Info Card --}}
    <div class="col-lg-4">
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3 text-primary"><i class="bi bi-person-circle me-2"></i>Informations</h6>
            <table class="table table-sm table-borderless small">
                <tr><td class="text-muted" width="40%">Téléphone</td><td class="fw-semibold">{{ $patient->phone }}</td></tr>
                <tr><td class="text-muted">Email</td><td>{{ $patient->email ?? '—' }}</td></tr>
                <tr><td class="text-muted">Naissance</td><td>{{ $patient->date_of_birth->format('d/m/Y') }}</td></tr>
                <tr><td class="text-muted">Adresse</td><td>{{ $patient->address ?? '—' }}</td></tr>
            </table>
        </div>

        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3 text-success"><i class="bi bi-heart-pulse me-2"></i>Médical</h6>
            <table class="table table-sm table-borderless small">
                <tr>
                    <td class="text-muted" width="40%">Groupe sanguin</td>
                    <td>
                        @if($patient->blood_type)
                            <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                        @else — @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Allergies</td>
                    <td>{{ $patient->allergies ?? '—' }}</td>
                </tr>
            </table>
            @if($patient->medical_notes)
                <div class="bg-light rounded p-2 small mt-2">{{ $patient->medical_notes }}</div>
            @endif
        </div>

        {{-- Quick stats --}}
        <div class="row g-2">
            <div class="col-4 text-center stat-card py-3">
                <div class="fw-bold fs-4 text-primary">{{ $patient->appointments->count() }}</div>
                <div class="text-muted" style="font-size:.72rem">RDV</div>
            </div>
            <div class="col-4 text-center stat-card py-3">
                <div class="fw-bold fs-4 text-success">{{ $patient->consultations->count() }}</div>
                <div class="text-muted" style="font-size:.72rem">Consult.</div>
            </div>
            <div class="col-4 text-center stat-card py-3">
                <div class="fw-bold fs-4 text-warning">{{ $patient->prescriptions->count() }}</div>
                <div class="text-muted" style="font-size:.72rem">Ordosc.</div>
            </div>
        </div>
    </div>

    {{-- History Tabs --}}
    <div class="col-lg-8">
        <div class="table-card">
            <ul class="nav nav-tabs px-3 pt-2" id="patientTabs">
                <li class="nav-item">
                    <a class="nav-link active small" data-bs-toggle="tab" href="#tab-rdv">
                        <i class="bi bi-calendar3 me-1"></i>Rendez-vous
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link small" data-bs-toggle="tab" href="#tab-consult">
                        <i class="bi bi-clipboard2-pulse me-1"></i>Consultations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link small" data-bs-toggle="tab" href="#tab-rx">
                        <i class="bi bi-file-earmark-medical me-1"></i>Ordonnances
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">

                {{-- Appointments tab --}}
                <div class="tab-pane fade show active" id="tab-rdv">
                    @forelse($patient->appointments->sortByDesc('appointment_date') as $appt)
                    <div class="d-flex align-items-start gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="rounded-2 text-center px-2 py-1" style="background:#f1f5f9;min-width:48px">
                            <div class="fw-bold" style="font-size:1.1rem;line-height:1">
                                {{ $appt->appointment_date->format('d') }}
                            </div>
                            <div class="text-muted" style="font-size:.7rem">
                                {{ $appt->appointment_date->format('M') }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold small">Dr. {{ $appt->doctor?->name ?? '—' }}</span>
                                {!! $appt->status_badge !!}
                            </div>
                            <div class="text-muted small">{{ $appt->appointment_date->format('H:i') }} — {{ $appt->reason ?? 'Consultation générale' }}</div>
                        </div>
                        <a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-light">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-center text-muted py-4">Aucun rendez-vous</p>
                    @endforelse
                </div>

                {{-- Consultations tab --}}
                <div class="tab-pane fade" id="tab-consult">
                    @forelse($patient->consultations->sortByDesc('created_at') as $consult)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold small">Dr. {{ $consult->doctor?->name ?? '—' }}</span>
                            <small class="text-muted">{{ $consult->created_at->format('d/m/Y') }}</small>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-md-6">
                                <strong class="text-muted d-block" style="font-size:.72rem;text-transform:uppercase">Symptômes</strong>
                                {{ $consult->symptoms }}
                            </div>
                            <div class="col-md-6">
                                <strong class="text-muted d-block" style="font-size:.72rem;text-transform:uppercase">Diagnostic</strong>
                                {{ $consult->diagnosis }}
                            </div>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('consultations.show', $consult) }}" class="btn btn-sm btn-outline-primary">
                                Voir détails <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            @if($consult->prescription)
                            <a href="{{ route('prescriptions.download', $consult) }}" class="btn btn-sm btn-outline-success ms-2">
                                <i class="bi bi-file-earmark-pdf me-1"></i>Ordonnance PDF
                            </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-4">Aucune consultation</p>
                    @endforelse
                </div>

                {{-- Prescriptions tab --}}
                <div class="tab-pane fade" id="tab-rx">
                    @forelse($patient->prescriptions->sortByDesc('created_at') as $rx)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small">Ordonnance #{{ $rx->id }}</span>
                            <small class="text-muted">{{ $rx->created_at->format('d/m/Y') }}</small>
                        </div>
                        <ul class="small mb-2">
                            @foreach($rx->items as $item)
                            <li><strong>{{ $item->medication_name }}</strong> — {{ $item->dosage }}, {{ $item->frequency }}, {{ $item->duration }}</li>
                            @endforeach
                        </ul>
                        @if($rx->pdf_path)
                        <a href="{{ route('prescriptions.download', $rx->consultation) }}"
                           class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger PDF
                        </a>
                        @endif
                    </div>
                    @empty
                    <p class="text-center text-muted py-4">Aucune ordonnance</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
