<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\{Appointment, Consultation, DoctorSchedule, Patient, PrescriptionItem, Prescription, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin MedClinic',
            'email'    => 'admin@medclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '+212 600 000 001',
        ]);

        $doctor1 = User::create([
            'name'      => 'Karim Benali',
            'email'     => 'doctor@medclinic.com',
            'password'  => Hash::make('password'),
            'role'      => 'medecin',
            'phone'     => '+212 600 000 002',
            'speciality'=> 'Médecine Générale',
        ]);

        $doctor2 = User::create([
            'name'      => 'Fatima Zahra Idrissi',
            'email'     => 'doctor2@medclinic.com',
            'password'  => Hash::make('password'),
            'role'      => 'medecin',
            'phone'     => '+212 600 000 003',
            'speciality'=> 'Cardiologie',
        ]);

        $doctor3 = User::create([
            'name'      => 'Youssef El Amrani',
            'email'     => 'doctor3@medclinic.com',
            'password'  => Hash::make('password'),
            'role'      => 'medecin',
            'phone'     => '+212 600 000 004',
            'speciality'=> 'Pédiatrie',
        ]);

        $secretary = User::create([
            'name'     => 'Samira Moussaoui',
            'email'    => 'secretary@medclinic.com',
            'password' => Hash::make('password'),
            'role'     => 'secretaire',
            'phone'    => '+212 600 000 005',
        ]);

        // ── Doctor Schedules (Lun–Ven 09:00–17:00) ─────────────
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

        // ── Patients ──────────────────────────────────────────
        $patientsData = [
            ['Mohamed',  'Alaoui',    'AB123456', '0612345678', '1985-03-15', 'male',   'A+'],
            ['Aicha',    'Benkirane', 'CD789012', '0623456789', '1990-07-22', 'female', 'B+'],
            ['Hassan',   'Tazi',      'EF345678', '0634567890', '1975-11-08', 'male',   'O+'],
            ['Nadia',    'Chraibi',   'GH901234', '0645678901', '1998-02-28', 'female', 'A-'],
            ['Omar',     'Berkane',   'IJ567890', '0656789012', '1963-09-14', 'male',   'AB+'],
            ['Zineb',    'Lahlou',    'KL123456', '0667890123', '2001-05-17', 'female', 'O-'],
            ['Khalid',   'Mansouri',  'MN789012', '0678901234', '1979-12-03', 'male',   'B-'],
            ['Houda',    'Berrada',   'OP345678', '0689012345', '1992-08-25', 'female', 'A+'],
            ['Rachid',   'Squalli',   'QR901234', '0690123456', '1955-04-11', 'male',   'AB-'],
            ['Meryem',   'Hajji',     'ST567890', '0601234567', '2003-01-30', 'female', 'O+'],
        ];

        $patients = [];
        foreach ($patientsData as $pd) {
            $patients[] = Patient::create([
                'first_name'    => $pd[0],
                'last_name'     => $pd[1],
                'cin'           => $pd[2],
                'phone'         => $pd[3],
                'date_of_birth' => $pd[4],
                'gender'        => $pd[5],
                'blood_type'    => $pd[6],
            ]);
        }

        // ── Appointments & Consultations ──────────────────────
        $doctors = [$doctor1, $doctor2, $doctor3];
        $statuses = ['pending','confirmed','completed','cancelled'];

        for ($i = 0; $i < 30; $i++) {
            $patient  = $patients[array_rand($patients)];
            $doctor   = $doctors[array_rand($doctors)];
            $daysAgo  = rand(-7, 30); // mix of past and future
            $apptDate = now()->addDays($daysAgo)->setHour(rand(9,16))->setMinute(0)->setSecond(0);

            $status = $daysAgo < 0 ? 'completed' : $statuses[array_rand($statuses)];

            $appointment = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $doctor->id,
                'appointment_date' => $apptDate,
                'status'           => $status,
                'reason'           => collect(['Douleur abdominale','Fièvre','Contrôle annuel','Mal de tête','Toux persistante'])->random(),
            ]);

            // Create consultation for completed appointments
            if ($status === 'completed') {
                $consultation = Consultation::create([
                    'appointment_id' => $appointment->id,
                    'patient_id'     => $patient->id,
                    'doctor_id'      => $doctor->id,
                    'symptoms'       => collect(['Douleur thoracique légère','Fatigue générale','Maux de tête récurrents','Fièvre à 38.5°C'])->random(),
                    'diagnosis'      => collect(['Grippe saisonnière','Hypertension légère','Infection virale','Stress et surmenage'])->random(),
                    'notes'          => 'Repos recommandé. Revoir dans 2 semaines.',
                    'temperature'    => rand(365, 395) / 10,
                    'blood_pressure' => (rand(110,140) . '/' . rand(70,90)),
                ]);

                // Add prescription
                $prescription = Prescription::create([
                    'consultation_id' => $consultation->id,
                    'patient_id'      => $patient->id,
                    'doctor_id'       => $doctor->id,
                ]);

                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_name' => collect(['Paracétamol','Ibuprofène','Amoxicilline','Doliprane'])->random(),
                    'dosage'          => collect(['500mg','1g','250mg'])->random(),
                    'frequency'       => collect(['3 fois/jour','2 fois/jour','1 fois/jour'])->random(),
                    'duration'        => collect(['5 jours','7 jours','10 jours'])->random(),
                ]);
            }
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->line('');
        $this->command->line('  <fg=yellow>Comptes de démonstration:</>');
        $this->command->line('  admin@medclinic.com    / password  (Admin)');
        $this->command->line('  doctor@medclinic.com   / password  (Médecin)');
        $this->command->line('  secretary@medclinic.com/ password  (Secrétaire)');
    }
}
