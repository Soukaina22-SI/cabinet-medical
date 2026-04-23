{{-- resources/views/emails/appointment-reminder.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background:#f1f5f9; margin:0; padding:24px; }
        .container { max-width:560px; margin:0 auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.08); }
        .header { background:linear-gradient(135deg,#f59e0b,#d97706); padding:32px; text-align:center; }
        .header h1 { color:#fff; font-size:22px; margin:0; }
        .header p { color:rgba(255,255,255,.85); margin:6px 0 0; font-size:14px; }
        .body { padding:32px; }
        .card { background:#fffbeb; border-radius:10px; padding:20px; margin:20px 0; border-left:4px solid #f59e0b; }
        .card-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #fde68a; font-size:14px; }
        .card-row:last-child { border-bottom:none; }
        .card-row .label { color:#92400e; }
        .card-row .value { font-weight:600; color:#1e293b; }
        .tip { background:#f0fdf4; border-radius:8px; padding:14px; font-size:13px; color:#14532d; margin:20px 0; }
        .footer { background:#f8fafc; padding:20px 32px; text-align:center; font-size:12px; color:#94a3b8; border-top:1px solid #e2e8f0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div style="font-size:36px;margin-bottom:8px">⏰</div>
        <h1>Rappel de Rendez-vous</h1>
        <p>Votre consultation est prévue <strong>demain</strong></p>
    </div>
    <div class="body">
        <p>Bonjour <strong>{{ $appointment->patient->full_name }}</strong>,</p>
        <p>Ceci est un rappel pour votre rendez-vous médical demain chez <strong>MedClinic</strong> :</p>

        <div class="card">
            <div class="card-row">
                <span class="label">🩺 Médecin</span>
                <span class="value">Dr. {{ $appointment->doctor->name }}</span>
            </div>
            <div class="card-row">
                <span class="label">📅 Date</span>
                <span class="value">{{ $appointment->appointment_date->translatedFormat('l d F Y') }}</span>
            </div>
            <div class="card-row">
                <span class="label">🕐 Heure</span>
                <span class="value">{{ $appointment->appointment_date->format('H:i') }}</span>
            </div>
            @if($appointment->reason)
            <div class="card-row">
                <span class="label">📋 Motif</span>
                <span class="value">{{ $appointment->reason }}</span>
            </div>
            @endif
        </div>

        <div class="tip">
            💡 <strong>Conseils :</strong><br>
            • Arrivez 10 minutes à l'avance<br>
            • Apportez votre CIN et carnet de santé<br>
            • En cas d'empêchement, annulez avant 24h
        </div>

        <p style="font-size:13px;color:#64748b">
            Pour annuler ou modifier votre rendez-vous, contactez-nous au <strong>+212 5XX-XXXXXX</strong>.
        </p>
    </div>
    <div class="footer">
        <strong>MedClinic</strong> — Casablanca, Maroc<br>
        Email automatique — Merci de ne pas répondre directement.
    </div>
</div>
</body>
</html>
