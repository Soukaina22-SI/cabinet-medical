{{-- resources/views/partials/sidebar-nav.blade.php --}}
@php $role = auth()->user()->role; @endphp

@if($role === 'admin')
    <span class="nav-section">Principal</span>
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>
    <a href="{{ route('admin.statistics') }}" class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
        <i class="bi bi-graph-up"></i> Statistiques
    </a>
    <span class="nav-section">Gestion</span>
    <a href="{{ route('admin.patients.index') }}" class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Patients
    </a>
    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="bi bi-person-badge"></i> Utilisateurs
    </a>
    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Rendez-vous
    </a>
    <a href="{{ route('consultations.index') }}" class="nav-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard2-pulse"></i> Consultations
    </a>

@elseif($role === 'medecin')
    <span class="nav-section">Principal</span>
    <a href="{{ route('doctor.dashboard') }}" class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>
    <span class="nav-section">Mon Agenda</span>
    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Mes Rendez-vous
    </a>
    <a href="{{ route('consultations.index') }}" class="nav-link {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard2-pulse"></i> Mes Consultations
    </a>
    <span class="nav-section">Patients &amp; Planning</span>
    <a href="{{ route('admin.patients.index') }}" class="nav-link">
        <i class="bi bi-people"></i> Dossiers Patients
    </a>
    <a href="{{ route('doctor.schedule') }}" class="nav-link {{ request()->routeIs('doctor.schedule') ? 'active' : '' }}">
        <i class="bi bi-clock"></i> Mes Disponibilités
    </a>

@elseif($role === 'secretaire')
    <span class="nav-section">Principal</span>
    <a href="{{ route('secretary.dashboard') }}" class="nav-link {{ request()->routeIs('secretary.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>
    <span class="nav-section">Gestion</span>
    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Rendez-vous
    </a>
    <a href="{{ route('admin.patients.index') }}" class="nav-link">
        <i class="bi bi-people"></i> Patients
    </a>
    <a href="{{ route('appointments.create') }}" class="nav-link">
        <i class="bi bi-calendar-plus"></i> Nouveau RDV
    </a>

@elseif($role === 'patient')
    <span class="nav-section">Mon Espace</span>
    <a href="{{ route('patient.dashboard') }}" class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Tableau de bord
    </a>
    <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i> Mes Rendez-vous
    </a>
    <a href="{{ route('appointments.create') }}" class="nav-link">
        <i class="bi bi-calendar-plus"></i> Prendre un RDV
    </a>
@endif

<span class="nav-section">Compte</span>
<a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
    <i class="bi bi-person-circle"></i> Mon Profil
</a>
<form method="POST" action="{{ route('logout') }}" class="m-0">
    @csrf
    <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" style="color:rgba(255,255,255,.62)">
        <i class="bi bi-box-arrow-right"></i> Déconnexion
    </button>
</form>
