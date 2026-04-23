{{-- resources/views/emails/appointment-confirmed.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background:#f1f5f9; margin:0; padding:24px; }
        .container { max-width:560px; margin:0 auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.08); }
        .header { background:linear-gradient(135deg,#1d4ed8,#3b82f6); padding:32px 32px 24px; text-align:center; }
        .header h1 { color:#fff; font-size:22px; margin:0; }
        .header p { color:rgba(255,255,255,.8); margin:6px 0 0; font-size:14px; }
        .body { padding:32px; }
        .greeting { font-size:16px; margin-bottom:20px; }
        .card { background:#f8fafc; border-radius:10px; padding:20px; margin:20px 0; border-left:4px solid #3b82f6; }
        .card-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #e2e8f0; font-size:14px; }
        .card-row:last-child { border-bottom:none; }
        .card-row .label { color:#64748b; }
        .card-row .value { font-weight:600; color:#1e293b; }
        .status { display:inline-block; background:#d1fae5; color:#065f46; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:600; margin:16px 0; }
        .cta { text-align:center; margin:24px 0; }
        .btn { background:#1d4ed8; color:#fff; text-decoration:none; padding:12px 32px; border-radius:8px; font-weight:600; font-size:14px; display:inline-block; }
        .footer { background:#f8fafc; padding:20px 32px; text-align:center; font-size:12px; color:#94a3b8; border-top:1px solid #e2e8f0; }
        .icon { font-size:32px; margin-bottom:8px; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <div class="icon">✅</div>
        <h1>Rendez-vous Confirmé</h1>
        <p>Votre consultation a bien été enregistrée</p>
    </div>

    <div class="body">
        <p class="greeting">
            Bonjour <strong>{{ $appointment->patient->full_name }}</strong>,
        </p>

        <p>Nous confirmons votre rendez-vous médical chez <strong>MedClinic</strong>.
           Voici le récapitulatif de votre consultation :</p>

        <div class="card">
            <div class="card-row">
                <span class="label">🩺 Médecin</span>
                <span class="value">Dr. {{ $appointment->doctor->name }}</span>
            </div>
            <div class="card-row">
                <span class="label">🏥 Spécialité</span>
                <span class="value">{{ $appointment->doctor->speciality ?? 'Médecine générale' }}</span>
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

        <div class="status">✓ Statut : Confirmé</div>

        <p style="font-size:14px;color:#475569">
            Merci d'arriver <strong>10 minutes avant</strong> votre rendez-vous.
            En cas d'empêchement, veuillez annuler au moins 24h à l'avance.
        </p>

        <div class="cta">
            <a href="{{ url('/appointments') }}" class="btn">Voir mon rendez-vous</a>
        </div>

        <p style="font-size:13px;color:#94a3b8">
            Si vous avez des questions, contactez-nous au <strong>+212 5XX-XXXXXX</strong>
            ou répondez à cet email.
        </p>
    </div>

    <div class="footer">
        <strong>MedClinic</strong> — Casablanca, Maroc<br>
        Cet email a été envoyé automatiquement. Merci de ne pas y répondre directement.
    </div>

</div>
</body>
</html>
