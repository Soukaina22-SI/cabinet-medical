{{-- resources/views/patient/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Mon Espace Patient')

@push('styles')
<style>
.patient-hero {
    background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
    border-radius: 1.25rem;
    color: #fff;
    padding: 2rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.patient-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.07);
    border-radius: 50%;
}
.patient-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; right: 80px;
    width: 120px; height: 120px;
    background: rgba(255,255,255,.05);
    border-radius: 50%;
}
.dossier-card {
    background: #fff;
    border-radius: 1rem;
    border: 1.5px solid #e2e8f0;
    overflow: hidden;
    height: 100%;
}
.dossier-header {
    background: linear-gradient(135deg, #f8fafc, #eff6ff);
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}
.dossier-body { padding: 1.5rem; }
.info-row {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .6rem 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: .875rem;
}
.info-row:last-child { border-bottom: none; }
.info-icon {
    width: 32px; height: 32px;
    border-radius: .5rem;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem;
    flex-shrink: 0;
    margin-top: .1rem;
}
.info-label { font-size: .72rem; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
.info-value { font-weight: 600; color: #1e293b; }
.appt-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    transition: background .15s;
}
.appt-item:last-child { border-bottom: none; }
.appt-item:hover { background: #f8fafc; }
.date-box {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border-radius: .65rem;
    text-align: center;
    padding: .5rem .6rem;
    min-width: 50px;
    flex-shrink: 0;
}
.date-box .day { font-size: 1.2rem; font-weight: 800; color: #1d4ed8; line-height: 1; }
.date-box .month { font-size: .65rem; font-weight: 700; color: #3b82f6; text-transform: uppercase; }
.booking-panel {
    background: #fff;
    border-radius: 1rem;
    border: 1.5px solid #e2e8f0;
    overflow: hidden;
}
.booking-header {
    background: linear-gradient(135deg, #059669, #10b981);
    padding: 1.25rem 1.5rem;
    color: #fff;
}
.slot-btn {
    border: 1.5px solid #e2e8f0;
    border-radius: .5rem;
    padding: .35rem .7rem;
    font-size: .8rem;
    font-weight: 600;
    color: #374151;
    background: #fff;
    cursor: pointer;
    transition: all .15s;
}
.slot-btn:hover, .slot-btn.selected {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #fff;
}
.section-card {
    background: #fff;
    border-radius: 1rem;
    border: 1.5px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 1rem;
}
.section-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.vital-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: #f1f5f9;
    border-radius: 2rem;
    padding: .3rem .75rem;
    font-size: .8rem;
    font-weight: 600;
    color: #475569;
}
</style>
@endpush

@section('content')
@php
    $user    = auth()->user();
    $patient = \App\Models\Patient::where('user_id', $user->id)->first();

    $upcomingAppts = $patient
        ? \App\Models\Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('appointment_date')->limit(5)->get()
        : collect();

    $pastAppts = $patient
        ? \App\Models\Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->where('appointment_date', '<', now())
            ->orderByDesc('appointment_date')->limit(3)->get()
        : collect();

    $consultations = $patient
        ? \App\Models\Consultation::with(['doctor','prescription.items'])
            ->where('patient_id', $patient->id)
            ->latest()->limit(5)->get()
        : collect();

    $doctors = \App\Models\User::where('role','medecin')->where('is_active', true)->get();

    $totalRdv   = $patient ? \App\Models\Appointment::where('patient_id', $patient->id)->count() : 0;
    $consultCnt = $patient ? \App\Models\Consultation::where('patient_id', $patient->id)->count() : 0;
    $prescCnt   = $patient ? \App\Models\Prescription::where('patient_id', $patient->id)->count() : 0;
@endphp

{{-- Flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible rounded-3 d-flex align-items-center gap-2 mb-3 shadow-sm">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <div>{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible rounded-3 d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════
     HERO BANNER
═══════════════════════════════════════════════════ --}}
<div class="patient-hero">
    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
             style="width:56px;height:56px;background:rgba(255,255,255,.2);font-size:1.3rem;flex-shrink:0">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h5 class="fw-bold mb-0">Bonjour, {{ $user->name }} 👋</h5>
            <p class="mb-0" style="opacity:.8;font-size:.9rem">
                @if($patient)
                    Patient depuis {{ $patient->created_at->translatedFormat('F Y') }}
                @else
                    Bienvenue dans votre espace santé
                @endif
            </p>
        </div>
        <div class="ms-auto d-none d-md-block">
            <a href="#booking-section" class="btn btn-light fw-semibold px-4">
                <i class="bi bi-calendar-plus me-2"></i>Prendre un RDV
            </a>
        </div>
    </div>

    @if($patient)
    {{-- Quick stats in hero --}}
    <div class="row g-2 mt-1">
        <div class="col-4">
            <div class="rounded-3 text-center py-2" style="background:rgba(255,255,255,.15)">
                <div class="fw-bold fs-5">{{ $totalRdv }}</div>
                <div style="font-size:.72rem;opacity:.8">Rendez-vous</div>
            </div>
        </div>
        <div class="col-4">
            <div class="rounded-3 text-center py-2" style="background:rgba(255,255,255,.15)">
                <div class="fw-bold fs-5">{{ $consultCnt }}</div>
                <div style="font-size:.72rem;opacity:.8">Consultations</div>
            </div>
        </div>
        <div class="col-4">
            <div class="rounded-3 text-center py-2" style="background:rgba(255,255,255,.15)">
                <div class="fw-bold fs-5">{{ $prescCnt }}</div>
                <div style="font-size:.72rem;opacity:.8">Ordonnances</div>
            </div>
        </div>
    </div>
    @endif
</div>

@if(!$patient)
{{-- ══ Pas de dossier ══ --}}
<div class="alert alert-info rounded-3 d-flex align-items-start gap-3 p-4 mb-4">
    <i class="bi bi-info-circle-fill fs-3 text-info mt-1"></i>
    <div>
        <strong class="d-block mb-1">Votre dossier médical n'est pas encore créé</strong>
        <p class="mb-2 small text-muted">
            Votre fiche patient sera créée lors de votre première visite au cabinet.
            Vous pouvez déjà prendre un rendez-vous ci-dessous.
        </p>
    </div>
</div>
@else

<div class="row g-3">

    {{-- ══════════════════════════════════════════════════
         COLONNE GAUCHE — Dossier médical
    ══════════════════════════════════════════════════ --}}
    <div class="col-lg-4">

        {{-- Dossier médical complet --}}
        <div class="dossier-card mb-3">
            <div class="dossier-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>Mon dossier médical
                    </h6>
                    <small class="text-muted">N° {{ str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</small>
                </div>
                @if($patient->blood_type)
                <span class="badge bg-danger fs-6 px-3">{{ $patient->blood_type }}</span>
                @endif
            </div>

            <div class="dossier-body">
                {{-- Identité --}}
                <div class="mb-3">
                    <p class="text-muted" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem">
                        Identité
                    </p>

                    <div class="info-row">
                        <div class="info-icon" style="background:#eff6ff;color:#2563eb"><i class="bi bi-person-fill"></i></div>
                        <div>
                            <div class="info-label">Nom complet</div>
                            <div class="info-value">{{ $patient->full_name }}</div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon" style="background:#f0fdf4;color:#16a34a"><i class="bi bi-calendar-heart"></i></div>
                        <div>
                            <div class="info-label">Date de naissance</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') }}
                                <span class="text-muted fw-normal">({{ $patient->age }} ans)</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-icon" style="background:#fefce8;color:#ca8a04"><i class="bi bi-gender-ambiguous"></i></div>
                        <div>
                            <div class="info-label">Genre</div>
                            <div class="info-value">{{ $patient->gender === 'male' ? '👨 Masculin' : '👩 Féminin' }}</div>
                        </div>
                    </div>

                    @if($patient->cin)
                    <div class="info-row">
                        <div class="info-icon" style="background:#f5f3ff;color:#7c3aed"><i class="bi bi-credit-card-2-front"></i></div>
                        <div>
                            <div class="info-label">CIN</div>
                            <div class="info-value">{{ $patient->cin }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Contact --}}
                <div class="mb-3">
                    <p class="text-muted" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem">
                        Contact
                    </p>
                    <div class="info-row">
                        <div class="info-icon" style="background:#fff7ed;color:#ea580c"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="info-label">Téléphone</div>
                            <div class="info-value">{{ $patient->phone }}</div>
                        </div>
                    </div>
                    @if($patient->email)
                    <div class="info-row">
                        <div class="info-icon" style="background:#eff6ff;color:#2563eb"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <div class="info-label">Email</div>
                            <div class="info-value" style="font-size:.82rem">{{ $patient->email }}</div>
                        </div>
                    </div>
                    @endif
                    @if($patient->address)
                    <div class="info-row">
                        <div class="info-icon" style="background:#f0fdf4;color:#16a34a"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <div class="info-label">Adresse</div>
                            <div class="info-value" style="font-size:.82rem">{{ $patient->address }}</div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Informations médicales --}}
                <div>
                    <p class="text-muted" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.5rem">
                        Informations médicales
                    </p>

                    @if($patient->blood_type)
                    <div class="info-row">
                        <div class="info-icon" style="background:#fef2f2;color:#dc2626"><i class="bi bi-droplet-fill"></i></div>
                        <div>
                            <div class="info-label">Groupe sanguin</div>
                            <div class="info-value">
                                <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($patient->allergies)
                    <div class="info-row">
                        <div class="info-icon" style="background:#fef2f2;color:#dc2626"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div>
                            <div class="info-label">⚠️ Allergies</div>
                            <div class="info-value text-danger" style="font-size:.82rem">{{ $patient->allergies }}</div>
                        </div>
                    </div>
                    @endif

                    @if($patient->medical_notes)
                    <div class="info-row">
                        <div class="info-icon" style="background:#f0fdf4;color:#16a34a"><i class="bi bi-journal-medical"></i></div>
                        <div>
                            <div class="info-label">Notes médicales</div>
                            <div class="info-value" style="font-size:.82rem">{{ Str::limit($patient->medical_notes, 100) }}</div>
                        </div>
                    </div>
                    @endif

                    @if(!$patient->blood_type && !$patient->allergies && !$patient->medical_notes)
                    <p class="text-muted small text-center py-2">
                        <i class="bi bi-clipboard-check me-1"></i>
                        Aucune information médicale renseignée
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Consultations récentes --}}
        <div class="section-card">
            <div class="section-card-header">
                <h6 class="fw-bold mb-0 small">
                    <i class="bi bi-clipboard2-pulse text-success me-2"></i>Mes consultations
                </h6>
                <span class="badge bg-success rounded-pill">{{ $consultCnt }}</span>
            </div>
            @forelse($consultations as $c)
            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <span class="fw-semibold small">Dr. {{ $c->doctor?->name ?? '—' }}</span>
                    <small class="text-muted">{{ $c->created_at->format('d/m/Y') }}</small>
                </div>
                @if($c->diagnosis)
                <p class="small text-muted mb-2 lh-sm">{{ Str::limit($c->diagnosis, 70) }}</p>
                @endif
                @if($c->prescription)
                <a href="{{ route('prescriptions.download', $c) }}"
                   class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                   style="font-size:.75rem;padding:.2rem .6rem">
                    <i class="bi bi-file-earmark-pdf"></i> Ordonnance PDF
                </a>
                @endif
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-clipboard2 fs-3 d-block mb-2 opacity-40"></i>
                <small>Aucune consultation</small>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════
         COLONNE DROITE — RDV + Réservation
    ══════════════════════════════════════════════════ --}}
    <div class="col-lg-8">

        {{-- ── Prochains RDV ── --}}
        <div class="section-card mb-3">
            <div class="section-card-header">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar3 text-primary me-2"></i>Mes prochains rendez-vous
                </h6>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">
                    Tout voir <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @forelse($upcomingAppts as $appt)
            <div class="appt-item">
                <div class="date-box">
                    <div class="day">{{ $appt->appointment_date->format('d') }}</div>
                    <div class="month">{{ $appt->appointment_date->isoFormat('MMM') }}</div>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="fw-semibold small">Dr. {{ $appt->doctor?->name ?? '—' }}</div>
                    <div class="text-muted small">
                        <i class="bi bi-clock me-1"></i>{{ $appt->appointment_date->format('H:i') }}
                        @if($appt->doctor?->speciality)
                            — {{ $appt->doctor->speciality }}
                        @endif
                    </div>
                    @if($appt->reason)
                    <div style="font-size:.76rem;color:#94a3b8">
                        <i class="bi bi-chat-text me-1"></i>{{ Str::limit($appt->reason, 40) }}
                    </div>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    {!! $appt->status_badge !!}
                    <a href="{{ route('appointments.show', $appt) }}"
                       class="btn btn-sm btn-light" title="Détails">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-40"></i>
                <small class="d-block mb-2">Aucun rendez-vous à venir</small>
            </div>
            @endforelse
        </div>

        {{-- ── Formulaire de réservation ── --}}
        <div class="booking-panel" id="booking-section">
            <div class="booking-header">
                <h6 class="fw-bold mb-1">
                    <i class="bi bi-calendar-plus me-2"></i>Prendre un nouveau rendez-vous
                </h6>
                <p class="mb-0 small" style="opacity:.85">
                    Choisissez un médecin, une date et un créneau disponible
                </p>
            </div>

            <div class="p-3">
                @if($patient->email)
                <div class="alert alert-info rounded-3 py-2 px-3 mb-3 small d-flex align-items-center gap-2">
                    <i class="bi bi-envelope-check text-primary fs-5"></i>
                    <span>Un email de confirmation sera envoyé à <strong>{{ $patient->email }}</strong></span>
                </div>
                @endif

                <form method="POST" action="{{ route('appointments.store') }}" id="bookingForm">
                    @csrf
                    {{-- Patient ID caché --}}
                    <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                    <div class="row g-3">

                        {{-- Médecin --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-person-badge text-primary me-1"></i>Médecin <span class="text-danger">*</span>
                            </label>
                            <select name="doctor_id" id="doctorSelect" class="form-select" required>
                                <option value="">-- Choisir un médecin --</option>
                                @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">
                                    Dr. {{ $doc->name }}
                                    @if($doc->speciality) — {{ $doc->speciality }}@endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-calendar3 text-primary me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="dateInput" class="form-control"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   required>
                        </div>

                        {{-- Créneaux disponibles --}}
                        <div class="col-12" id="slotsSection" style="display:none">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-clock text-primary me-1"></i>Créneau disponible <span class="text-danger">*</span>
                            </label>
                            <input type="hidden" name="appointment_date" id="appointmentDateFinal">
                            <div id="slotsContainer" class="d-flex flex-wrap gap-2">
                                <span class="text-muted small">Sélectionnez un médecin et une date</span>
                            </div>
                            <div id="noSlotsMsg" class="text-muted small mt-1" style="display:none">
                                <i class="bi bi-x-circle me-1 text-danger"></i>
                                Aucun créneau disponible ce jour. Choisissez une autre date.
                            </div>
                        </div>

                        {{-- Motif --}}
                        <div class="col-12">
                            <label class="form-label small fw-semibold">
                                <i class="bi bi-chat-text text-primary me-1"></i>Motif de la consultation
                            </label>
                            <input type="text" name="reason" class="form-control"
                                   placeholder="Ex: Douleur abdominale, contrôle annuel, fièvre..."
                                   maxlength="200">
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Notes supplémentaires</label>
                            <textarea name="notes" class="form-control" rows="2"
                                      placeholder="Informations utiles pour le médecin (optionnel)"
                                      maxlength="500"></textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="col-12">
                            <button type="submit" id="submitBtn" class="btn btn-success w-100 fw-semibold" disabled>
                                <i class="bi bi-calendar-check me-2"></i>Confirmer le rendez-vous
                                @if($patient->email)
                                    &amp; recevoir confirmation par email
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Historique RDV passés --}}
        @if($pastAppts->count())
        <div class="section-card mt-3">
            <div class="section-card-header">
                <h6 class="fw-bold mb-0 small text-muted">
                    <i class="bi bi-clock-history me-2"></i>Historique des rendez-vous passés
                </h6>
            </div>
            @foreach($pastAppts as $appt)
            <div class="appt-item" style="opacity:.75">
                <div class="date-box" style="background:#f1f5f9">
                    <div class="day" style="color:#64748b">{{ $appt->appointment_date->format('d') }}</div>
                    <div class="month" style="color:#94a3b8">{{ $appt->appointment_date->isoFormat('MMM') }}</div>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold small">Dr. {{ $appt->doctor?->name ?? '—' }}</div>
                    <div class="text-muted small">{{ $appt->appointment_date->format('d/m/Y H:i') }}</div>
                </div>
                {!! $appt->status_badge !!}
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
(function () {
    const doctorSelect = document.getElementById('doctorSelect');
    const dateInput    = document.getElementById('dateInput');
    const slotsSection = document.getElementById('slotsSection');
    const slotsContainer = document.getElementById('slotsContainer');
    const noSlotsMsg   = document.getElementById('noSlotsMsg');
    const finalInput   = document.getElementById('appointmentDateFinal');
    const submitBtn    = document.getElementById('submitBtn');

    let selectedSlot = null;

    function loadSlots() {
        const doctorId = doctorSelect.value;
        const date     = dateInput.value;
        if (!doctorId || !date) return;

        slotsSection.style.display = 'block';
        slotsContainer.innerHTML = '<span class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>Chargement des créneaux...</span>';
        noSlotsMsg.style.display = 'none';
        finalInput.value = '';
        submitBtn.disabled = true;
        selectedSlot = null;

        fetch(`{{ route('appointments.available-slots') }}?doctor_id=${doctorId}&date=${date}`)
            .then(r => r.json())
            .then(data => {
                slotsContainer.innerHTML = '';
                if (!data.slots || data.slots.length === 0) {
                    noSlotsMsg.style.display = 'block';
                    return;
                }
                data.slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'slot-btn';
                    btn.textContent = slot;
                    btn.onclick = function () {
                        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                        btn.classList.add('selected');
                        finalInput.value = date + ' ' + slot + ':00';
                        submitBtn.disabled = false;
                        selectedSlot = slot;
                    };
                    slotsContainer.appendChild(btn);
                });
            })
            .catch(() => {
                slotsContainer.innerHTML = '<span class="text-danger small"><i class="bi bi-x-circle me-1"></i>Erreur de chargement.</span>';
            });
    }

    if (doctorSelect) doctorSelect.addEventListener('change', loadSlots);
    if (dateInput)    dateInput.addEventListener('change', loadSlots);

    // Form validation
    const form = document.getElementById('bookingForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!finalInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un créneau horaire.');
            }
        });
    }

    // Smooth scroll to booking
    document.querySelectorAll('a[href="#booking-section"]').forEach(a => {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('booking-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
})();
</script>
@endpush
