# 🔧 Instructions de correction — MedClinic

## Problèmes corrigés dans ce ZIP :
1. `full_name on null` dans /appointments (AppointmentController + views)
2. `age on null` dans /consultations (ConsultationController + view)
3. Accès non autorisé pour la Secrétaire → /admin/patients (normal, seul Admin peut y accéder)

## Comment appliquer les corrections :

### Option A — Script automatique (recommandé)
```cmd
cd C:\Users\hp\cabinet-medical\cabinet-medical
python fix_project.py
php artisan serve
```

### Option B — Remplacer les fichiers manuellement
Copiez ces fichiers du ZIP vers votre projet :
- `app/Http/Controllers/AppointmentController.php`
- `app/Http/Controllers/ConsultationController.php`
- `resources/views/consultations/index.blade.php`
- `resources/views/appointments/show.blade.php`
- `resources/views/doctor/dashboard.blade.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/consultations/show.blade.php`
- `routes/web.php`

### Après correction, vider le cache :
```cmd
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan serve
```

## Comptes de test :
| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@medclinic.com | password | Admin |
| doctor@medclinic.com | password | Médecin |
| secretary@medclinic.com | password | Secrétaire |
| patient@medclinic.com | password | Patient |

## Note sur "Accès non autorisé" pour /admin/patients :
La Secrétaire NE PEUT PAS accéder à /admin/patients — c'est voulu.
La secrétaire accède aux patients via /appointments (créer RDV) et /consultations.
