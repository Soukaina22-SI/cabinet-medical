{{-- resources/views/consultations/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouvelle Consultation')

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-light rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Nouvelle consultation</h5>
        <p class="text-muted small mb-0">
            Patient: <strong>{{ $appointment->patient->full_name }}</strong> —
            RDV: {{ $appointment->appointment_date->format('d/m/Y à H:i') }}
        </p>
    </div>
</div>

<form method="POST" action="{{ route('consultations.store', $appointment) }}" id="consultForm">
@csrf

{{-- Patient vitals --}}
<div class="stat-card mb-3">
    <h6 class="fw-semibold mb-3 text-success">
        <i class="bi bi-activity me-2"></i>Constantes vitales
    </h6>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Poids (kg)</label>
            <input type="number" name="weight" class="form-control" step="0.1"
                   placeholder="70.5" value="{{ old('weight') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Taille (cm)</label>
            <input type="number" name="height" class="form-control" step="0.1"
                   placeholder="175" value="{{ old('height') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Tension artérielle</label>
            <input type="text" name="blood_pressure" class="form-control"
                   placeholder="120/80" value="{{ old('blood_pressure') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Température (°C)</label>
            <input type="number" name="temperature" class="form-control" step="0.1"
                   placeholder="37.5" value="{{ old('temperature') }}">
        </div>
    </div>
</div>

{{-- Clinical notes --}}
<div class="stat-card mb-3">
    <h6 class="fw-semibold mb-3 text-primary">
        <i class="bi bi-clipboard2-pulse me-2"></i>Compte-rendu clinique
    </h6>
    <div class="mb-3">
        <label class="form-label small fw-semibold">Symptômes *</label>
        <textarea name="symptoms" class="form-control" rows="3" required
                  placeholder="Décrire les symptômes rapportés par le patient...">{{ old('symptoms') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label small fw-semibold">Diagnostic *</label>
        <textarea name="diagnosis" class="form-control" rows="3" required
                  placeholder="Diagnostic médical...">{{ old('diagnosis') }}</textarea>
    </div>
    <div>
        <label class="form-label small fw-semibold">Notes complémentaires</label>
        <textarea name="notes" class="form-control" rows="2"
                  placeholder="Observations, recommandations...">{{ old('notes') }}</textarea>
    </div>
</div>

{{-- Prescription --}}
<div class="stat-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-semibold mb-0 text-warning">
            <i class="bi bi-capsule me-2"></i>Ordonnance
        </h6>
        <button type="button" class="btn btn-sm btn-outline-warning" onclick="addMedication()">
            <i class="bi bi-plus-lg me-1"></i>Ajouter un médicament
        </button>
    </div>

    <div id="medicationsList">
        {{-- Medication rows added dynamically --}}
    </div>

    <p class="text-muted small text-center mt-2" id="noMedMsg">
        <i class="bi bi-info-circle me-1"></i>
        Cliquez sur "Ajouter un médicament" pour créer une ordonnance.
    </p>
</div>

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary px-4">Annuler</a>
    <button type="submit" class="btn btn-primary px-5">
        <i class="bi bi-check-lg me-1"></i>Enregistrer la consultation
    </button>
</div>

</form>
</div>
</div>
@endsection

@push('scripts')
<script>
let medCount = 0;

function addMedication() {
    document.getElementById('noMedMsg').style.display = 'none';
    const idx = medCount++;
    const div = document.createElement('div');
    div.className = 'border rounded-3 p-3 mb-3';
    div.id = `med_${idx}`;
    div.innerHTML = `
        <div class="d-flex justify-content-between mb-2">
            <span class="badge bg-warning-subtle text-warning fw-semibold">Médicament ${idx+1}</span>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMed(${idx})">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="medications[${idx}][name]" class="form-control form-control-sm"
                       placeholder="Nom du médicament *" required>
            </div>
            <div class="col-md-2">
                <input type="text" name="medications[${idx}][dosage]" class="form-control form-control-sm"
                       placeholder="Dose (ex: 500mg)" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="medications[${idx}][frequency]" class="form-control form-control-sm"
                       placeholder="Fréquence (ex: 3x/jour)" required>
            </div>
            <div class="col-md-3">
                <input type="text" name="medications[${idx}][duration]" class="form-control form-control-sm"
                       placeholder="Durée (ex: 7 jours)" required>
            </div>
            <div class="col-12">
                <input type="text" name="medications[${idx}][instructions]" class="form-control form-control-sm"
                       placeholder="Instructions particulières (optionnel)">
            </div>
        </div>
    `;
    document.getElementById('medicationsList').appendChild(div);
}

function removeMed(idx) {
    document.getElementById(`med_${idx}`)?.remove();
    if (document.getElementById('medicationsList').children.length === 0) {
        document.getElementById('noMedMsg').style.display = '';
    }
}
</script>
@endpush
