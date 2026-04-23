{{-- resources/views/admin/users/form.blade.php --}}
@extends('layouts.app')
@section('title', isset($user) ? 'Modifier Utilisateur' : 'Nouvel Utilisateur')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
    <li class="breadcrumb-item active">{{ isset($user) ? 'Modifier' : 'Nouveau' }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-7">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-light rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">{{ isset($user) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}</h5>
        <p class="text-muted small mb-0">{{ isset($user) ? $user->name . ' — ' . $user->email : 'Remplir le formulaire' }}</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST"
      action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if(isset($user)) @method('PUT') @endif

    <div class="stat-card mb-3">
        <h6 class="fw-semibold mb-3 text-primary">
            <i class="bi bi-person me-2"></i>Informations générales
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Nom complet *</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $user->name ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Email *</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $user->email ?? '') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Rôle *</label>
                <select name="role" id="roleSelect" class="form-select" required>
                    <option value="">Sélectionner un rôle</option>
                    <option value="admin"      {{ old('role', $user->role ?? '') === 'admin'      ? 'selected':'' }}>👨‍💻 Admin</option>
                    <option value="medecin"    {{ old('role', $user->role ?? '') === 'medecin'    ? 'selected':'' }}>👨‍⚕️ Médecin</option>
                    <option value="secretaire" {{ old('role', $user->role ?? '') === 'secretaire' ? 'selected':'' }}>🧑‍💼 Secrétaire</option>
                    <option value="patient"    {{ old('role', $user->role ?? '') === 'patient'    ? 'selected':'' }}>👤 Patient</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Téléphone</label>
                <input type="text" name="phone" class="form-control"
                       value="{{ old('phone', $user->phone ?? '') }}">
            </div>

            {{-- Speciality field (only for doctors) --}}
            <div class="col-12" id="specialityField"
                 style="{{ old('role', $user->role ?? '') === 'medecin' ? '' : 'display:none' }}">
                <label class="form-label small fw-semibold">Spécialité *</label>
                <select name="speciality" class="form-select">
                    <option value="">Sélectionner</option>
                    @foreach(['Médecine Générale','Cardiologie','Pédiatrie','Dermatologie','Gynécologie','Orthopédie','Neurologie','Ophtalmologie','ORL','Radiologie'] as $spec)
                    <option value="{{ $spec }}" {{ old('speciality', $user->speciality ?? '') === $spec ? 'selected':'' }}>
                        {{ $spec }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="stat-card mb-4">
        <h6 class="fw-semibold mb-3 text-warning">
            <i class="bi bi-lock me-2"></i>{{ isset($user) ? 'Changer le mot de passe (optionnel)' : 'Mot de passe *' }}
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">
                    Mot de passe {{ !isset($user) ? '*' : '' }}
                </label>
                <input type="password" name="password" class="form-control"
                       {{ !isset($user) ? 'required' : '' }}
                       placeholder="{{ isset($user) ? 'Laisser vide pour ne pas changer' : '' }}">
                <div class="form-text">Minimum 8 caractères</div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
        <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-check-lg me-1"></i>
            {{ isset($user) ? 'Mettre à jour' : 'Créer l\'utilisateur' }}
        </button>
    </div>

</form>
</div>
</div>

@push('scripts')
<script>
document.getElementById('roleSelect').addEventListener('change', function() {
    document.getElementById('specialityField').style.display =
        this.value === 'medecin' ? '' : 'none';
});
</script>
@endpush
@endsection
