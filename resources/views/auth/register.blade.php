<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Patient — MedClinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .register-card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
            max-width: 700px;
            margin: 0 auto;
        }
        .brand-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: .875rem;
            display: flex; align-items: center; justify-content: center;
        }
        .form-control, .form-select {
            border-radius: .65rem;
            border: 1.5px solid #e2e8f0;
            font-size: .9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        .form-label { font-size: .83rem; font-weight: 600; color: #475569; margin-bottom: .3rem; }
        .section-header {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #64748b;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: .6rem;
            margin-bottom: 1.25rem;
        }
        .step-num {
            width: 24px; height: 24px;
            background: #3b82f6;
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem;
            font-weight: 800;
            flex-shrink: 0;
        }
        .btn-register {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            border-radius: .75rem;
            padding: .8rem;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: .02em;
            transition: all .2s;
        }
        .btn-register:hover { opacity: .92; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(29,78,216,.35); }
        .input-icon { border: 1.5px solid #e2e8f0; border-radius: .65rem 0 0 .65rem; background:#fff; }
        .input-icon + .form-control { border-left: none; border-radius: 0 .65rem .65rem 0; }
        .optional-tag { font-size: .72rem; font-weight: 400; color: #94a3b8; margin-left: .3rem; }
    </style>
</head>
<body>
<div class="container py-4">
<div class="register-card">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="brand-icon">
            <i class="bi bi-heart-pulse-fill text-white" style="font-size:1.3rem"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0">Créer votre espace patient</h5>
            <p class="text-muted small mb-0">MedClinic — Gestion médicale en ligne</p>
        </div>
    </div>

    {{-- Errors --}}
    @if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4 py-3">
        <div class="d-flex gap-2 align-items-center mb-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Veuillez corriger les erreurs suivantes :</strong>
        </div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    {{-- ══ SECTION 1 : Compte ══ --}}
    <div class="mb-4">
        <div class="section-header">
            <span class="step-num">1</span> Informations de connexion
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text input-icon"><i class="bi bi-envelope text-muted" style="font-size:.85rem"></i></span>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="votre@email.com" required autofocus>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nom d'affichage <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Prénom Nom (affiché dans le système)" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="pwd1"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimum 8 caractères" required
                           style="border-radius:.65rem 0 0 .65rem">
                    <button type="button" class="btn btn-outline-secondary"
                            style="border:1.5px solid #e2e8f0;border-radius:0 .65rem .65rem 0"
                            onclick="togglePwd('pwd1','eye1')">
                        <i class="bi bi-eye" id="eye1"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="pwd2"
                           class="form-control"
                           placeholder="Répéter le mot de passe" required
                           style="border-radius:.65rem 0 0 .65rem">
                    <button type="button" class="btn btn-outline-secondary"
                            style="border:1.5px solid #e2e8f0;border-radius:0 .65rem .65rem 0"
                            onclick="togglePwd('pwd2','eye2')">
                        <i class="bi bi-eye" id="eye2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ SECTION 2 : Identité ══ --}}
    <div class="mb-4">
        <div class="section-header">
            <span class="step-num">2</span> Identité & Contact
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                       class="form-control @error('first_name') is-invalid @enderror"
                       placeholder="Prénom" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nom de famille <span class="text-danger">*</span></label>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                       class="form-control @error('last_name') is-invalid @enderror"
                       placeholder="Nom" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text input-icon"><i class="bi bi-phone text-muted" style="font-size:.85rem"></i></span>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="0612 345 678" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Numéro CIN <span class="optional-tag">(optionnel)</span></label>
                <input type="text" name="cin" value="{{ old('cin') }}"
                       class="form-control @error('cin') is-invalid @enderror"
                       placeholder="Ex: AB123456">
            </div>
            <div class="col-md-6">
                <label class="form-label">Date de naissance <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                       class="form-control @error('date_of_birth') is-invalid @enderror"
                       max="{{ date('Y-m-d', strtotime('-1 year')) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Genre <span class="text-danger">*</span></label>
                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>👨 Masculin</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>👩 Féminin</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Adresse <span class="optional-tag">(optionnel)</span></label>
                <input type="text" name="address" value="{{ old('address') }}"
                       class="form-control" placeholder="Adresse complète">
            </div>
        </div>
    </div>

    {{-- ══ SECTION 3 : Médical (optionnel) ══ --}}
    <div class="mb-4">
        <div class="section-header">
            <span class="step-num" style="background:#10b981">3</span>
            Informations médicales <span class="optional-tag" style="text-transform:none">— optionnel</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Groupe sanguin</label>
                <select name="blood_type" class="form-select">
                    <option value="">-- Non renseigné --</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                        <option value="{{ $bt }}" {{ old('blood_type') === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="bg-blue-50 rounded-3 p-3 w-100" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <div class="small text-primary">
                        <i class="bi bi-shield-check me-1"></i>
                        <strong>Données sécurisées</strong><br>
                        <span style="font-size:.78rem;color:#475569">Vos informations médicales sont protégées et accessibles uniquement à votre équipe médicale.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="terms" required>
        <label class="form-check-label small text-muted" for="terms">
            J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a>
            et la <a href="#" class="text-primary">politique de confidentialité</a> de MedClinic.
        </label>
    </div>

    <button type="submit" class="btn btn-register btn-primary w-100 text-white">
        <i class="bi bi-person-plus-fill me-2"></i>Créer mon compte patient
    </button>

    <div class="text-center mt-3">
        <span class="small text-muted">Déjà inscrit ?</span>
        <a href="{{ route('login') }}" class="small fw-semibold text-primary text-decoration-none ms-1">
            Se connecter <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    </form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd(fieldId, iconId) {
    const f = document.getElementById(fieldId);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.className = f.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
