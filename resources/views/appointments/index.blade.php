{{-- resources/views/appointments/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Rendez-vous')

@section('breadcrumb')
    <li class="breadcrumb-item active">Rendez-vous</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Rendez-vous</h5>
        <p class="text-muted small mb-0">Gestion du planning et des consultations</p>
    </div>
    <a href="{{ route('appointments.create') }}" class="btn btn-primary rounded-3">
        <i class="bi bi-plus-lg me-1"></i> Nouveau RDV
    </a>
</div>

{{-- View Toggle --}}
<div class="d-flex gap-2 mb-3">
    <button class="btn btn-sm btn-primary" id="btnCalendar" onclick="showView('calendar')">
        <i class="bi bi-calendar3 me-1"></i> Calendrier
    </button>
    <button class="btn btn-sm btn-outline-secondary" id="btnList" onclick="showView('list')">
        <i class="bi bi-list-ul me-1"></i> Liste
    </button>
</div>

{{-- ── Calendar View ──────────────────────────────────────── --}}
<div id="calendarView">
    <div class="stat-card">
        <div id="fullCalendar"></div>
    </div>
</div>

{{-- ── List View ───────────────────────────────────────────── --}}
<div id="listView" style="display:none">

    {{-- Filters --}}
    <div class="stat-card mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small">Statut</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    <option value="pending"   {{ request('status')==='pending'   ? 'selected':'' }}>En attente</option>
                    <option value="confirmed" {{ request('status')==='confirmed' ? 'selected':'' }}>Confirmé</option>
                    <option value="cancelled" {{ request('status')==='cancelled' ? 'selected':'' }}>Annulé</option>
                    <option value="completed" {{ request('status')==='completed' ? 'selected':'' }}>Terminé</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary">Filtrer</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Médecin</th>
                        <th>Date & Heure</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $appt->patient?->full_name ?? 'Patient inconnu' }}</span>
                        </td>
                        <td>Dr. {{ $appt->doctor?->name ?? '—' }}</td>
                        <td>
                            <span class="fw-semibold">{{ $appt->appointment_date->format('d/m/Y') }}</span>
                            <span class="badge bg-light text-dark ms-1">{{ $appt->appointment_date->format('H:i') }}</span>
                        </td>
                        <td><small class="text-muted">{{ Str::limit($appt->reason, 30) ?? '—' }}</small></td>
                        <td>{!! $appt->status_badge !!}</td>
                        <td class="text-end">
                            @if($appt->isPending())
                            <button class="btn btn-sm btn-success" onclick="updateStatus({{ $appt->id }}, 'confirmed')">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="updateStatus({{ $appt->id }}, 'cancelled')">
                                <i class="bi bi-x"></i>
                            </button>
                            @endif
                            <a href="{{ route('appointments.show', $appt) }}" class="btn btn-sm btn-light">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>Aucun rendez-vous trouvé
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appointments->hasPages())
        <div class="p-3 border-top">
            {{ $appointments->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── FullCalendar ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const cal = new FullCalendar.Calendar(document.getElementById('fullCalendar'), {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: @json($calendarAppointments),
        eventClick: function(info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        height: 650,
        eventDisplay: 'block',
    });
    cal.render();
});

// ── View toggle ───────────────────────────────────────────────
function showView(view) {
    document.getElementById('calendarView').style.display = view === 'calendar' ? '' : 'none';
    document.getElementById('listView').style.display     = view === 'list'     ? '' : 'none';
    document.getElementById('btnCalendar').className = view === 'calendar'
        ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
    document.getElementById('btnList').className = view === 'list'
        ? 'btn btn-sm btn-primary' : 'btn btn-sm btn-outline-secondary';
}

// ── Status update ─────────────────────────────────────────────
async function updateStatus(id, status) {
    const res = await fetch(`/appointments/${id}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: JSON.stringify({ status })
    });
    if (res.ok) location.reload();
}
</script>
@endpush
