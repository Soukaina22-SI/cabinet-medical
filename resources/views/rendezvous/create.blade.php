{{-- resources/views/rendezvous/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Prendre un rendez-vous')

@push('styles')
<style>
.medecin-card {
    cursor: pointer;
    border: 2px solid transparent;
    transition: all .2s;
    border-radius: 12px;
}
.medecin-card:hover { border-color: #1a6fa0; background: #f0f7ff; }
.medecin-card.selected { border-color: #1a6fa0; background: #e8f4fd; }

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
}
.cal-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 13px;
    cursor: pointer;
    border: 1px solid transparent;
    transition: all .15s;
    font-weight: 500;
}
.cal-day.empty    { cursor: default; }
.cal-day.past     { color: #ccc; cursor: not-allowed; }
.cal-day.today    { border-color: #1a6fa0; color: #1a6fa0; font-weight: 700; }
.cal-day.has-slot { background: #e8f4fd; color: #1a6fa0; }
.cal-day.has-slot:hover { background: #1a6fa0; color: white; }
.cal-day.selected { background: #1a6fa0 !important; color: white !important; }
.cal-day.no-slot  { background: #f8f8f8; color: #bbb; cursor: not-allowed; }
.cal-day.loading  { background: #f0f0f0; cursor: wait; }

.slot-btn {
    border: 1.5px solid #dee2e6;
    background: white;
    border-radius: 8px;
    padding: 8px 4px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    text-align: center;
    width: 100%;
}
.slot-btn:hover:not(:disabled)   { border-color: #1a6fa0; background: #e8f4fd; color: #1a6fa0; }
.slot-btn.selected                { background: #1a6fa0; border-color: #1a6fa0; color: white; }
.slot-btn:disabled                { background: #f5f5f5; color: #ccc; cursor: not-allowed; border-color: #eee; }

.step-indicator {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 24px;
}
.step {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 500;
    color: #aaa;
}
.step.active  { color: #1a6fa0; }
.step.done    { color: #198754; }
.step-circle  {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    flex-shrink: 0;
}
.step.active  .step-circle { background: #1a6fa0; color: white; }
.step.done    .step-circle { background: #198754; color: white; }
.step-line    { flex: 1; height: 2px; background: #e9ecef; margin: 0 8px; }
.step-line.done { background: #198754; }

#recap-box {
    background: #f0f7ff;
    border: 1.5px solid #b6d8f2;
    border-radius: 12px;
    padding: 16px;
    font-size: 13px;
}
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-calendar-plus text-primary me-2"></i>Prendre un rendez-vous
    </h4>
    <a href="{{ route('rendezvous.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

{{-- Étapes --}}
<div class="step-indicator">
    <div class="step active" id="step-ind-1">
        <div class="step-circle">1</div>
        <span>Médecin</span>
    </div>
    <div class="step-line" id="line-1"></div>
    <div class="step" id="step-ind-2">
        <div class="step-circle">2</div>
        <span>Date & Créneau</span>
    </div>
    <div class="step-line" id="line-2"></div>
    <div class="step" id="step-ind-3">
        <div class="step-circle">3</div>
        <span>Confirmation</span>
    </div>
</div>

<form method="POST" action="{{ route('rendezvous.store') }}" id="formRdv">
@csrf
<input type="hidden" name="medecin_id"  id="input_medecin_id">
<input type="hidden" name="date_heure"  id="input_date_heure">
<input type="hidden" name="motif"       id="input_motif_hidden">

<div class="row g-3">

    {{-- ═══════════════════════════════════════ --}}
    {{-- ÉTAPE 1 — Choisir un médecin           --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="col-12" id="step1">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">1</span>Choisissez un médecin</h6>
            <div class="row g-3">
                @foreach($medecins as $m)
                <div class="col-md-4">
                    <div class="medecin-card p-3 d-flex align-items-center gap-3"
                         onclick="selectMedecin({{ $m->id }}, '{{ $m->user->nom_complet }}', '{{ $m->specialite }}', {{ $m->duree_rdv }})">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px">
                            <i class="bi bi-person-badge text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Dr. {{ $m->user->nom_complet }}</div>
                            <div class="text-muted" style="font-size:12px">{{ $m->specialite }}</div>
                            <div class="text-muted" style="font-size:11px">
                                <i class="bi bi-clock me-1"></i>Consultation {{ $m->duree_rdv }} min
                            </div>
                        </div>
                        <i class="bi bi-check-circle-fill text-primary ms-auto" id="check-{{ $m->id }}" style="display:none;font-size:18px"></i>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- ÉTAPE 2 — Calendrier + créneaux        --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="col-12" id="step2" style="display:none">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">2</span>Choisissez une date et un créneau</h6>
            <div class="row g-4">

                {{-- Calendrier --}}
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeMonth(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <span class="fw-semibold" id="cal-title"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeMonth(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    {{-- Jours de la semaine --}}
                    <div class="calendar-grid mb-1">
                        @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $j)
                            <div style="text-align:center;font-size:11px;font-weight:600;color:#888;padding:4px 0">{{ $j }}</div>
                        @endforeach
                    </div>

                    {{-- Jours du mois --}}
                    <div class="calendar-grid" id="cal-grid"></div>

                    <div class="d-flex gap-3 mt-3" style="font-size:11px">
                        <span><span style="display:inline-block;width:12px;height:12px;background:#e8f4fd;border-radius:3px;margin-right:4px"></span>Disponible</span>
                        <span><span style="display:inline-block;width:12px;height:12px;background:#1a6fa0;border-radius:3px;margin-right:4px"></span>Sélectionné</span>
                        <span><span style="display:inline-block;width:12px;height:12px;background:#f8f8f8;border:1px solid #eee;border-radius:3px;margin-right:4px"></span>Indisponible</span>
                    </div>
                </div>

                {{-- Créneaux horaires --}}
                <div class="col-md-6">
                    <div id="slots-section">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-calendar2-event fs-2 d-block mb-2 opacity-40"></i>
                            <span class="small">Sélectionnez une date pour voir les créneaux</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════ --}}
    {{-- ÉTAPE 3 — Motif + confirmation         --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="col-12" id="step3" style="display:none">
        <div class="card p-4" style="max-width:580px">
            <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">3</span>Confirmer le rendez-vous</h6>

            {{-- Récapitulatif --}}
            <div id="recap-box" class="mb-4">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em">Médecin</div>
                        <div class="fw-semibold small" id="recap-medecin">—</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em">Spécialité</div>
                        <div class="fw-semibold small" id="recap-specialite">—</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em">Date</div>
                        <div class="fw-semibold small" id="recap-date">—</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em">Heure</div>
                        <div class="fw-semibold small" id="recap-heure">—</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Motif de la consultation <span class="text-danger">*</span></label>
                <textarea id="motif-input" class="form-control" rows="3"
                          placeholder="Décrivez brièvement votre motif (douleur, suivi, bilan...)"
                          required oninput="document.getElementById('input_motif_hidden').value=this.value"></textarea>
                <div class="invalid-feedback">Veuillez indiquer le motif.</div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                    <i class="bi bi-arrow-left me-1"></i>Modifier
                </button>
                <button type="submit" class="btn btn-primary flex-grow-1" id="btn-submit">
                    <i class="bi bi-check2-circle me-2"></i>Confirmer le rendez-vous
                </button>
            </div>

            <p class="text-muted small mt-3 mb-0">
                <i class="bi bi-envelope me-1"></i>Un email de confirmation vous sera envoyé.
            </p>
        </div>
    </div>

</div>
</form>
@endsection

@push('scripts')
<script>
// ── État global ───────────────────────────────────────────
const state = {
    medecinId:   null,
    medecinNom:  '',
    specialite:  '',
    dureeRdv:    30,
    year:        new Date().getFullYear(),
    month:       new Date().getMonth(), // 0-indexed
    selectedDate: null,
    selectedHeure: null,
    // cache: { 'YYYY-MM-DD': [{heure, disponible}, ...] }
    cache: {},
    // dates ayant au moins 1 créneau : Set
    datesWithSlots: new Set(),
};

const moisNoms = ['Janvier','Février','Mars','Avril','Mai','Juin',
                  'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

// ── Sélection médecin ─────────────────────────────────────
function selectMedecin(id, nom, specialite, duree) {
    // Retirer l'ancienne sélection visuelle
    document.querySelectorAll('.medecin-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('[id^="check-"]').forEach(i => i.style.display = 'none');

    state.medecinId  = id;
    state.medecinNom = nom;
    state.specialite = specialite;
    state.dureeRdv   = duree;
    state.cache      = {};
    state.datesWithSlots.clear();
    state.selectedDate  = null;
    state.selectedHeure = null;

    // Marquer la carte
    event.currentTarget.classList.add('selected');
    document.getElementById('check-' + id).style.display = 'inline';

    document.getElementById('input_medecin_id').value = id;

    // Afficher étape 2
    document.getElementById('step2').style.display = '';
    document.getElementById('step3').style.display = 'none';

    setStepIndicator(2);
    renderCalendar();
    document.getElementById('step2').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Calendrier ────────────────────────────────────────────
function renderCalendar() {
    const { year, month } = state;
    document.getElementById('cal-title').textContent = moisNoms[month] + ' ' + year;

    const grid      = document.getElementById('cal-grid');
    const today     = new Date(); today.setHours(0,0,0,0);
    const firstDay  = new Date(year, month, 1).getDay(); // 0=dim
    // Convertir pour lundi en premier (0=lun)
    const startOffset = (firstDay === 0) ? 6 : firstDay - 1;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    grid.innerHTML = '';

    // Cellules vides avant le 1er
    for (let i = 0; i < startOffset; i++) {
        const el = document.createElement('div');
        el.className = 'cal-day empty';
        grid.appendChild(el);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const date    = new Date(year, month, d);
        const dateStr = formatDate(date);
        const isPast  = date < today;
        const isToday = date.getTime() === today.getTime();
        const isSelected = dateStr === state.selectedDate;

        const el = document.createElement('div');
        el.textContent = d;

        if (isPast) {
            el.className = 'cal-day past';
        } else {
            el.className = 'cal-day has-slot';
            // Si on a déjà le cache pour ce mois
            if (state.datesWithSlots.size > 0) {
                if (!state.datesWithSlots.has(dateStr)) {
                    el.className = 'cal-day no-slot';
                }
            }
            if (isSelected) el.className = 'cal-day selected';
            if (isToday && !isSelected) el.classList.add('today');
            el.onclick = () => selectDate(dateStr, el);
        }

        grid.appendChild(el);
    }
}

async function selectDate(dateStr, el) {
    // Mise à jour visuelle immédiate
    document.querySelectorAll('.cal-day.selected').forEach(d => {
        d.classList.remove('selected');
        d.classList.add('has-slot');
    });
    el.className = 'cal-day selected';

    state.selectedDate  = dateStr;
    state.selectedHeure = null;
    document.getElementById('input_date_heure').value = '';

    renderSlots(null); // loading

    const slots = await fetchSlots(dateStr);
    renderSlots(slots, dateStr);
}

async function fetchSlots(dateStr) {
    if (state.cache[dateStr]) return state.cache[dateStr];

    try {
        const res   = await fetch(`/rendezvous/disponibilites?medecin_id=${state.medecinId}&date=${dateStr}`);
        const slots = await res.json();
        state.cache[dateStr] = slots;

        // Mettre à jour datesWithSlots
        const hasAvail = slots.some(s => s.disponible);
        if (hasAvail) state.datesWithSlots.add(dateStr);
        else          state.datesWithSlots.delete(dateStr);

        return slots;
    } catch (e) {
        return [];
    }
}

function renderSlots(slots, dateStr) {
    const section = document.getElementById('slots-section');

    if (slots === null) {
        section.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                <span class="small text-muted">Chargement des créneaux...</span>
            </div>`;
        return;
    }

    if (!slots.length) {
        section.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-40"></i>
                <span class="small">Aucun créneau ce jour</span>
            </div>`;
        return;
    }

    const available = slots.filter(s => s.disponible);
    const taken     = slots.filter(s => !s.disponible);

    // Formatage de la date en français
    const [y, m, d] = dateStr.split('-');
    const dateObj = new Date(y, m - 1, d);
    const dateLabel = dateObj.toLocaleDateString('fr-FR', { weekday:'long', day:'numeric', month:'long' });

    let html = `<div class="fw-semibold small mb-3 text-capitalize">${dateLabel}</div>`;
    html += `<div class="text-muted small mb-2">
                ${available.length} créneau${available.length > 1 ? 'x' : ''} disponible${available.length > 1 ? 's' : ''}
                sur ${slots.length}
             </div>`;

    if (!available.length) {
        html += `<div class="alert alert-warning py-2 small">
                    <i class="bi bi-calendar-x me-1"></i>
                    Tous les créneaux sont pris pour cette date.
                 </div>`;
    } else {
        // Matin / Après-midi
        const matin   = available.filter(s => parseInt(s.heure) < 12);
        const apresmidi = available.filter(s => parseInt(s.heure) >= 12);

        if (matin.length) {
            html += `<div class="text-muted small fw-semibold mb-1"><i class="bi bi-sunrise me-1"></i>Matin</div>`;
            html += `<div class="row g-2 mb-3">`;
            matin.forEach(s => {
                const isSel = s.heure === state.selectedHeure;
                html += `<div class="col-4">
                    <button type="button" class="slot-btn ${isSel ? 'selected' : ''}"
                            onclick="selectSlot('${s.heure}', this)">
                        ${s.heure}
                    </button>
                </div>`;
            });
            html += `</div>`;
        }

        if (apresmidi.length) {
            html += `<div class="text-muted small fw-semibold mb-1"><i class="bi bi-sunset me-1"></i>Après-midi</div>`;
            html += `<div class="row g-2">`;
            apresmidi.forEach(s => {
                const isSel = s.heure === state.selectedHeure;
                html += `<div class="col-4">
                    <button type="button" class="slot-btn ${isSel ? 'selected' : ''}"
                            onclick="selectSlot('${s.heure}', this)">
                        ${s.heure}
                    </button>
                </div>`;
            });
            html += `</div>`;
        }
    }

    // Créneaux pris (info)
    if (taken.length) {
        html += `<details class="mt-3">
            <summary class="text-muted small" style="cursor:pointer">
                ${taken.length} créneau${taken.length > 1 ? 'x' : ''} déjà pris
            </summary>
            <div class="row g-2 mt-1">
                ${taken.map(s => `<div class="col-4"><button type="button" class="slot-btn" disabled>${s.heure}</button></div>`).join('')}
            </div>
        </details>`;
    }

    section.innerHTML = html;
}

function selectSlot(heure, btn) {
    document.querySelectorAll('.slot-btn.selected').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    state.selectedHeure = heure;
    const dateHeure = state.selectedDate + ' ' + heure + ':00';
    document.getElementById('input_date_heure').value = dateHeure;

    // Mise à jour récap
    const [y, m, d] = state.selectedDate.split('-');
    const dateObj   = new Date(y, m - 1, d);
    const dateLabel = dateObj.toLocaleDateString('fr-FR', { weekday:'long', day:'numeric', month:'long', year:'numeric' });

    document.getElementById('recap-medecin').textContent   = 'Dr. ' + state.medecinNom;
    document.getElementById('recap-specialite').textContent = state.specialite;
    document.getElementById('recap-date').textContent       = dateLabel;
    document.getElementById('recap-heure').textContent      = heure;

    // Afficher étape 3
    document.getElementById('step3').style.display = '';
    setStepIndicator(3);
    document.getElementById('step3').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Navigation calendrier ─────────────────────────────────
function changeMonth(delta) {
    state.month += delta;
    if (state.month < 0)  { state.month = 11; state.year--; }
    if (state.month > 11) { state.month = 0;  state.year++; }
    renderCalendar();
}

// ── Retour ────────────────────────────────────────────────
function goBack() {
    document.getElementById('step3').style.display = 'none';
    state.selectedHeure = null;
    document.getElementById('input_date_heure').value = '';
    setStepIndicator(2);
    document.getElementById('step2').scrollIntoView({ behavior: 'smooth', block: 'start' });
    // Re-render les créneaux avec la date déjà sélectionnée
    if (state.selectedDate && state.cache[state.selectedDate]) {
        renderSlots(state.cache[state.selectedDate], state.selectedDate);
    }
}

// ── Indicateur d'étapes ───────────────────────────────────
function setStepIndicator(activeStep) {
    for (let i = 1; i <= 3; i++) {
        const el   = document.getElementById('step-ind-' + i);
        const circ = el.querySelector('.step-circle');
        if (i < activeStep) {
            el.className = 'step done';
            circ.innerHTML = '<i class="bi bi-check2" style="font-size:12px"></i>';
        } else if (i === activeStep) {
            el.className = 'step active';
            circ.textContent = i;
        } else {
            el.className = 'step';
            circ.textContent = i;
        }
        if (i < 3) {
            document.getElementById('line-' + i).className = i < activeStep ? 'step-line done' : 'step-line';
        }
    }
}

// ── Validation submit ─────────────────────────────────────
document.getElementById('formRdv').addEventListener('submit', function (e) {
    const motif = document.getElementById('motif-input').value.trim();
    if (!motif) {
        e.preventDefault();
        document.getElementById('motif-input').classList.add('is-invalid');
        return;
    }
    if (!state.medecinId || !state.selectedDate || !state.selectedHeure) {
        e.preventDefault();
        alert('Veuillez compléter toutes les étapes.');
    }
});

// ── Utilitaire ────────────────────────────────────────────
function formatDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}
</script>
@endpush