{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')
@section('title', 'Inscription')
@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light py-4">
    <div class="card p-4 shadow-sm" style="width: 520px; border-radius: 16px;">

        {{-- Header --}}
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:64px;height:64px;">
                <i class="bi bi-person-plus-fill text-primary fs-3"></i>
            </div>
            <h4 class="fw-bold mb-1">Créer un compte patient</h4>
            <p class="text-muted small mb-0">Remplissez le formulaire pour accéder à votre espace</p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0 ps-3 mt-1">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            {{-- Section : Identité --}}
            <p class="text-uppercase fw-semibold text-muted small mb-2" style="letter-spacing:.06em">
                <i class="bi bi-person me-1"></i> Identité
            </p>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small mb-1">Prénom <span class="text-danger">*</span></label>
                    <input type="text"
                           name="prenom"
                           class="form-control @error('prenom') is-invalid @enderror"
                           value="{{ old('prenom') }}"
                           placeholder="Mohammed"
                           required autofocus>
                    @error('prenom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small mb-1">Nom <span class="text-danger">*</span></label>
                    <input type="text"
                           name="nom"
                           class="form-control @error('nom') is-invalid @enderror"
                           value="{{ old('nom') }}"
                           placeholder="El Idrissi"
                           required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small mb-1">Date de naissance <span class="text-danger">*</span></label>
                    <input type="date"
                           name="date_naissance"
                           class="form-control @error('date_naissance') is-invalid @enderror"
                           value="{{ old('date_naissance') }}"
                           max="{{ now()->subYears(1)->format('Y-m-d') }}"
                           required>
                    @error('date_naissance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small mb-1">Sexe <span class="text-danger">*</span></label>
                    <select name="sexe" class="form-select @error('sexe') is-invalid @enderror" required>
                        <option value="">-- Choisir --</option>
                        <option value="M" {{ old('sexe') === 'M' ? 'selected' : '' }}>Masculin</option>
                        <option value="F" {{ old('sexe') === 'F' ? 'selected' : '' }}>Féminin</option>
                    </select>
                    @error('sexe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small mb-1">Téléphone</label>
                <div class="input-group">
                    <span class="input-group-text text-muted small">
                        <i class="bi bi-telephone"></i>
                    </span>
                    <input type="tel"
                           name="telephone"
                           class="form-control @error('telephone') is-invalid @enderror"
                           value="{{ old('telephone') }}"
                           placeholder="06 XX XX XX XX">
                    @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr class="my-3">

            {{-- Section : Connexion --}}
            <p class="text-uppercase fw-semibold text-muted small mb-2" style="letter-spacing:.06em">
                <i class="bi bi-shield-lock me-1"></i> Accès au compte
            </p>

            <div class="mb-3">
                <label class="form-label fw-semibold small mb-1">Adresse email <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted small">
                        <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="votre@email.com"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label fw-semibold small mb-1">Mot de passe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               name="password"
                               id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="8 caractères min."
                               required minlength="8">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePwd('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Force du mot de passe --}}
                    <div class="mt-1" id="strength-bar" style="display:none">
                        <div class="progress" style="height:4px">
                            <div id="strength-fill" class="progress-bar" style="width:0;transition:width .3s"></div>
                        </div>
                        <small id="strength-label" class="text-muted" style="font-size:11px"></small>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small mb-1">Confirmer <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               class="form-control"
                               placeholder="Répéter le mot de passe"
                               required>
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="togglePwd('password_confirmation', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small id="match-msg" class="small mt-1" style="display:none"></small>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" id="btnSubmit">
                <i class="bi bi-person-check me-2"></i>Créer mon compte
            </button>
        </form>

        <hr class="my-3">
        <p class="text-center small mb-0">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="text-primary fw-semibold">Se connecter</a>
        </p>

    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Afficher / masquer mot de passe ──────────────────────
function togglePwd(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// ── Force du mot de passe ────────────────────────────────
const pwdInput  = document.getElementById('password');
const pwdConf   = document.getElementById('password_confirmation');
const bar       = document.getElementById('strength-bar');
const fill      = document.getElementById('strength-fill');
const label     = document.getElementById('strength-label');
const matchMsg  = document.getElementById('match-msg');

pwdInput.addEventListener('input', function () {
    const val = this.value;
    bar.style.display = val.length ? 'block' : 'none';

    let score = 0;
    if (val.length >= 8)              score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const levels = [
        { pct:  25, cls: 'bg-danger',  txt: 'Faible' },
        { pct:  50, cls: 'bg-warning', txt: 'Moyen' },
        { pct:  75, cls: 'bg-info',    txt: 'Bon' },
        { pct: 100, cls: 'bg-success', txt: 'Excellent' },
    ];
    const lvl = levels[score - 1] || levels[0];
    fill.style.width    = lvl.pct + '%';
    fill.className      = 'progress-bar ' + lvl.cls;
    label.textContent   = lvl.txt;
    label.style.color   = '';

    checkMatch();
});

pwdConf.addEventListener('input', checkMatch);

function checkMatch() {
    if (!pwdConf.value) { matchMsg.style.display = 'none'; return; }
    matchMsg.style.display = 'inline';
    if (pwdInput.value === pwdConf.value) {
        matchMsg.textContent  = '✓ Mots de passe identiques';
        matchMsg.className    = 'small mt-1 text-success';
        pwdConf.classList.remove('is-invalid');
        pwdConf.classList.add('is-valid');
    } else {
        matchMsg.textContent  = '✗ Les mots de passe ne correspondent pas';
        matchMsg.className    = 'small mt-1 text-danger';
        pwdConf.classList.remove('is-valid');
        pwdConf.classList.add('is-invalid');
    }
}
</script>
@endpush