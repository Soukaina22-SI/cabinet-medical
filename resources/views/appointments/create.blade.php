{{-- resources/views/appointments/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Nouveau Rendez-vous')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Rendez-vous</a></li>
    <li class="breadcrumb-item active">Nouveau</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-7">

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-light rounded-3">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Nouveau rendez-vous</h5>
        <p class="text-muted small mb-0">Réserver un créneau pour un patient</p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm">
@csrf

<div class="stat-card mb-3">
    <h6 class="fw-semibold mb-3 text-primary"><i class="bi bi-people me-2"></i>Patient & Médecin</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Patient *</label>
            <select name="patient_id" id="patientSelect" class="form-select" required>
                <option value="">-- Sélectionner un patient --</option>
                @foreach($patients as $p)
                    <option value="{{ $p->id }}" {{ request('patient_id') == $p->id || old('patient_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->full_name }} ({{ $p->cin ?? $p->phone }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Médecin *</label>
            <select name="doctor_id" id="doctorSelect" class="form-select" required>
                <option value="">-- Sélectionner un médecin --</option>
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}" {{ old('doctor_id') == $d->id ? 'selected' : '' }}>
                        Dr. {{ $d->name }} — {{ $d->speciality ?? 'Généraliste' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="stat-card mb-3">
    <h6 class="fw-semibold mb-3 text-success"><i class="bi bi-calendar3 me-2"></i>Date & Créneau</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Date *</label>
            <input type="date" id="dateInput" class="form-control"
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                   value="{{ old('date') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Heure *</label>
            <select name="appointment_time" id="timeSelect" class="form-select" disabled required>
                <option value="">-- Sélectionner d'abord médecin & date --</option>
            </select>
            <div id="loadingSlots" class="text-muted small mt-1" style="display:none">
                <i class="bi bi-arrow-repeat spin me-1"></i>Chargement des créneaux...
            </div>
        </div>
        {{-- Hidden: combined datetime --}}
        <input type="hidden" name="appointment_date" id="appointmentDateHidden">
    </div>
</div>

<div class="stat-card mb-4">
    <h6 class="fw-semibold mb-3"><i class="bi bi-chat-square-text me-2"></i>Informations complémentaires</h6>
    <div class="mb-3">
        <label class="form-label small fw-semibold">Motif de la consultation</label>
        <textarea name="reason" class="form-control" rows="2"
                  placeholder="Douleur abdominale, contrôle de routine...">{{ old('reason') }}</textarea>
    </div>
    <div>
        <label class="form-label small fw-semibold">Notes (optionnel)</label>
        <textarea name="notes" class="form-control" rows="2"
                  placeholder="Notes pour le médecin...">{{ old('notes') }}</textarea>
    </div>
</div>

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
    <button type="submit" class="btn btn-primary px-5" id="submitBtn" disabled>
        <i class="bi bi-calendar-check me-1"></i>Confirmer le RDV
    </button>
</div>

</form>
</div>
</div>
@endsection

@push('scripts')
<style>
.spin { animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.slot-btn { cursor:pointer; transition:all .15s; }
.slot-btn:hover { background:#3b82f6!important; color:#fff!important; }
.slot-btn.selected { background:#3b82f6!important; color:#fff!important; border-color:#3b82f6!important; }
</style>
<script>
const doctorSelect = document.getElementById('doctorSelect');
const dateInput    = document.getElementById('dateInput');
const timeSelect   = document.getElementById('timeSelect');
const submitBtn    = document.getElementById('submitBtn');
const hiddenDate   = document.getElementById('appointmentDateHidden');

async function loadSlots() {
    const doctorId = doctorSelect.value;
    const date     = dateInput.value;

    if (!doctorId || !date) return;

    document.getElementById('loadingSlots').style.display = 'block';
    timeSelect.disabled = true;
    timeSelect.innerHTML = '<option value="">Chargement...</option>';
    submitBtn.disabled = true;

    try {
        const res  = await fetch(`{{ route('appointments.available-slots') }}?doctor_id=${doctorId}&date=${date}`);
        const data = await res.json();

        document.getElementById('loadingSlots').style.display = 'none';

        if (data.slots.length === 0) {
            timeSelect.innerHTML = '<option value="">Aucun créneau disponible ce jour</option>';
            return;
        }

        timeSelect.innerHTML = '<option value="">-- Choisir un horaire --</option>';
        data.slots.forEach(slot => {
            const opt = document.createElement('option');
            opt.value = slot;
            opt.textContent = slot;
            timeSelect.appendChild(opt);
        });
        timeSelect.disabled = false;

    } catch (e) {
        timeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        document.getElementById('loadingSlots').style.display = 'none';
    }
}

timeSelect.addEventListener('change', () => {
    if (timeSelect.value) {
        hiddenDate.value = dateInput.value + ' ' + timeSelect.value + ':00';
        submitBtn.disabled = false;
    } else {
        submitBtn.disabled = true;
    }
});

doctorSelect.addEventListener('change', loadSlots);
dateInput.addEventListener('change', loadSlots);
</script>
@endpush
