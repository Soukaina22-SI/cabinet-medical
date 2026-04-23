<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedClinic') — MedClinic</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --accent: #3b82f6;
        }

        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* ── Sidebar ─────────────────────────────────────────── */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            transition: transform .3s ease;
        }

        #sidebar .brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        #sidebar .brand h5 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            letter-spacing: -.5px;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,.65);
            padding: .6rem 1.25rem;
            border-radius: .5rem;
            margin: .1rem .75rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .9rem;
            transition: all .2s;
        }

        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background: var(--sidebar-hover);
            color: #fff;
        }

        #sidebar .nav-link.active {
            background: var(--accent);
            color: #fff;
        }

        #sidebar .nav-section {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.35);
            padding: 1rem 1.5rem .25rem;
        }

        /* ── Main ────────────────────────────────────────────── */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-area { padding: 1.75rem; }

        /* ── Cards ───────────────────────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: transform .2s, box-shadow .2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,.07);
        }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: .75rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }

        /* ── Tables ──────────────────────────────────────────── */
        .table-card {
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .table thead th {
            background: #f8fafc;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            padding: .85rem 1rem;
        }

        .table td { padding: .85rem 1rem; vertical-align: middle; }

        /* ── Alerts ──────────────────────────────────────────── */
        .alert { border-radius: .75rem; border: none; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #main-content { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- ── Sidebar ──────────────────────────────────────────────── -->
<nav id="sidebar">
    <div class="brand d-flex align-items-center gap-2">
        <div style="background:var(--accent);border-radius:.5rem;padding:.4rem .6rem">
            <i class="bi bi-heart-pulse-fill text-white fs-5"></i>
        </div>
        <h5>Med<span style="color:var(--accent)">Clinic</span></h5>
    </div>

    <div class="mt-2">
        @include('partials.sidebar-nav')
    </div>
</nav>

<!-- ── Main ─────────────────────────────────────────────────── -->
<div id="main-content">

    <!-- Topbar -->
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary-subtle text-primary">
                {{ ucfirst(auth()->user()->role) }}
            </span>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2"
                    data-bs-toggle="dropdown">
                    <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle" width="28" height="28">
                    {{ auth()->user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Flash messages -->
    <div class="content-area pb-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Page Content -->
    <div class="content-area">
        @yield('content')
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<!-- FullCalendar -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
    // Mobile sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>

@stack('scripts')
</body>
</html>
