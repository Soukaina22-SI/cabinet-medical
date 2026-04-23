{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('breadcrumb')
    <li class="breadcrumb-item active">Utilisateurs</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Gestion des Utilisateurs</h5>
        <p class="text-muted small mb-0">{{ $users->total() }} utilisateurs enregistrés</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-3">
        <i class="bi bi-plus-lg me-1"></i>Nouvel Utilisateur
    </a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Spécialité</th>
                    <th>RDV</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $user->avatar_url }}" class="rounded-circle"
                                 width="36" height="36" alt="">
                            <div>
                                <div class="fw-semibold small">{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $roleMap = ['admin'=>['Admin','danger'],'medecin'=>['Médecin','success'],
                                        'secretaire'=>['Secrétaire','info'],'patient'=>['Patient','secondary']];
                            [$label,$color] = $roleMap[$user->role] ?? [$user->role,'light'];
                        @endphp
                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">
                            {{ $label }}
                        </span>
                    </td>
                    <td><small class="text-muted">{{ $user->speciality ?? '—' }}</small></td>
                    <td>
                        @if($user->isDoctor())
                            <span class="badge bg-light text-dark">{{ $user->appointments_count }}</span>
                        @else — @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Actif
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Inactif
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-light">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-light"
                                    title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                <i class="bi bi-toggle-{{ $user->is_active ? 'on text-success' : 'off text-muted' }}"></i>
                            </button>
                        </form>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              class="d-inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="p-3 border-top">{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
