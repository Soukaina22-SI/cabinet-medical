@extends('layouts.app')
@section('title', 'Nouveau Patient')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
            <li class="breadcrumb-item active">Nouveau patient</li>
        </ol>
    </nav>
    <h5 class="fw-bold mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Ajouter un nouveau patient</h5>
</div>

@if($errors->any())
<div class="alert alert-danger rounded-3 mb-4">
    <div class="d-flex gap-2 align-items-center mb-1">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <strong>Veuillez corriger les erreurs :</strong>
    </div>
    <ul class="mb-0 ps-3 small">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('patients.store') }}">
@csrf

<div class="row g-3">
    {{-- Colonne principale --}}
    <div class="col-lg-8">

        {{-- Identité --}}
        <div class="table-card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom">
                <i class="bi bi-person me-2 text-primary"></i>Identité
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Prénom <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           class="form-control @error('first_name') is-invalid @enderror"
                           placeholder="Prénom" required autofocus>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Nom <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="form-control @error('last_name') is-invalid @enderror"
                           placeholder="Nom de famille" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Date de naissance <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                           class="form-control @error('date_of_birth') is-invalid @enderror"
                           max="{{ date('Y-m-d', strtotime('-1 year')) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Genre <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>👨 Masculin</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>👩 Féminin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">CIN</label>
                    <input type="text" name="cin" value="{{ old('cin') }}"
                           class="form-control @error('cin') is-invalid @enderror"
                           placeholder="Ex: AB123456">
                    @error('cin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Adresse</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                           class="form-control" placeholder="Adresse complète">
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="table-card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom">
                <i class="bi bi-telephone me-2 text-success"></i>Contact
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Téléphone <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-phone text-muted"></i></span>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="0612 345 678" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="email@exemple.com">
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes médicales --}}
        <div class="table-card p-4">
            <h6 class="fw-bold mb-3 pb-2 border-bottom">
                <i class="bi bi-journal-medical me-2 text-warning"></i>Notes médicales
            </h6>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold small">Allergies connues</label>
                    <input type="text" name="allergies" value="{{ old('allergies') }}"
                           class="form-control" placeholder="Ex: Pénicilline, Aspirine...">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Antécédents / Notes</label>
                    <textarea name="medical_notes" rows="3" class="form-control"
                              placeholder="Antécédents médicaux, chirurgicaux, familiaux...">{{ old('medical_notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Colonne droite --}}
    <div class="col-lg-4">
        <div class="table-card p-4 mb-3">
            <h6 class="fw-bold mb-3 pb-2 border-bottom">
                <i class="bi bi-heart-pulse me-2 text-danger"></i>Informations médicales
            </h6>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Groupe sanguin</label>
                <select name="blood_type" class="form-select">
                    <option value="">-- Non renseigné --</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                        <option value="{{ $bt }}" {{ old('blood_type') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Info card --}}
        <div class="rounded-3 p-3" style="background:#eff6ff;border:1px solid #bfdbfe">
            <div class="d-flex gap-2">
                <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                <div class="small text-primary">
                    <strong>Rôle : {{ ucfirst(auth()->user()->role) }}</strong><br>
                    <span class="text-muted">Vous pouvez créer et modifier des dossiers patients.</span>
                    @if(!auth()->user()->isAdmin())
                    <br><span class="text-muted">La suppression est réservée à l'Admin.</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-3 d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-check me-2"></i>Créer le dossier patient
            </button>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Annuler
            </a>
        </div>
    </div>
</div>
</form>
@endsection
