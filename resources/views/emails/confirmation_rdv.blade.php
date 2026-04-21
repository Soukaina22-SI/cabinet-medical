
 
{{-- ============================================================ --}}
{{-- resources/views/emails/confirmation_rdv.blade.php --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>
body { font-family: Arial, sans-serif; background: #f4f4f4; }
.container { max-width: 560px; margin: 30px auto; background: white; border-radius: 8px; overflow: hidden; }
.header { background: #1a6fa0; color: white; padding: 24px; text-align: center; }
.body { padding: 24px; }
.info-box { background: #f0f7ff; border-radius: 8px; padding: 16px; margin: 16px 0; }
.footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #777; }
</style></head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0">✅ Rendez-vous confirmé</h2>
    </div>
    <div class="body">
        <p>Bonjour <strong>{{ $patient->prenom }} {{ $patient->nom }}</strong>,</p>
        <p>Votre rendez-vous a bien été enregistré.</p>
        <div class="info-box">
            <p style="margin:4px 0"><strong>🩺 Médecin :</strong> Dr. {{ $medecin->nom_complet }}</p>
            <p style="margin:4px 0"><strong>📅 Date :</strong> {{ $rdv->date_heure->format('l d F Y') }}</p>
            <p style="margin:4px 0"><strong>🕐 Heure :</strong> {{ $rdv->date_heure->format('H:i') }}</p>
            <p style="margin:4px 0"><strong>📋 Motif :</strong> {{ $rdv->motif }}</p>
        </div>
        <p style="color:#777;font-size:13px">Vous recevrez un rappel la veille de votre rendez-vous.</p>
    </div>
    <div class="footer">Cabinet Médical — Ne pas répondre à cet email</div>
</div>
</body>
</html>
 