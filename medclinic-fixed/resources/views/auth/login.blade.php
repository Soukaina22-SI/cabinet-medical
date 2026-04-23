<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — MedClinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
            width: 100%;
            max-width: 420px;
        }
        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .form-control {
            border-radius: .65rem;
            padding: .7rem 1rem;
            border: 1.5px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            border-radius: .65rem;
            padding: .75rem;
            font-weight: 600;
            letter-spacing: .02em;
        }
        .btn-login:hover { opacity: .92; transform: translateY(-1px); }
        .demo-badge {
            font-size: .78rem;
            background: #f1f5f9;
            border-radius: .5rem;
            padding: .5rem .75rem;
            color: #475569;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="login-card mx-auto">

                <!-- Brand -->
                <div class="text-center mb-4">
                    <div class="brand-icon">
                        <i class="bi bi-heart-pulse-fill text-white fs-3"></i>
                    </div>
                    <h4 class="fw-bold mb-1">MedClinic</h4>
                    <p class="text-muted small mb-0">Système de gestion de clinique médicale</p>
                </div>

                <!-- Errors -->
                @if($errors->any())
                    <div class="alert alert-danger rounded-3 d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Adresse email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"
                                  style="border:1.5px solid #e2e8f0;border-radius:.65rem 0 0 .65rem">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control border-start-0 @error('email') is-invalid @enderror"
                                   placeholder="votre@email.com" required autofocus
                                   style="border-radius:0 .65rem .65rem 0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-muted">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"
                                  style="border:1.5px solid #e2e8f0;border-radius:.65rem 0 0 .65rem">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" id="passwordInput"
                                   class="form-control border-start-0 @error('password') is-invalid @enderror"
                                   placeholder="••••••••" required
                                   style="border-radius:0 .65rem .65rem 0">
                            <button type="button" class="btn btn-outline-secondary border-start-0"
                                    style="border-radius:0 .65rem .65rem 0;border:1.5px solid #e2e8f0"
                                    onclick="togglePwd()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                        </div>
                        <a href="#" class="small text-primary text-decoration-none">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                    </button>
                </form>

                <!-- Demo credentials -->
                <div class="mt-4">
                    <p class="text-center small text-muted mb-2">Comptes de démonstration</p>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="demo-badge text-center cursor-pointer" onclick="fillDemo('admin@medclinic.com','password')">
                                <i class="bi bi-shield-check text-primary me-1"></i>
                                <strong>Admin</strong><br>
                                <span class="text-muted" style="font-size:.72rem">admin@medclinic.com</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="demo-badge text-center" onclick="fillDemo('doctor@medclinic.com','password')">
                                <i class="bi bi-stethoscope text-success me-1"></i>
                                <strong>Médecin</strong><br>
                                <span class="text-muted" style="font-size:.72rem">doctor@medclinic.com</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const inp = document.getElementById('passwordInput');
    const ico = document.getElementById('eyeIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'bi bi-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'bi bi-eye'; }
}
function fillDemo(email, pwd) {
    document.querySelector('[name=email]').value = email;
    document.querySelector('[name=password]').value = pwd;
}
</script>
</body>
</html>
