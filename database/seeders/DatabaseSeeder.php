<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\{Appointment, Consultation, DoctorSchedule, Patient, Prescription, PrescriptionItem, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Staff ─────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin MedClinic',
            'email'    => 'admin@medclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '+212 600 000 001',
        ]);

        $doctor1 = User::create([
            'name'       => 'Karim Benali',
            'email'      => 'doctor@medclinic.com',
            'password'   => Hash::make('password'),
            'role'       => 'medecin',
            'phone'      => '+212 600 000 002',
            'speciality' => 'Médecine Générale',
        ]);

        $doctor2 = User::create([
            'name'       => 'Fatima Zahra Idrissi',
            'email'      => 'doctor2@medclinic.com',
            'password'   => Hash::make('password'),
            'role'       => 'medecin',
            'phone'      => '+212 600 000 003',
            'speciality' => 'Cardiologie',
        ]);

        $doctor3 = User::create([
            'name'       => 'Youssef El Amrani',
            'email'      => 'doctor3@medclinic.com',
            'password'   => Hash::make('password'),
            'role'       => 'medecin',
            'phone'      => '+212 600 000 004',
            'speciality' => 'Pédiatrie',
        ]);

        User::create([
            'name'     => 'Samira Moussaoui',
            'email'    => 'secretary@medclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'secretaire',
            'phone'    => '+212 600 000 005',
        ]);

        // ── Patient user (avec dossier lié) ───────────────────
        $patientUser = User::create([
            'name'     => 'Mohamed Alaoui',
            'email'    => 'patient@medclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'patient',
            'phone'    => '+212 612 345 678',
        ]);

        // ── Doctor Schedules (Lun–Ven 09:00–17:00) ────────────
        foreach ([$doctor1, $doctor2, $doctor3] as $doc) {
            foreach ([1, 2, 3, 4, 5] as $day) {
                DoctorSchedule::create([
                    'doctor_id'    => $doc->id,
                    'day_of_week'  => $day,
                    'start_time'   => '09:00:00',
                    'end_time'     => '17:00:00',
                    'is_available' => true,
                ]);
            }
        }

        // ── Patients (1er = lié au compte patient) ─────────────
        $pData = [
            ['Mohamed','Alaoui',   'AB123456','0612345678','1985-03-15','male',  'A+', $patientUser->id, 'patient@medclinic.com'],
            ['Aicha',  'Benkirane','CD789012','0623456789','1990-07-22','female','B+', null, null],
            ['Hassan', 'Tazi',     'EF345678','0634567890','1975-11-08','male',  'O+', null, null],
            ['Nadia',  'Chraibi',  'GH901234','0645678901','1998-02-28','female','A-', null, null],
            ['Omar',   'Berkane',  'IJ567890','0656789012','1963-09-14','male',  'AB+',null, null],
            ['Zineb',  'Lahlou',   'KL123456','0667890123','2001-05-17','female','O-', null, null],
            ['Khalid', 'Mansouri', 'MN789012','0678901234','1979-12-03','male',  'B-', null, null],
            ['Houda',  'Berrada',  'OP345678','0689012345','1992-08-25','female','A+', null, null],
            ['Rachid', 'Squalli',  'QR901234','0690123456','1955-04-11','male',  'AB-',null, null],
            ['Meryem', 'Hajji',    'ST567890','0601234567','2003-01-30','female','O+', null, null],
        ];

        $patients = [];
        foreach ($pData as $p) {
            $patients[] = Patient::create([
                'user_id'       => $p[7],
                'first_name'    => $p[0],
                'last_name'     => $p[1],
                'cin'           => $p[2],
                'phone'         => $p[3],
                'email'         => $p[8],
                'date_of_birth' => $p[4],
                'gender'        => $p[5],
                'blood_type'    => $p[6],
            ]);
        }

        // ── Appointments & Consultations ──────────────────────
        $doctors  = [$doctor1, $doctor2, $doctor3];
        $statuses = ['pending','confirmed','completed','cancelled'];
        $reasons  = ['Douleur abdominale','Fièvre','Contrôle annuel','Mal de tête','Toux persistante','Consultation de suivi'];

        for ($i = 0; $i < 30; $i++) {
            $patient = $patients[array_rand($patients)];
            $doctor  = $doctors[array_rand($doctors)];
            $days    = rand(-15, 30);
            $date    = now()->addDays($days)->setHour(rand(9,16))->setMinute(0)->setSecond(0);
            $status  = $days < 0 ? 'completed' : $statuses[array_rand($statuses)];

            $appt = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctor->id,
                'appointment_date' => $date,
                'status'           => $status,
                'reason'           => collect($reasons)->random(),
                'reminder_sent'    => false,
            ]);

            if ($status === 'completed') {
                $consult = Consultation::create([
                    'appointment_id' => $appt->id,
                    'patient_id'     => $patient->id,
                    'doctor_id'      => $doctor->id,
                    'symptoms'       => collect(['Douleur thoracique','Fatigue générale','Maux de tête','Fièvre 38.5°C','Toux sèche'])->random(),
                    'diagnosis'      => collect(['Grippe saisonnière','Hypertension légère','Infection virale','Stress et surmenage','Rhume'])->random(),
                    'notes'          => 'Repos recommandé. Revoir dans 2 semaines si persistance.',
                    'temperature'    => rand(365, 395) / 10,
                    'blood_pressure' => rand(110,140) . '/' . rand(70,90),
                ]);

                $prescription = Prescription::create([
                    'consultation_id' => $consult->id,
                    'patient_id'      => $patient->id,
                    'doctor_id'       => $doctor->id,
                ]);

                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_name' => collect(['Paracétamol','Ibuprofène','Amoxicilline','Doliprane 1g'])->random(),
                    'dosage'          => collect(['500mg','1g','250mg'])->random(),
                    'frequency'       => collect(['3×/jour','2×/jour','1×/jour'])->random(),
                    'duration'        => collect(['5 jours','7 jours','10 jours'])->random(),
                ]);
            }
        }

        $this->command->info('✅ Données de démo créées !');
        $this->command->line('');
        $this->command->line('  admin@medclinic.com      / password  → Admin');
        $this->command->line('  doctor@medclinic.com     / password  → Médecin');
        $this->command->line('  doctor2@medclinic.com    / password  → Cardiologue');
        $this->command->line('  secretary@medclinic.com  / password  → Secrétaire');
        $this->command->line('  patient@medclinic.com    / password  → Patient (dossier lié)');
    }
}
