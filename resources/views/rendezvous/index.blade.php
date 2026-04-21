{{-- resources/views/rendezvous/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Mes Rendez-vous')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-calendar3 text-primary me-2"></i>
        @if(auth()->user()->isPatient())
            Mes rendez-vous
        @elseif(auth()->user()->isMedecin())
            Mon planning
        @else
            Tous les rendez-vous
        @endif
    </h4>

    @if(auth()->user()->isPatient())
    <a href="{{ route('rendezvous.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus me-2"></i>Prendre un RDV
    </a>
    @endif
</div>

{{-- Filtres rapides (admin/secrétaire) --}}
@if(auth()->user()->isAdmin() || auth()->user()->isSecretaire())
<div class="card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">Statut</label>
            <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                @foreach(['en_attente'=>'En attente','confirme'=>'Confirmé','annule'=>'Annulé','termine'=>'Terminé'] as $val => $label)
                    <option value="{{ $val }}" {{ request('statut') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold mb-1">Date</label>
            <input type="date" name="date" class="form-control form-control-sm"
                   value="{{ request('date') }}" onchange="this.form.submit()">
        </div>
        <div class="col-md-2">
            <a href="{{ route('rendezvous.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi bi-x-circle me-1"></i>Réinitialiser
            </a>
        </div>
    </form>
</div>
@endif

{{-- Tableau --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    @if(!auth()->user()->isPatient())
                        <th>Patient</th>
                    @endif
                    @if(!auth()->user()->isMedecin())
                        <th>Médecin</th>
                    @endif
                    <th>Date & Heure</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rdvs as $rdv)
                <tr>
                    <td class="ps-3 text-muted small">{{ $rdv->id }}</td>

                    @if(!auth()->user()->isPatient())
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-teal-subtle rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:32px;height:32px;background:#e1f5ee">
                                <i class="bi bi-person-fill text-success" style="font-size:13px"></i>
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $rdv->patient->user->nom_complet }}</div>
                                <div class="text-muted" style="font-size:11px">{{ $rdv->patient->user->telephone ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    @endif

                    @if(!auth()->user()->isMedecin())
                    <td>
                        <div class="fw-semibold small">Dr. {{ $rdv->medecin->user->nom_complet }}</div>
                        <div class="text-muted" style="font-size:11px">{{ $rdv->medecin->specialite }}</div>
                    </td>
                    @endif

                    <td>
                        <div class="fw-semibold small">
                            <i class="bi bi-calendar2 text-primary me-1"></i>
                            {{ $rdv->date_heure->format('d/m/Y') }}
                        </div>
                        <div class="text-muted" style="font-size:12px">
                            <i class="bi bi-clock me-1"></i>{{ $rdv->date_heure->format('H:i') }}
                        </div>
                    </td>

                    <td>
                        <span class="small text-muted">{{ Str::limit($rdv->motif, 40) }}</span>
                    </td>

                    <td>
                        @php
                            $cfg = match($rdv->statut) {
                                'confirme'   => ['success', 'check-circle-fill', 'Confirmé'],
                                'annule'     => ['danger',  'x-circle-fill',     'Annulé'],
                                'termine'    => ['secondary','check2-all',        'Terminé'],
                                default      => ['warning', 'hourglass-split',    'En attente'],
                            };
                        @endphp
                        <span class="badge rounded-pill bg-{{ $cfg[0] }}-subtle text-{{ $cfg[0] }}-emphasis border border-{{ $cfg[0] }}-subtle px-2 py-1" style="font-size:11px">
                            <i class="bi bi-{{ $cfg[1] }} me-1"></i>{{ $cfg[2] }}
                        </span>
                    </td>

                    <td class="text-end pe-3">
                        <div class="d-flex justify-content-end gap-1">

                            {{-- Voir détail --}}
                            <a href="{{ route('rendezvous.show', $rdv) }}"
                               class="btn btn-sm btn-outline-secondary" title="Détails">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- Confirmer (admin/secrétaire/médecin si en attente) --}}
                            @if($rdv->statut === 'en_attente' && !auth()->user()->isPatient())
                            <form method="POST" action="{{ route('rendezvous.confirmer', $rdv) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success" title="Confirmer">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Lancer consultation (médecin, rdv confirmé) --}}
                            @if(auth()->user()->isMedecin() && $rdv->statut === 'confirme' && !$rdv->consultation)
                            <a href="{{ route('consultations.create', $rdv) }}"
                               class="btn btn-sm btn-outline-primary" title="Démarrer consultation">
                                <i class="bi bi-clipboard2-pulse"></i>
                            </a>
                            @endif

                            {{-- Annuler (si pas encore terminé/annulé) --}}
                            @if(!in_array($rdv->statut, ['annule', 'termine']))
                            <form method="POST" action="{{ route('rendezvous.annuler', $rdv) }}"
                                  onsubmit="return confirm('Annuler ce rendez-vous ?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-40"></i>
                        <span class="small">Aucun rendez-vous trouvé</span>
                        @if(auth()->user()->isPatient())
                            <div class="mt-2">
                                <a href="{{ route('rendezvous.create') }}" class="btn btn-sm btn-primary">
                                    Prendre un rendez-vous
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rdvs->hasPages())
    <div class="card-footer bg-transparent py-3 d-flex justify-content-between align-items-center">
        <span class="text-muted small">
            {{ $rdvs->firstItem() }}–{{ $rdvs->lastItem() }} sur {{ $rdvs->total() }} rendez-vous
        </span>
        {{ $rdvs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection