{{-- resources/views/prescriptions/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ordonnance #{{ $prescription->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }

        .header {
            border-bottom: 3px solid #1d4ed8;
            padding-bottom: 16px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .clinic-name { font-size: 22px; font-weight: bold; color: #1d4ed8; }
        .clinic-subtitle { font-size: 11px; color: #64748b; margin-top: 3px; }

        .doc-info { text-align: right; }
        .doc-name { font-size: 14px; font-weight: bold; }
        .doc-specialty { color: #64748b; font-size: 11px; }

        .title-banner {
            background: #1d4ed8;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 4px;
            margin-bottom: 20px;
            letter-spacing: 2px;
        }

        .info-grid { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-box {
            flex: 1;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }
        .info-box-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .info-value { font-weight: bold; font-size: 13px; }
        .info-sub { font-size: 11px; color: #64748b; margin-top: 2px; }

        .rx-symbol {
            font-size: 32px;
            font-weight: bold;
            color: #1d4ed8;
            margin-bottom: 10px;
        }

        .med-item {
            border-left: 4px solid #1d4ed8;
            padding: 10px 14px;
            margin-bottom: 10px;
            background: #f8fafc;
            border-radius: 0 6px 6px 0;
        }
        .med-name { font-size: 14px; font-weight: bold; color: #1d4ed8; margin-bottom: 4px; }
        .med-details { font-size: 11px; color: #475569; }
        .med-details span { margin-right: 16px; }
        .med-instructions { font-size: 11px; color: #64748b; font-style: italic; margin-top: 4px; }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0; right: 0;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 12px;
            font-size: 10px;
            color: #94a3b8;
        }

        .signature-area {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            display: inline-block;
            border-top: 2px solid #1e293b;
            width: 200px;
            padding-top: 8px;
            font-size: 11px;
            color: #475569;
        }
        .watermark {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div>
        <div class="clinic-name">🏥 MedClinic</div>
        <div class="clinic-subtitle">Clinique Médicale Moderne</div>
        <div class="clinic-subtitle">📞 +212 5XX-XXXXXX &nbsp;|&nbsp; ✉ contact@medclinic.ma</div>
        <div class="clinic-subtitle">📍 Casablanca, Maroc</div>
    </div>
    <div class="doc-info">
        <div class="doc-name">Dr. {{ $prescription->doctor->name }}</div>
        <div class="doc-specialty">{{ $prescription->doctor->speciality ?? 'Médecin Généraliste' }}</div>
        <div class="clinic-subtitle" style="margin-top:8px">
            Date: {{ $prescription->created_at->format('d/m/Y') }}
        </div>
        <div class="clinic-subtitle">Ordonnance N° {{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>
</div>

<!-- Title -->
<div class="title-banner">O R D O N N A N C E</div>

<!-- Patient & Doctor info -->
<div class="info-grid">
    <div class="info-box">
        <div class="info-box-title">Patient</div>
        <div class="info-value">{{ $prescription->patient->full_name }}</div>
        <div class="info-sub">
            Âge: {{ $prescription->patient->age }} ans &nbsp;|&nbsp;
            {{ $prescription->patient->gender === 'male' ? 'M' : 'F' }}
            @if($prescription->patient->blood_type)
                &nbsp;|&nbsp; Groupe: {{ $prescription->patient->blood_type }}
            @endif
        </div>
    </div>
    <div class="info-box">
        <div class="info-box-title">Diagnostic</div>
        <div class="info-value" style="font-size:12px">
            {{ $prescription->consultation->diagnosis }}
        </div>
    </div>
</div>

<!-- Rx symbol -->
<div class="rx-symbol">℞</div>

<!-- Medications -->
@foreach($prescription->items as $index => $item)
<div class="med-item">
    <div class="med-name">{{ $index + 1 }}. {{ $item->medication_name }}</div>
    <div class="med-details">
        <span>💊 Dosage: <strong>{{ $item->dosage }}</strong></span>
        <span>🕐 Fréquence: <strong>{{ $item->frequency }}</strong></span>
        <span>📅 Durée: <strong>{{ $item->duration }}</strong></span>
    </div>
    @if($item->instructions)
        <div class="med-instructions">ℹ {{ $item->instructions }}</div>
    @endif
</div>
@endforeach

<!-- Notes from consultation -->
@if($prescription->consultation->notes)
<div style="margin-top:20px;padding:12px;background:#fffbeb;border:1px solid #fbbf24;border-radius:6px">
    <div style="font-size:10px;text-transform:uppercase;color:#92400e;margin-bottom:4px">Recommandations</div>
    <div style="font-size:11px">{{ $prescription->consultation->notes }}</div>
</div>
@endif

<!-- Signature -->
<div class="signature-area">
    <div class="signature-line">Signature & Cachet du Médecin</div>
    <div class="watermark">Dr. {{ $prescription->doctor->name }}</div>
</div>

<!-- Footer -->
<div class="footer">
    MedClinic — Ordonnance N° {{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}
    — Émise le {{ $prescription->created_at->format('d/m/Y') }}
    — Ce document est confidentiel
</div>

</body>
</html>
