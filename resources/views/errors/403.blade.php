{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès Refusé — MedClinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg,#0f172a,#1e3a5f); min-height:100vh; display:flex; align-items:center; }
        .error-card { background:#fff; border-radius:1.5rem; padding:3rem; text-align:center; max-width:480px; width:100%; margin:0 auto; }
        .error-icon { font-size:4rem; margin-bottom:1rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="error-card">
        <div class="error-icon">🔒</div>
        <h2 class="fw-bold">Accès non autorisé</h2>
        <p class="text-muted">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
        <a href="javascript:history.back()" class="btn btn-primary me-2">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Accueil</a>
    </div>
</div>
</body>
</html>
