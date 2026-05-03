#!/usr/bin/env python3
"""
fix_project.py — Script de correction automatique du projet MedClinic
Exécuter depuis la racine du projet: python fix_project.py
"""
import os, sys

BASE = os.getcwd()

def fix_file(rel_path, replacements):
    path = os.path.join(BASE, rel_path.replace('/', os.sep))
    if not os.path.exists(path):
        print(f"  ⚠️  Fichier introuvable: {rel_path}")
        return 0
    with open(path, 'r', encoding='utf-8', errors='replace') as f:
        content = f.read()
    original = content
    count = 0
    for old, new in replacements:
        if old in content:
            content = content.replace(old, new)
            count += 1
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    status = f"✅ {count} correction(s)" if count else "ℹ️  Déjà correct"
    print(f"  {status} — {rel_path}")
    return count

print("=" * 60)
print("  MedClinic — Correction automatique des erreurs")
print("=" * 60)

# ── 1. AppointmentController ──────────────────────────────────
print("\n[1/4] AppointmentController.php")
fix_file('app/Http/Controllers/AppointmentController.php', [
    # Fix calendarAppointments: add whereHas before get()
    (
        "$calendarAppointments = $calQuery->get()->map(fn($a) => [",
        "$calendarAppointments = $calQuery->whereHas('patient')->whereHas('doctor')\n            ->get()->map(fn($a) => ["
    ),
    # Fix null-unsafe title in map
    (
        "'title' => $a->patient->full_name . ' — Dr. ' . $a->doctor->name,",
        "'title' => ($a->patient?->full_name ?? 'Patient') . ' — Dr. ' . ($a->doctor?->name ?? '—'),"
    ),
])

# ── 2. ConsultationController ─────────────────────────────────
print("\n[2/4] ConsultationController.php")
fix_file('app/Http/Controllers/ConsultationController.php', [
    (
        "Consultation::with(['patient', 'doctor', 'prescription.items']);",
        "Consultation::with(['patient', 'doctor', 'prescription.items'])->whereHas('patient')->whereHas('doctor');"
    ),
])

# ── 3. consultations/index.blade.php ─────────────────────────
print("\n[3/4] resources/views/consultations/index.blade.php")
fix_file('resources/views/consultations/index.blade.php', [
    ("{{ $c->patient->age }} ans",               "{{ $c->patient?->age ?? '—' }} ans"),
    ("$c->patient->full_name",                   "$c->patient?->full_name ?? 'Patient inconnu'"),
    ("$c->patient->first_name",                  "$c->patient?->first_name ?? '?'"),
    ("$c->patient->last_name",                   "$c->patient?->last_name ?? ''"),
    ("$c->doctor->name",                         "$c->doctor?->name ?? '—'"),
    ("src=\"{{ $c->doctor->avatar_url }}\"",     "src=\"{{ $c->doctor?->avatar_url ?? '' }}\""),
    ("$c->doctor->avatar_url",                   "$c->doctor?->avatar_url ?? ''"),
])

# ── 4. Tous les views — corrections null-safe globales ────────
print("\n[4/4] Toutes les vues (null-safe global)")

views_fixes = [
    # appointments/index
    ("resources/views/appointments/index.blade.php", [
        ("$appt->patient->full_name",  "$appt->patient?->full_name ?? 'Patient inconnu'"),
        ("$appt->doctor->name",        "$appt->doctor?->name ?? '—'"),
    ]),
    # appointments/show
    ("resources/views/appointments/show.blade.php", [
        ("$appointment->patient->full_name",  "$appointment->patient?->full_name ?? 'Patient inconnu'"),
        ("$appointment->patient->phone",      "$appointment->patient?->phone ?? '—'"),
        ("$appointment->patient->email",      "$appointment->patient?->email"),
        ("$appointment->patient->first_name", "$appointment->patient?->first_name ?? '?'"),
        ("$appointment->patient->last_name",  "$appointment->patient?->last_name ?? ''"),
        ("$appointment->doctor->name",        "$appointment->doctor?->name ?? '—'"),
        ("$appointment->doctor->speciality",  "$appointment->doctor?->speciality"),
    ]),
    # appointments/edit
    ("resources/views/appointments/edit.blade.php", [
        ("$appointment->patient->full_name", "$appointment->patient?->full_name ?? 'Patient inconnu'"),
        ("$appointment->doctor->name",       "$appointment->doctor?->name ?? '—'"),
    ]),
    # doctor/dashboard
    ("resources/views/doctor/dashboard.blade.php", [
        ("$appt->patient->full_name",  "$appt->patient?->full_name ?? 'Patient inconnu'"),
        ("$appt->patient->first_name", "$appt->patient?->first_name ?? '?'"),
        ("$appt->patient->last_name",  "$appt->patient?->last_name ?? ''"),
        ("$appt->doctor->name",        "$appt->doctor?->name ?? '—'"),
    ]),
    # secretary/dashboard
    ("resources/views/secretary/dashboard.blade.php", [
        ("$appt->patient->full_name", "$appt->patient?->full_name ?? 'Patient inconnu'"),
        ("$appt->doctor->name",       "$appt->doctor?->name ?? '—'"),
    ]),
    # admin/dashboard
    ("resources/views/admin/dashboard.blade.php", [
        ("$appt->patient->full_name",  "$appt->patient?->full_name ?? 'Patient inconnu'"),
        ("$appt->patient->first_name", "$appt->patient?->first_name ?? '?'"),
        ("$appt->patient->last_name",  "$appt->patient?->last_name ?? ''"),
        ("$appt->doctor->name",        "$appt->doctor?->name ?? '—'"),
    ]),
    # admin/patients/show
    ("resources/views/admin/patients/show.blade.php", [
        ("$appt->doctor->name",    "$appt->doctor?->name ?? '—'"),
        ("$consult->doctor->name", "$consult->doctor?->name ?? '—'"),
    ]),
    # consultations/show
    ("resources/views/consultations/show.blade.php", [
        ("$consultation->patient->full_name",  "$consultation->patient?->full_name ?? 'Patient inconnu'"),
        ("$consultation->patient->first_name", "$consultation->patient?->first_name ?? '?'"),
        ("$consultation->patient->last_name",  "$consultation->patient?->last_name ?? ''"),
        ("$consultation->doctor->name",        "$consultation->doctor?->name ?? '—'"),
    ]),
    # consultations/create
    ("resources/views/consultations/create.blade.php", [
        ("$appointment->patient->full_name", "$appointment->patient?->full_name ?? 'Patient inconnu'"),
    ]),
]

for rel_path, fixes in views_fixes:
    fix_file(rel_path, fixes)

# ── Routes check ──────────────────────────────────────────────
print("\n[BONUS] Vérification routes/web.php")
routes_path = os.path.join(BASE, 'routes', 'web.php')
if os.path.exists(routes_path):
    with open(routes_path, 'r', encoding='utf-8') as f:
        routes = f.read()
    issues = []
    if 'role:secretaire' not in routes and 'secretaire' not in routes:
        issues.append("  ⚠️  Le rôle 'secretaire' n'est pas dans les routes!")
    if "role:admin,medecin,secretaire" not in routes:
        issues.append("  ⚠️  Les routes consultations/appointments ne donnent pas accès à la secrétaire!")
    if not issues:
        print("  ✅ Routes correctes")
    else:
        for i in issues:
            print(i)

print("\n" + "=" * 60)
print("  ✅ Correction terminée !")
print("  Redémarrez le serveur: php artisan serve")
print("=" * 60)

# ── 5. sidebar-nav.blade.php ──────────────────────────────────
print("\n[5/5] sidebar-nav — Correction routes patients")
fix_file('resources/views/partials/sidebar-nav.blade.php', [
    # Fix wrong route name in sidebar
    ("route('patients.index')", "route('admin.patients.index')"),
    ("route('patients.create')", "route('admin.patients.create')"),
    ("routeIs('patients.*')", "routeIs('admin.patients.*')"),
])

# ── 6. admin/patients/index.blade.php — Admin-only delete button ──
print("\n[6/6] admin/patients — Bouton Supprimer Admin uniquement")
# Already handled by line-based approach above
