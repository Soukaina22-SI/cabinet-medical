{{-- resources/views/appointments/show.blade.php --}}
@extends('layouts.app')
@section('title', 'Rendez-vous #' . $appointment->id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('appointments.index') }}">Rendez-vous</a></li>
    <li class="breadcrumb-item active">RDV #{{ $appointment->id }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-light rounded-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h5 class="fw-bold mb-0">Rendez-vous #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</h5>
            <p class="text-muted small mb-0">
                {{ $appointment->appointment_date->translatedFormat('l d F Y à H:i') }}
            </p>
        </div>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        {!! $appointment->status_badge !!}

        @if(auth()->user()->isDoctor() || auth()->user()->isAdmin() || auth()->user()->isSecretary())
            @if(!$appointment->isCancelled())
            <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            @endif

            {{-- 🔔 Bouton rappel email --}}
            @if($appointment->isConfirmed() && $appointment->patient?->email)
            <form method="POST" action="{{ route('appointments.send-reminder', $appointment) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-warning"
                        onclick="return confirm('Envoyer un email de rappel à {{ $appointment->patient?->email }} ?')"
                        title="Envoyer rappel email">
                    <i class="bi bi-bell me-1"></i>
                    {{ $appointment->reminder_sent ? 'Rappel renvoyé' : 'Envoyer rappel' }}
                </button>
            </form>
            @endif

            @if($appointment->isConfirmed() && !$appointment->consultation && auth()->user()->isDoctor())
            <a href="{{ route('consultations.create', $appointment) }}" class="btn btn-sm btn-success">
                <i class="bi bi-clipboard2-pulse me-1"></i>Démarrer consultation
            </a>
            @endif
        @endif
    </div>
</div>

<div class="row g-3">

    {{-- Left: RDV details --}}
    <div class="col-lg-5">

        {{-- Patient card --}}
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3 text-primary">
                <i class="bi bi-person-circle me-2"></i>Patient
            </h6>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                     style="width:48px;height:48px;background:#dbeafe;color:#1d4ed8;font-size:1rem">
                    {{ $appointment->patient ? strtoupper(substr($appointment->patient?->first_name ?? '?',0,1).substr($appointment->patient?->last_name ?? '',0,1)) : '?' }}
                </div>
                <div>
                    <div class="fw-bold">{{ $appointment->patient?->full_name ?? 'Patient inconnu' }}</div>
                    <small class="text-muted">{{ $appointment->patient->age }} ans — {{ $appointment->patient->gender === 'male' ? 'M' : 'F' }}</small>
                </div>
            </div>
            <table class="table table-sm table-borderless small">
                <tr><td class="text-muted">CIN</td><td><code>{{ $appointment->patient->cin ?? '—' }}</code></td></tr>
                <tr><td class="text-muted">Téléphone</td><td>{{ $appointment->patient?->phone ?? '—' }}</td></tr>
                @if($appointment->patient->blood_type)
                <tr><td class="text-muted">Groupe sanguin</td>
                    <td><span class="badge bg-danger">{{ $appointment->patient->blood_type }}</span></td></tr>
                @endif
                @if($appointment->patient->allergies)
                <tr><td class="text-muted">Allergies</td><td class="text-warning small">⚠ {{ $appointment->patient->allergies }}</td></tr>
                @endif
            </table>
            <a href="{{ route('admin.patients.show', $appointment->patient) }}"
               class="btn btn-sm btn-outline-primary w-100">
                Voir dossier complet <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Doctor card --}}
        <div class="stat-card mb-3">
            <h6 class="fw-semibold mb-3 text-success">
                <i class="bi bi-person-badge me-2"></i>Médecin
            </h6>
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $appointment->doctor->avatar_url }}" class="rounded-circle"
                     width="44" height="44" alt="">
                <div>
                    <div class="fw-bold">Dr. {{ $appointment->doctor?->name ?? '—' }}</div>
                    <small class="text-muted">{{ $appointment->doctor?->speciality ?? 'Médecine générale' }}</small>
                </div>
            </div>
        </div>

        {{-- RDV Info --}}
        <div class="stat-card">
            <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2"></i>Détails du RDV</h6>
            <table class="table table-sm table-borderless small">
                <tr>
                    <td class="text-muted">Date & Heure</td>
                    <td class="fw-semibold">{{ $appointment->appointment_date->format('d/m/Y à H:i') }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Statut</td>
                    <td>{!! $appointment->status_badge !!}</td>
                </tr>
                <tr>
                    <td class="text-muted">Motif</td>
                    <td>{{ $appointment->reason ?? '—' }}</td>
                </tr>
                @if($appointment->notes)
                <tr>
                    <td class="text-muted">Notes</td>
                    <td>{{ $appointment->notes }}</td>
                </tr>
                @endif
                <tr>
                    <td class="text-muted">Créé le</td>
                    <td>{{ $appointment->created_at->format('d/m/Y') }}</td>
                </tr>
            </table>

            {{-- Status quick update --}}
            @if(!$appointment->isCompleted())
            <div class="border-top pt-3 mt-2">
                <p class="small text-muted mb-2">Mettre à jour le statut :</p>
                <div class="d-flex gap-2 flex-wrap">
                    @if(!$appointment->isConfirmed())
                    <button onclick="updateStatus('confirmed')"
                            class="btn btn-sm btn-success">
                        <i class="bi bi-check me-1"></i>Confirmer
                    </button>
                    @endif
                    @if(!$appointment->isCancelled())
                    <button onclick="updateStatus('cancelled')"
                            class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-x me-1"></i>Annuler
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>

    </div>

    {{-- Right: Consultation result --}}
    <div class="col-lg-7">

        @if($appointment->consultation)
        @php $c = $appointment->consultation; @endphp
        <div class="stat-card mb-3">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-semibold mb-0 text-success">
                    <i class="bi bi-clipboard2-pulse me-2"></i>Compte-rendu de consultation
                </h6>
                <small class="text-muted">{{ $c->created_at->format('d/m/Y') }}</small>
            </div>

            {{-- Vitals --}}
            @if($c->weight || $c->blood_pressure || $c->temperature)
            <div class="row g-2 mb-3">
                @if($c->weight)
                <div class="col-3 text-center border rounded-3 py-2">
                    <div class="fw-bold">{{ $c->weight }} kg</div>
                    <div class="text-muted" style="font-size:.72rem">Poids</div>
                </div>
                @endif
                @if($c->height)
                <div class="col-3 text-center border rounded-3 py-2">
                    <div class="fw-bold">{{ $c->height }} cm</div>
                    <div class="text-muted" style="font-size:.72rem">Taille</div>
                </div>
                @endif
                @if($c->blood_pressure)
                <div class="col-3 text-center border rounded-3 py-2">
                    <div class="fw-bold">{{ $c->blood_pressure }}</div>
                    <div class="text-muted" style="font-size:.72rem">Tension</div>
                </div>
                @endif
                @if($c->temperature)
                <div class="col-3 text-center border rounded-3 py-2">
                    <div class="fw-bold {{ $c->temperature > 38 ? 'text-danger' : '' }}">{{ $c->temperature }}°C</div>
                    <div class="text-muted" style="font-size:.72rem">Temp.</div>
                </div>
                @endif
            </div>
            @endif

            <div class="mb-3">
                <strong class="small text-muted d-block mb-1" style="text-transform:uppercase;font-size:.72rem">Symptômes</strong>
                <p class="mb-0 small">{{ $c->symptoms }}</p>
            </div>
            <div class="mb-3">
                <strong class="small text-muted d-block mb-1" style="text-transform:uppercase;font-size:.72rem">Diagnostic</strong>
                <p class="mb-0 small fw-semibold">{{ $c->diagnosis }}</p>
            </div>
            @if($c->notes)
            <div>
                <strong class="small text-muted d-block mb-1" style="text-transform:uppercase;font-size:.72rem">Notes</strong>
                <p class="mb-0 small fst-italic">{{ $c->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Prescription --}}
        @if($c->prescription && $c->prescription->items->count())
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold mb-0 text-warning">
                    <i class="bi bi-capsule me-2"></i>Ordonnance
                </h6>
                <a href="{{ route('prescriptions.download', $c) }}"
                   class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Télécharger PDF
                </a>
            </div>
            @foreach($c->prescription->items as $item)
            <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="rounded-2 p-2 text-center" style="background:#fef9c3;min-width:36px">
                    <i class="bi bi-capsule text-warning"></i>
                </div>
                <div>
                    <div class="fw-semibold small">{{ $item->medication_name }}</div>
                    <div class="text-muted small">
                        {{ $item->dosage }} — {{ $item->frequency }} — {{ $item->duration }}
                    </div>
                    @if($item->instructions)
                    <div class="text-muted small fst-italic">{{ $item->instructions }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @else
        {{-- No consultation yet --}}
        <div class="stat-card text-center py-5">
            <i class="bi bi-clipboard2 text-muted" style="font-size:3rem"></i>
            <h6 class="mt-3 text-muted">Aucune consultation enregistrée</h6>
            @if($appointment->isConfirmed() && auth()->user()->isDoctor())
            <a href="{{ route('consultations.create', $appointment) }}"
               class="btn btn-primary mt-3">
                <i class="bi bi-clipboard2-pulse me-1"></i>Démarrer la consultation
            </a>
            @elseif($appointment->isPending())
            <p class="text-muted small mt-2">Le rendez-vous doit être confirmé d'abord.</p>
            @endif
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
async function updateStatus(status) {
    const res = await fetch('{{ route('appointments.update-status', $appointment) }}', {
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
