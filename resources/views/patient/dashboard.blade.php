{{-- resources/views/patient/dashboard.blade.php --}}
@extends('layouts.app')
@section('title', 'Mon Espace Patient')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">
            <i class="bi bi-house-heart text-primary me-2"></i>
            Bonjour, {{ auth()->user()->prenom }} 👋
        </h4>
        <p class="text-muted small mb-0">Bienvenue dans votre espace patient</p>
    </div>
    <a href="{{ route('rendezvous.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus me-2"></i>Prendre un RDV
    </a>
</div>

{{-- Carte infos rapides --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 border-start border-primary border-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px">
                    <i class="bi bi-calendar3 text-primary fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Prochain RDV</div>
                    @php
                        $prochainRdv = $rdvs->where('statut', '!=', 'annule')
                                           ->where('date_heure', '>=', now())
                                           ->sortBy('date_heure')->first();
                    @endphp
                    @if($prochainRdv)
                        <div class="fw-semibold small">{{ $prochainRdv->date_heure->format('d/m/Y à H:i') }}</div>
                        <div class="text-muted" style="font-size:11px">Dr. {{ $prochainRdv->medecin->user->nom_complet }}</div>
                    @else
                        <div class="fw-semibold small text-muted">Aucun à venir</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-start border-success border-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px">
                    <i class="bi bi-clipboard2-pulse text-success fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Consultations</div>
                    <div class="fw-bold fs-4">{{ $consultations->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 border-start border-warning border-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                     style="width:48px;height:48px">
                    <i class="bi bi-file-earmark-medical text-warning fs-5"></i>
                </div>
                <div>
                    <div class="text-muted small">Ordonnances</div>
                    <div class="fw-bold fs-4">
                        {{ $consultations->filter(fn($c) => $c->ordonnance)->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Mes rendez-vous --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar3 text-primary me-2"></i>Mes rendez-vous</h6>
                <a href="{{ route('rendezvous.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                @forelse($rdvs as $rdv)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="text-center bg-light rounded px-2 py-1" style="min-width:52px">
                        <div class="fw-bold text-primary" style="font-size:18px">{{ $rdv->date_heure->format('d') }}</div>
                        <div class="text-muted" style="font-size:10px;text-transform:uppercase">{{ $rdv->date_heure->format('M') }}</div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">Dr. {{ $rdv->medecin->user->nom_complet }}</div>
                        <div class="text-muted" style="font-size:12px">{{ $rdv->medecin->specialite }} · {{ $rdv->date_heure->format('H:i') }}</div>
                        <div class="text-muted" style="font-size:11px">{{ Str::limit($rdv->motif, 40) }}</div>
                    </div>
                    @php
                        $badgeClass = match($rdv->statut) {
                            'confirme'   => 'success',
                            'annule'     => 'danger',
                            'termine'    => 'secondary',
                            default      => 'warning',
                        };
                        $labels = ['en_attente'=>'En attente','confirme'=>'Confirmé','annule'=>'Annulé','termine'=>'Terminé'];
                    @endphp
                    <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }}-emphasis border border-{{ $badgeClass }}-subtle" style="font-size:10px">
                        {{ $labels[$rdv->statut] ?? $rdv->statut }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                    <span class="small">Aucun rendez-vous</span>
                    <div class="mt-2">
                        <a href="{{ route('rendezvous.create') }}" class="btn btn-sm btn-primary">Prendre un RDV</a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Mes consultations --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-clipboard2-pulse text-success me-2"></i>Mes consultations</h6>
            </div>
            <div class="card-body p-0">
                @forelse($consultations as $consultation)
                <div class="px-3 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold small">Dr. {{ $consultation->medecin->user->nom_complet }}</div>
                            <div class="text-muted" style="font-size:12px">{{ $consultation->date_heure->format('d/m/Y') }}</div>
                            <div class="text-muted mt-1" style="font-size:12px">
                                <i class="bi bi-stethoscope me-1"></i>{{ Str::limit($consultation->diagnostic, 50) }}
                            </div>
                        </div>
                        @if($consultation->ordonnance)
                        <a href="{{ route('ordonnances.pdf', $consultation->ordonnance) }}"
                           class="btn btn-sm btn-outline-danger ms-2" title="Télécharger l'ordonnance">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-50"></i>
                    <span class="small">Aucune consultation</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Dossier médical --}}
    @if($patient->dossierMedical)
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-folder2-open text-warning me-2"></i>Mon dossier médical</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3">
                            <div class="small text-muted mb-1">Groupe sanguin</div>
                            <div class="fw-bold fs-5 text-danger">
                                {{ $patient->dossierMedical->groupe_sanguin ?? '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3">
                            <div class="small text-muted mb-1">Allergies</div>
                            <div class="small fw-semibold">
                                {{ $patient->dossierMedical->allergies ?? 'Aucune connue' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3">
                            <div class="small text-muted mb-1">Antécédents</div>
                            <div class="small fw-semibold">
                                {{ $patient->dossierMedical->antecedents ?? 'Aucun connu' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection