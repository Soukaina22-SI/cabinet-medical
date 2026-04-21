
 
{{-- ============================================================ --}}
{{-- resources/views/pdf/ordonnance.blade.php --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1a1a1a; }
.header { display: flex; justify-content: space-between; border-bottom: 2px solid #1a6fa0; padding-bottom: 10px; margin-bottom: 20px; }
.medecin-info h3 { color: #1a6fa0; margin: 0; font-size: 16px; }
.medecin-info p { margin: 2px 0; font-size: 11px; color: #555; }
.patient-box { background: #f0f7ff; border-left: 4px solid #1a6fa0; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
.rx-symbol { font-size: 42px; color: #1a6fa0; font-weight: bold; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th { background: #1a6fa0; color: white; padding: 8px; text-align: left; font-size: 11px; }
td { padding: 7px 8px; border-bottom: 1px solid #eee; font-size: 12px; }
tr:nth-child(even) td { background: #f8f9fa; }
.footer { margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; font-size: 11px; color: #777; }
.signature-box { border: 1px dashed #ccc; height: 60px; width: 180px; margin-top: 30px; padding: 5px; font-size: 10px; color: #999; }
</style>
</head>
<body>
<div class="header">
    <div class="medecin-info">
        <h3>Dr. {{ $medecin->user->nom_complet }}</h3>
        <p>Spécialité : {{ $medecin->specialite }}</p>
        <p>N° Ordre : {{ $medecin->num_ordre }}</p>
        <p>Tél : {{ $medecin->user->telephone }}</p>
    </div>
    <div style="text-align:right">
        <div class="rx-symbol">℞</div>
        <p style="font-size:11px;color:#555">Date : {{ now()->format('d/m/Y') }}</p>
    </div>
</div>
 
<div class="patient-box">
    <strong>Patient :</strong> {{ $patient->user->nom_complet }} &nbsp;|&nbsp;
    <strong>Âge :</strong> {{ $patient->age }} ans &nbsp;|&nbsp;
    <strong>Sexe :</strong> {{ $patient->sexe === 'M' ? 'Masculin' : 'Féminin' }}
</div>
 
<table>
    <thead>
        <tr>
            <th>Médicament</th>
            <th>Dosage</th>
            <th>Posologie</th>
            <th>Durée</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ordonnance->medicaments as $med)
        <tr>
            <td><strong>{{ $med['nom'] }}</strong></td>
            <td>{{ $med['dosage'] }}</td>
            <td>{{ $med['posologie'] }}</td>
            <td>{{ $med['duree'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
 
@if($ordonnance->instructions)
<div style="margin-top:15px;padding:10px;background:#fffbeb;border-left:3px solid #f59e0b;border-radius:4px">
    <strong style="font-size:12px">Instructions :</strong>
    <p style="margin:4px 0;font-size:12px">{{ $ordonnance->instructions }}</p>
</div>
@endif
 
<div style="display:flex;justify-content:space-between;margin-top:30px">
    <div>
        <div class="signature-box">Signature & Cachet du médecin</div>
    </div>
    <div style="text-align:right;font-size:11px;color:#777">
        <p>Ordonnance n° {{ $ordonnance->id }}</p>
        <p>Valable 3 mois à compter du {{ now()->format('d/m/Y') }}</p>
    </div>
</div>
 
<div class="footer">
    <p>Cabinet Médical — Généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
</div>
</body>
</html>
 
 