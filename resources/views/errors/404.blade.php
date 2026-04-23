<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Introuvable — MedClinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .error-card {
            background: #fff;
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
        }
        .error-number {
            font-size: 6rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="error-card">
        <div class="error-number">404</div>
        <div style="font-size:3rem;margin:.5rem 0">🔍</div>
        <h3 class="fw-bold mt-2">Page introuvable</h3>
        <p class="text-muted">La page que vous cherchez n'existe pas ou a été déplacée.</p>
        <div class="d-flex gap-2 justify-content-center mt-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i>Retour
            </a>
            <a href="{{ url('/') }}" class="btn btn-primary px-4">
                <i class="bi bi-house me-1"></i>Accueil
            </a>
        </div>
    </div>
</div>
</body>
</html>
