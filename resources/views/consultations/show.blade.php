{{-- resources/views/consultations/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Consultation #' . $consultation->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Rendez-vous</a></li>
    <li class="breadcrumb-item active">Consultation #{{ $consultation->id }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('appointments.show', $consultation->appointment) }}" class="btn btn-sm btn-light rounded-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0">Consultation #{{ str_pad($consultation->id, 5, '0', STR_PAD_LEFT) }}</h5>
            <p class="text-muted small mb-0">
                {{ $consultation->created_at->translatedFormat('l d F Y') }} —
                Dr. {{ $consultation->doctor?->name ?? '—' }}
            </p>
        </div>
    </div>
    @if($consultation->prescription)
    <a href="{{ route('prescriptions.download', $consultation) }}" class="btn btn-danger rounded-3">
        <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger Ordonnance PDF
    </a>
    @endif
</div>

<div class="row g-3">

    <div class="col-lg-4">
        {{-- Patient info --}}
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3 text-primary"><i class="bi bi-person-circle me-2"></i>Patient</h6>
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                     style="width:44px;height:44px;background:#dbeafe;color:#1d4ed8">
                    {{ $consultation->patient ? strtoupper(substr($consultation->patient?->first_name ?? '?',0,1).substr($consultation->patient?->last_name ?? '',0,1)) : '?' }}
                </div>
                <div>
                    <div class="fw-bold">{{ $consultation->patient?->full_name ?? 'Patient inconnu' }}</div>
                    <small class="text-muted">{{ $consultation->patient->age }} ans</small>
                </div>
            </div>
            @if($consultation->patient->allergies)
            <div class="alert alert-warning py-2 small mb-0">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>Allergies:</strong> {{ $consultation->patient->allergies }}
            </div>
            @endif
        </div>

        {{-- Vitals --}}
        @if($consultation->weight || $consultation->blood_pressure || $consultation->temperature)
        <div class="stat-card">
            <h6 class="fw-semibold mb-3 text-success"><i class="bi bi-activity me-2"></i>Constantes vitales</h6>
            <div class="row g-2">
                @if($consultation->weight)
                <div class="col-6 text-center border rounded-3 py-2">
                    <div class="fw-bold">{{ $consultation->weight }} kg</div>
                    <div class="text-muted" style="font-size:.72rem">Poids</div>
                </div>
                @endif
                @if($consultation->height)
                <div class="col-6 text-center border rounded-3 py-2">
                    <div class="fw-bold">{{ $consultation->height }} cm</div>
                    <div class="text-muted" style="font-size:.72rem">Taille</div>
                </div>
                @endif
                @if($consultation->blood_pressure)
                <div class="col-6 text-center border rounded-3 py-2">
                    <div class="fw-bold">{{ $consultation->blood_pressure }}</div>
                    <div class="text-muted" style="font-size:.72rem">Tension</div>
                </div>
                @endif
                @if($consultation->temperature)
                <div class="col-6 text-center border rounded-3 py-2">
                    <div class="fw-bold {{ $consultation->temperature > 38 ? 'text-danger' : '' }}">
                        {{ $consultation->temperature }}°C
                    </div>
                    <div class="text-muted" style="font-size:.72rem">Température</div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-8">

        {{-- Clinical report --}}
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3 text-primary">
                <i class="bi bi-clipboard2-pulse me-2"></i>Compte-rendu clinique
            </h6>

            <div class="mb-4">
                <p class="text-muted small text-uppercase fw-semibold mb-1" style="font-size:.7rem;letter-spacing:.08em">
                    Symptômes présentés
                </p>
                <div class="border-start border-3 border-warning ps-3">
                    {{ $consultation->symptoms }}
                </div>
            </div>

            <div class="mb-4">
                <p class="text-muted small text-uppercase fw-semibold mb-1" style="font-size:.7rem;letter-spacing:.08em">
                    Diagnostic
                </p>
                <div class="border-start border-3 border-success ps-3 fw-semibold">
                    {{ $consultation->diagnosis }}
                </div>
            </div>

            @if($consultation->notes)
            <div>
                <p class="text-muted small text-uppercase fw-semibold mb-1" style="font-size:.7rem;letter-spacing:.08em">
                    Notes & Recommandations
                </p>
                <div class="border-start border-3 border-info ps-3 fst-italic text-muted">
                    {{ $consultation->notes }}
                </div>
            </div>
            @endif
        </div>

        {{-- Prescription --}}
        @if($consultation->prescription && $consultation->prescription->items->count())
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold mb-0 text-warning">
                    <i class="bi bi-capsule me-2"></i>Ordonnance médicale
                </h6>
                <a href="{{ route('prescriptions.download', $consultation) }}"
                   class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
            </div>

            <div class="table-responsive">
                <table class="table small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Médicament</th>
                            <th>Dosage</th>
                            <th>Fréquence</th>
                            <th>Durée</th>
                            <th>Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consultation->prescription->items as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->medication_name }}</td>
                            <td><span class="badge bg-light text-dark">{{ $item->dosage }}</span></td>
                            <td>{{ $item->frequency }}</td>
                            <td>{{ $item->duration }}</td>
                            <td class="text-muted fst-italic">{{ $item->instructions ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
