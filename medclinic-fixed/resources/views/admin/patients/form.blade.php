{{-- resources/views/admin/patients/form.blade.php (shared by create & edit) --}}
@extends('layouts.app')
@section('title', isset($patient) ? 'Modifier Patient' : 'Nouveau Patient')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Patients</a></li>
    <li class="breadcrumb-item active">{{ isset($patient) ? 'Modifier' : 'Nouveau' }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.patients.index') }}" class="btn btn-sm btn-light rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">{{ isset($patient) ? 'Modifier le patient' : 'Nouveau patient' }}</h5>
        <p class="text-muted small mb-0">{{ isset($patient) ? $patient->full_name : 'Remplir le formulaire ci-dessous' }}</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST"
      action="{{ isset($patient) ? route('admin.patients.update', $patient) : route('admin.patients.store') }}">
    @csrf
    @if(isset($patient)) @method('PUT') @endif

    {{-- Informations personnelles --}}
    <div class="stat-card mb-4">
        <h6 class="fw-semibold mb-3 text-primary">
            <i class="bi bi-person-lines-fill me-2"></i>Informations personnelles
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Prénom *</label>
                <input type="text" name="first_name" class="form-control"
                       value="{{ old('first_name', $patient->first_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nom *</label>
                <input type="text" name="last_name" class="form-control"
                       value="{{ old('last_name', $patient->last_name ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">CIN</label>
                <input type="text" name="cin" class="form-control"
                       value="{{ old('cin', $patient->cin ?? '') }}" placeholder="AB123456">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Téléphone *</label>
                <input type="text" name="phone" class="form-control"
                       value="{{ old('phone', $patient->phone ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $patient->email ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Date de naissance *</label>
                <input type="date" name="date_of_birth" class="form-control"
                       value="{{ old('date_of_birth', isset($patient) ? $patient->date_of_birth->format('Y-m-d') : '') }}"
                       required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Genre *</label>
                <select name="gender" class="form-select" required>
                    <option value="">Sélectionner</option>
                    <option value="male"   {{ old('gender', $patient->gender ?? '') === 'male'   ? 'selected' : '' }}>Masculin</option>
                    <option value="female" {{ old('gender', $patient->gender ?? '') === 'female' ? 'selected' : '' }}>Féminin</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Adresse</label>
                <textarea name="address" class="form-control" rows="2"
                          placeholder="Adresse complète...">{{ old('address', $patient->address ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Informations médicales --}}
    <div class="stat-card mb-4">
        <h6 class="fw-semibold mb-3 text-success">
            <i class="bi bi-heart-pulse me-2"></i>Informations médicales
        </h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Groupe sanguin</label>
                <select name="blood_type" class="form-select">
                    <option value="">—</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                        <option value="{{ $bt }}" {{ old('blood_type', $patient->blood_type ?? '') === $bt ? 'selected' : '' }}>
                            {{ $bt }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-9">
                <label class="form-label small fw-semibold">Allergies</label>
                <input type="text" name="allergies" class="form-control"
                       value="{{ old('allergies', $patient->allergies ?? '') }}"
                       placeholder="Pénicilline, arachides...">
            </div>
            <div class="col-12">
                <label class="form-label small fw-semibold">Notes médicales</label>
                <textarea name="medical_notes" class="form-control" rows="3"
                          placeholder="Antécédents, maladies chroniques...">{{ old('medical_notes', $patient->medical_notes ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-check-lg me-1"></i>
            {{ isset($patient) ? 'Mettre à jour' : 'Créer le patient' }}
        </button>
    </div>

</form>
</div>
</div>
@endsection
