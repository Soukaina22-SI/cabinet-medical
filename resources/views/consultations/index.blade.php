{{-- resources/views/consultations/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Consultations')

@section('breadcrumb')
    <li class="breadcrumb-item active">Consultations</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Consultations</h5>
        <p class="text-muted small mb-0">{{ $consultations->total() }} consultations enregistrées</p>
    </div>
</div>

{{-- Filters --}}
<div class="stat-card mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small">Recherche patient</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Nom du patient...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small">Médecin</label>
            <select name="doctor_id" class="form-select">
                <option value="">Tous les médecins</option>
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}" {{ request('doctor_id') == $d->id ? 'selected':'' }}>
                        Dr. {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary">Filtrer</button>
            @if(request()->hasAny(['search','doctor_id','date']))
                <a href="{{ route('consultations.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Diagnostic</th>
                    <th>Ordonnance</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultations as $c)
                <tr>
                    <td><small class="text-muted">{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</small></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initials"
                                 style="width:34px;height:34px;font-size:.75rem;font-weight:700;
                                        background:#dbeafe;color:#1d4ed8">
                                {{ $c->patient ? strtoupper(substr($c->patient?->first_name ?? "?",0,1).substr($c->patient?->last_name ?? "",0,1)) : '?' }}
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $c->patient?->full_name ?? 'Patient inconnu' }}</div>
                                <small class="text-muted">{{ $c->patient?->age ?? "—" }} ans</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $c->doctor?->avatar_url ?? '' }}" class="rounded-circle"
                                 width="28" height="28">
                            <small>Dr. {{ $c->doctor?->name ?? '—' }}</small>
                        </div>
                    </td>
                    <td>
                        <small>{{ Str::limit($c->diagnosis, 45) }}</small>
                    </td>
                    <td>
                        @if($c->prescription)
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-check-circle me-1"></i>{{ $c->prescription->items->count() }} méd.
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <small>{{ $c->created_at->format('d/m/Y') }}</small><br>
                        <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('consultations.show', $c) }}" class="btn btn-sm btn-light">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($c->prescription)
                        <a href="{{ route('prescriptions.download', $c) }}"
                           class="btn btn-sm btn-light text-danger" title="PDF">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard2-x fs-2 d-block mb-2"></i>
                        Aucune consultation trouvée
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($consultations->hasPages())
    <div class="p-3 d-flex justify-content-between align-items-center border-top">
        <small class="text-muted">
            {{ $consultations->firstItem() }}–{{ $consultations->lastItem() }} sur {{ $consultations->total() }}
        </small>
        {{ $consultations->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
