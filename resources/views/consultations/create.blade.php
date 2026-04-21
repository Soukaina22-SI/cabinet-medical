
{{-- ============================================================ --}}
{{-- resources/views/consultations/create.blade.php --}}
{{-- ============================================================ --}}
@extends('layouts.app')
@section('title', 'Nouvelle Consultation')
@section('content')
<div class="d-flex justify-content-between mb-4">
    <h4 class="fw-bold"><i class="bi bi-clipboard2-pulse text-primary me-2"></i>Nouvelle Consultation</h4>
</div>
 
<div class="row g-3">
    {{-- Infos patient --}}
    <div class="col-md-4">
        <div class="card p-3">
            <h6 class="fw-bold border-bottom pb-2 mb-3">Patient</h6>
            <p class="mb-1"><strong>{{ $rendezVous->patient->user->nom_complet }}</strong></p>
            <p class="mb-1 text-muted small">{{ $rendezVous->patient->age }} ans</p>
            <p class="mb-1 text-muted small">{{ $rendezVous->motif }}</p>
            @if($rendezVous->patient->dossierMedical)
                <div class="mt-3">
                    <span class="badge bg-warning-subtle text-warning-emphasis">
                        Allergies : {{ $rendezVous->patient->dossierMedical->allergies ?? 'Aucune' }}
                    </span>
                </div>
            @endif
        </div>
    </div>
 
    {{-- Formulaire consultation --}}
    <div class="col-md-8">
        <div class="card p-4">
            <form method="POST" action="{{ route('consultations.store', $rendezVous) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Diagnostic</label>
                    <textarea name="diagnostic" class="form-control" rows="3" required
                              placeholder="Diagnostic médical..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Compte rendu</label>
                    <textarea name="compte_rendu" class="form-control" rows="4" required
                              placeholder="Observations, examens, conclusions..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Honoraires (MAD)</label>
                    <input type="number" name="prix" class="form-control" min="0" step="0.01" required value="200">
                </div>
 
                {{-- Ordonnance --}}
                <div class="card bg-light p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0">Ordonnance</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="ajouterMedicament()">
                            <i class="bi bi-plus-circle me-1"></i>Ajouter médicament
                        </button>
                    </div>
                    <div id="medicaments-container"></div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Instructions générales</label>
                        <textarea name="instructions" class="form-control form-control-sm" rows="2"
                                  placeholder="Conseils, précautions..."></textarea>
                    </div>
                </div>
 
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check2 me-2"></i>Enregistrer la consultation
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
let idx = 0;
function ajouterMedicament() {
    const container = document.getElementById('medicaments-container');
    const div = document.createElement('div');
    div.className = 'border rounded p-2 mb-2 bg-white';
    div.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">Médicament</label>
                <input type="text" name="medicaments[${idx}][nom]" class="form-control form-control-sm" required placeholder="Ex: Amoxicilline">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Dosage</label>
                <input type="text" name="medicaments[${idx}][dosage]" class="form-control form-control-sm" required placeholder="500mg">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Durée</label>
                <input type="text" name="medicaments[${idx}][duree]" class="form-control form-control-sm" required placeholder="7 jours">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Posologie</label>
                <input type="text" name="medicaments[${idx}][posologie]" class="form-control form-control-sm" required placeholder="3x/jour">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest('.border').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>`;
    container.appendChild(div);
    idx++;
}
</script>
@endpush
 
 