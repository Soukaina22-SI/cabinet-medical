{{-- resources/views/appointments/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Modifier Rendez-vous #' . $appointment->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Rendez-vous</a></li>
    <li class="breadcrumb-item"><a href="{{ route('appointments.show', $appointment) }}">#{{ $appointment->id }}</a></li>
    <li class="breadcrumb-item active">Modifier</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-7">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-sm btn-light rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Modifier le rendez-vous</h5>
        <p class="text-muted small mb-0">
            {{ $appointment->patient->full_name }} — Dr. {{ $appointment->doctor->name }}
        </p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('appointments.update', $appointment) }}">
@csrf
@method('PUT')

{{-- Patient & Doctor (read-only) --}}
<div class="stat-card mb-3" style="background:#f8fafc">
    <h6 class="fw-semibold mb-3 text-muted"><i class="bi bi-lock me-2"></i>Informations fixes</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-muted">Patient</label>
            <input type="text" class="form-control bg-white" value="{{ $appointment->patient->full_name }}" disabled>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-muted">Médecin</label>
            <input type="text" class="form-control bg-white" value="Dr. {{ $appointment->doctor->name }}" disabled>
        </div>
    </div>
</div>

{{-- Editable fields --}}
<div class="stat-card mb-3">
    <h6 class="fw-semibold mb-3 text-primary"><i class="bi bi-calendar3 me-2"></i>Date & Statut</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Date et heure *</label>
            <input type="datetime-local" name="appointment_date" class="form-control"
                   value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d\TH:i')) }}"
                   required>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Statut *</label>
            <select name="status" class="form-select" required>
                @foreach(['pending' => '⏳ En attente', 'confirmed' => '✅ Confirmé', 'cancelled' => '❌ Annulé', 'completed' => '✔ Terminé'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $appointment->status) === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="stat-card mb-4">
    <h6 class="fw-semibold mb-3"><i class="bi bi-chat-square-text me-2"></i>Informations complémentaires</h6>
    <div class="mb-3">
        <label class="form-label small fw-semibold">Motif de la consultation</label>
        <textarea name="reason" class="form-control" rows="2">{{ old('reason', $appointment->reason) }}</textarea>
    </div>
    <div>
        <label class="form-label small fw-semibold">Notes internes</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $appointment->notes) }}</textarea>
    </div>
</div>

{{-- Danger zone --}}
@if(!$appointment->isCancelled() && !$appointment->isCompleted())
<div class="stat-card mb-4 border border-danger border-opacity-25">
    <h6 class="fw-semibold mb-2 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Zone de danger</h6>
    <p class="small text-muted mb-3">L'annulation est irréversible. Le patient sera notifié.</p>
    <form method="POST"
          action="{{ route('appointments.update', $appointment) }}"
          onsubmit="return confirm('Confirmer l\'annulation de ce rendez-vous ?')">
        @csrf @method('PUT')
        <input type="hidden" name="appointment_date" value="{{ $appointment->appointment_date->format('Y-m-d H:i:s') }}">
        <input type="hidden" name="status" value="cancelled">
        <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-x-circle me-1"></i>Annuler ce rendez-vous
        </button>
    </form>
</div>
@endif

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-outline-secondary px-4">Annuler</a>
    <button type="submit" class="btn btn-primary px-5">
        <i class="bi bi-check-lg me-1"></i>Enregistrer les modifications
    </button>
</div>

</form>
</div>
</div>
@endsection
