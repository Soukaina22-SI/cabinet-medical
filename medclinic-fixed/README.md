# 🏥 MedClinic — Système de Gestion de Clinique Médicale

Application web **complète** de gestion de clinique médicale — Laravel 11, Bootstrap 5, MySQL, Chart.js, DomPDF.

---

## 🧱 Stack Technique

| Couche | Technologie | Version |
|--------|-------------|---------|
| Backend | Laravel (PHP) | 11 / 8.2+ |
| Frontend | Bootstrap + Bootstrap Icons | 5.3 |
| Base de données | MySQL | 8.0+ |
| Graphiques | Chart.js + FullCalendar | 4 / 6 |
| PDF | barryvdh/laravel-dompdf | ^2.2 |
| Email | Laravel Mail (Mailtrap) | — |
| Tests | PHPUnit | 11 |
| CI/CD | GitHub Actions | — |
| Déploiement | Railway / Docker / VPS | — |

---

## 🔑 Comptes de Démonstration

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| admin@medclinic.com | password | Admin |
| doctor@medclinic.com | password | Médecin (Généraliste) |
| doctor2@medclinic.com | password | Médecin (Cardiologue) |
| secretary@medclinic.com | password | Secrétaire |

---

## 🚀 Installation Rapide

```bash
git clone https://github.com/votre-compte/medclinic.git && cd medclinic
composer install
cp .env.example .env && php artisan key:generate
# Editer .env : DB_DATABASE, DB_USERNAME, DB_PASSWORD, MAIL_*
php artisan migrate --seed && php artisan storage:link
npm install && npm run build
php artisan serve
```
Accès : http://localhost:8000

---

## 🐳 Docker

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```
| Service | URL |
|---------|-----|
| Application | http://localhost:8000 |
| phpMyAdmin | http://localhost:8080 |
| Mailhog | http://localhost:8025 |

---

## 🧪 Tests (21 tests, 50+ assertions)

```bash
php artisan test                 # Tous les tests
php artisan test --coverage      # Avec couverture
php artisan test --filter AuthTest
```

| Suite | Tests | Couvre |
|-------|-------|--------|
| AuthTest | 7 | Login, logout, rôles, redirections |
| AppointmentTest | 5 | CRUD, statuts, créneaux, autorisations |
| ConsultationTest | 7 | Création, prescription, PDF |
| StatisticsTest | 5 | KPIs, filtres, taux annulation |
| PatientModelTest | 5 | full_name, âge, recherche |
| AppointmentModelTest | 4 | Helpers statut, scopes |
| UserModelTest | 5 | Rôles, scopes, avatar |

---

## 🌐 Déploiement

### Railway (le plus simple)
```bash
# 1. Connecter GitHub sur railway.app
# 2. Ajouter un service MySQL
# 3. Configurer APP_KEY, DB_*, MAIL_*
# 4. Déploiement automatique sur git push
```

### VPS Ubuntu
```bash
sudo apt install nginx php8.2-fpm mysql-server nodejs npm
cd /var/www && git clone <repo> medclinic && cd medclinic
composer install --no-dev && npm run build
php artisan migrate --seed && php artisan optimize
```

---

## ⚙️ Commandes Artisan

```bash
php artisan serve                         # Serveur dev
php artisan migrate:fresh --seed         # Reset + données démo
php artisan appointments:send-reminders  # Rappels email J-1
php artisan optimize                     # Cache prod
php artisan test                         # Tests
```

---

## 📁 Structure Clé

```
app/
├── Http/Controllers/
│   ├── Admin/{Dashboard,Patient,Statistics,User}Controller.php
│   ├── Doctor/ScheduleController.php
│   ├── Auth/LoginController.php
│   ├── AppointmentController.php
│   ├── ConsultationController.php
│   └── ProfileController.php
├── Models/ (User, Patient, Appointment, Consultation, Prescription...)
└── Mail/   (AppointmentConfirmed, AppointmentReminder)

resources/views/
├── admin/      layouts/    auth/     appointments/
├── consultations/  doctor/   secretary/  patient/
├── prescriptions/pdf.blade.php   emails/   errors/
└── profile/

tests/
├── Feature/ (Auth, Appointment, Consultation, Statistics)
└── Unit/    (User, Patient, Appointment models)
```
