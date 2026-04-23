{{-- resources/views/admin/patients/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Patients')
@section('breadcrumb')
    <li class="breadcrumb-item active">Patients</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Gestion des Patients</h5>
        <p class="text-muted small mb-0">{{ $patients->total() }} patients enregistrés</p>
    </div>
    <a href="{{ route('admin.patients.create') }}" class="btn btn-primary rounded-3">
        <i class="bi bi-plus-lg me-1"></i> Nouveau Patient
    </a>
</div>

{{-- Search --}}
<div class="table-card mb-4">
    <div class="p-3 border-bottom">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control" placeholder="Nom, CIN, téléphone...">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary px-4">Rechercher</button>
                @if(request('search'))
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x"></i> Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>CIN</th>
                    <th>Téléphone</th>
                    <th>Date de naissance</th>
                    <th>RDV</th>
                    <th>Consultations</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                 style="width:38px;height:38px;background:{{ $patient->gender==='male'?'#dbeafe':'#fce7f3' }};
                                        color:{{ $patient->gender==='male'?'#1d4ed8':'#be185d' }};font-size:.8rem">
                                {{ strtoupper(substr($patient->first_name,0,1).substr($patient->last_name,0,1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $patient->full_name }}</div>
                                <small class="text-muted">{{ $patient->email ?? '—' }}</small>
                            </div>
                        </div>
                    </td>
                    <td><code>{{ $patient->cin ?? '—' }}</code></td>
                    <td>{{ $patient->phone }}</td>
                    <td>{{ $patient->date_of_birth->format('d/m/Y') }}
                        <small class="text-muted">({{ $patient->age }} ans)</small>
                    </td>
                    <td><span class="badge bg-info-subtle text-info">{{ $patient->appointments_count ?? $patient->appointments->count() }}</span></td>
                    <td><span class="badge bg-purple-subtle" style="background:#ede9fe;color:#6d28d9">{{ $patient->consultations->count() }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-light" title="Voir">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-sm btn-light" title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer ce patient ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger" title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-2 d-block mb-2"></i>
                        @if(request('search'))
                            Aucun résultat pour "{{ request('search') }}"
                        @else
                            Aucun patient enregistré
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($patients->hasPages())
    <div class="p-3 d-flex justify-content-between align-items-center border-top">
        <small class="text-muted">
            Affichage {{ $patients->firstItem() }}–{{ $patients->lastItem() }} sur {{ $patients->total() }}
        </small>
        {{ $patients->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
