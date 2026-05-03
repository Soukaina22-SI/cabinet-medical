@extends('layouts.app')
@section('title', 'Gestion des Patients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Patients</h5>
        <p class="text-muted small mb-0">{{ $patients->total() }} patient(s) enregistré(s)</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-2"></i>Nouveau patient
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible rounded-3 mb-3">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filtres --}}
<div class="table-card mb-3 p-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control border-start-0" placeholder="Rechercher par nom, CIN, téléphone...">
            </div>
        </div>
        <div class="col-md-3">
            <select name="gender" class="form-select">
                <option value="">Tous les genres</option>
                <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>👨 Masculin</option>
                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>👩 Féminin</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="bi bi-funnel me-1"></i>Filtrer
            </button>
            @if(request()->hasAny(['search','gender']))
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x"></i>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Patient</th>
                    <th>Téléphone</th>
                    <th>Âge</th>
                    <th>Groupe sang.</th>
                    <th>RDV</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                 style="width:38px;height:38px;background:#eff6ff;color:#3b82f6;font-size:.8rem;flex-shrink:0">
                                {{ strtoupper(substr($patient->first_name,0,1).substr($patient->last_name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold small">{{ $patient->full_name }}</div>
                                <small class="text-muted">{{ $patient->cin ?? $patient->email ?? '—' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="small">{{ $patient->phone }}</td>
                    <td class="small">{{ $patient->age }} ans</td>
                    <td>
                        @if($patient->blood_type)
                            <span class="badge bg-danger">{{ $patient->blood_type }}</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $patient->appointments_count ?? 0 }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('patients.show', $patient) }}"
                               class="btn btn-sm btn-light" title="Voir le dossier">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('patients.edit', $patient) }}"
                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}"
                               class="btn btn-sm btn-outline-success" title="Nouveau RDV">
                                <i class="bi bi-calendar-plus"></i>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('patients.destroy', $patient) }}" class="d-inline"
                                  onsubmit="return confirm('Supprimer {{ $patient->full_name }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-person-x fs-2 d-block mb-2 opacity-40"></i>
                        Aucun patient trouvé
                        <div class="mt-2">
                            <a href="{{ route('patients.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-person-plus me-1"></i>Ajouter un patient
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())
    <div class="p-3 border-top">{{ $patients->links() }}</div>
    @endif
</div>
@endsection
