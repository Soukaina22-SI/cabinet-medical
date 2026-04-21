{{-- ============================================================ --}}
{{-- resources/views/layouts/app.blade.php --}}
{{-- ============================================================ --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cabinet Médical - @yield('title', 'Accueil')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --bs-primary: #1a6fa0; }
        .sidebar { min-height: 100vh; background: #1a6fa0; }
        .sidebar .nav-link { color: rgba(255,255,255,.8); }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.15); border-radius: 8px; }
        .sidebar .nav-link i { width: 22px; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.08); border-radius: 12px; }
        .stat-card { border-left: 4px solid; }
        .badge-en_attente { background: #ffc107; }
        .badge-confirme { background: #198754; }
        .badge-annule { background: #dc3545; }
        .badge-termine { background: #6c757d; }
    </style>
    @stack('styles')
</head>
<body class="bg-light">
 
@auth
<div class="container-fluid">
    <div class="row">
        {{-- Sidebar --}}
        <div class="col-md-2 px-0 sidebar text-white">
            <div class="p-3 border-bottom border-secondary">
                <i class="bi bi-hospital-fill fs-4 me-2"></i>
                <strong>Cabinet Médical</strong>
            </div>
            <nav class="nav flex-column p-2 gap-1">
                @if(auth()->user()->isAdmin())
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
                    <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}"><i class="bi bi-people-fill"></i> Utilisateurs</a>
                    <a class="nav-link" href="{{ route('patients.index') }}"><i class="bi bi-person-heart"></i> Patients</a>
                    <a class="nav-link" href="{{ route('rendezvous.index') }}"><i class="bi bi-calendar3"></i> Rendez-vous</a>
                @elseif(auth()->user()->isMedecin())
                    <a class="nav-link {{ request()->routeIs('medecin.dashboard') ? 'active' : '' }}" href="{{ route('medecin.dashboard') }}"><i class="bi bi-speedometer2"></i> Mon Planning</a>
                    <a class="nav-link" href="{{ route('rendezvous.index') }}"><i class="bi bi-calendar3"></i> Mes RDV</a>
                    <a class="nav-link" href="{{ route('patients.index') }}"><i class="bi bi-person-heart"></i> Patients</a>
                @elseif(auth()->user()->isSecretaire())
                    <a class="nav-link" href="{{ route('secretaire.dashboard') }}"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
                    <a class="nav-link" href="{{ route('rendezvous.index') }}"><i class="bi bi-calendar3"></i> Rendez-vous</a>
                    <a class="nav-link" href="{{ route('patients.index') }}"><i class="bi bi-person-heart"></i> Patients</a>
                @elseif(auth()->user()->isPatient())
                    <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}"><i class="bi bi-house-heart"></i> Mon Espace</a>
                    <a class="nav-link" href="{{ route('rendezvous.create') }}"><i class="bi bi-calendar-plus"></i> Prendre RDV</a>
                    <a class="nav-link" href="{{ route('rendezvous.index') }}"><i class="bi bi-calendar3"></i> Mes RDV</a>
                @endif
            </nav>
            <div class="p-3 mt-auto border-top border-secondary">
                <small class="d-block mb-1 opacity-75">{{ auth()->user()->nom_complet }}</small>
                <span class="badge bg-light text-primary">{{ ucfirst(auth()->user()->role) }}</span>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light w-100">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
 
        {{-- Main content --}}
        <div class="col-md-10 py-4 px-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
 
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
 
            @yield('content')
        </div>
    </div>
</div>
@else
    @yield('content')
@endauth
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')
</body>
</html>
 
 