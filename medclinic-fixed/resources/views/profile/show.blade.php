{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Mon Profil')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mon Profil</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-8">

<h5 class="fw-bold mb-4">Mon Profil</h5>

<div class="row g-4">

    {{-- Left: Avatar & Info --}}
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $user->avatar_url }}" class="rounded-circle border border-4 border-white shadow"
                     width="100" height="100" id="avatarPreview" alt="avatar">
                <label for="avatarInput"
                       class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle p-1"
                       style="width:28px;height:28px;cursor:pointer" title="Changer la photo">
                    <i class="bi bi-camera-fill" style="font-size:.7rem"></i>
                </label>
            </div>
            <h6 class="fw-bold mb-0">{{ $user->name }}</h6>
            <p class="text-muted small mb-2">{{ $user->email }}</p>
            @php
                $roleLabels = ['admin'=>['Admin','danger'],'medecin'=>['Médecin','success'],
                               'secretaire'=>['Secrétaire','info'],'patient'=>['Patient','secondary']];
                [$rLabel,$rColor] = $roleLabels[$user->role] ?? [$user->role,'secondary'];
            @endphp
            <span class="badge bg-{{ $rColor }}-subtle text-{{ $rColor }} border border-{{ $rColor }}-subtle">
                {{ $rLabel }}
            </span>

            @if($user->isDoctor())
                <p class="text-muted small mt-2 mb-0">{{ $user->speciality ?? 'Spécialité non définie' }}</p>
            @endif

            <hr>
            <div class="row text-center g-0">
                @if($user->isDoctor())
                <div class="col-6 border-end">
                    <div class="fw-bold text-primary">{{ $user->appointments()->count() }}</div>
                    <div class="text-muted" style="font-size:.72rem">RDV total</div>
                </div>
                <div class="col-6">
                    <div class="fw-bold text-success">{{ $user->consultations()->count() }}</div>
                    <div class="text-muted" style="font-size:.72rem">Consultations</div>
                </div>
                @else
                <div class="col-12">
                    <div class="fw-bold text-primary">{{ $user->created_at->format('d/m/Y') }}</div>
                    <div class="text-muted" style="font-size:.72rem">Membre depuis</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right: Edit Forms --}}
    <div class="col-md-8">

        {{-- Info form --}}
        <div class="stat-card mb-4">
            <h6 class="fw-semibold mb-3 text-primary">
                <i class="bi bi-person-circle me-2"></i>Informations personnelles
            </h6>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                @csrf @method('PUT')

                {{-- Hidden avatar input --}}
                <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*"
                       onchange="previewAvatar(this)">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nom complet *</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                        <div class="form-text text-muted">L'email ne peut pas être modifié.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Téléphone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}" placeholder="+212 6XX XXX XXX">
                    </div>
                    @if($user->isDoctor())
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Spécialité</label>
                        <select name="speciality" class="form-select">
                            @foreach(['Médecine Générale','Cardiologie','Pédiatrie','Dermatologie',
                                      'Gynécologie','Orthopédie','Neurologie','Ophtalmologie','ORL','Radiologie'] as $s)
                            <option value="{{ $s }}" {{ $user->speciality === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>

        {{-- Password form --}}
        <div class="stat-card">
            <h6 class="fw-semibold mb-3 text-warning">
                <i class="bi bi-lock me-2"></i>Changer le mot de passe
            </h6>

            @if($errors->has('current_password'))
                <div class="alert alert-danger py-2 small">{{ $errors->first('current_password') }}</div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Min. 8 caractères" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Confirmer</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-warning px-4 text-white">
                        <i class="bi bi-shield-lock me-1"></i>Mettre à jour
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
        // Auto-submit form after selection
        document.getElementById('profileForm').submit();
    }
}
</script>
@endpush
