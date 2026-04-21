<?php
 
// ============================================================
// database/seeders/DatabaseSeeder.php
// ============================================================
namespace Database\Seeders;
 
use App\Models\User;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Ordonnance;
use App\Models\DossierMedical;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
 
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──
        User::create([
            'nom'       => 'Admin',
            'prenom'    => 'Super',
            'email'     => 'admin@cabinet.ma',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'telephone' => '0600000001',
        ]);
 
        // ── Secrétaire ──
        User::create([
            'nom'       => 'Alami',
            'prenom'    => 'Fatima',
            'email'     => 'secretaire@cabinet.ma',
            'password'  => Hash::make('password'),
            'role'      => 'secretaire',
            'telephone' => '0600000002',
        ]);
 
        // ── Médecins ──
        $medecinsData = [
            ['Hassan', 'Benali',    'Cardiologie',     'ORD-001'],
            ['Leila',  'Moussaoui', 'Pédiatrie',       'ORD-002'],
            ['Omar',   'Tazi',      'Médecine Générale','ORD-003'],
        ];
 
        $medecins = [];
        foreach ($medecinsData as [$prenom, $nom, $specialite, $ordre]) {
            $user = User::create([
                'nom'       => $nom,
                'prenom'    => $prenom,
                'email'     => strtolower($prenom) . '.' . strtolower($nom) . '@cabinet.ma',
                'password'  => Hash::make('password'),
                'role'      => 'medecin',
                'telephone' => '0600' . rand(100000, 999999),
            ]);
            $medecins[] = $user->medecin()->create([
                'specialite' => $specialite,
                'num_ordre'  => $ordre,
                'duree_rdv'  => 30,
            ]);
        }
 
        // ── Patients (5) ──
        $patientsData = [
            ['Mohammed', 'El Idrissi', '1985-03-15', 'M'],
            ['Aicha',    'Bennani',    '1992-07-22', 'F'],
            ['Youssef',  'Chraibi',    '1978-11-08', 'M'],
            ['Nadia',    'Filali',     '2001-05-30', 'F'],
            ['Karim',    'Boussouf',   '1965-01-12', 'M'],
        ];
 
        $patients = [];
        foreach ($patientsData as $i => [$prenom, $nom, $naissance, $sexe]) {
            $user = User::create([
                'nom'       => $nom,
                'prenom'    => $prenom,
                'email'     => strtolower($prenom) . '@patient.ma',
                'password'  => Hash::make('password'),
                'role'      => 'patient',
                'telephone' => '0650' . rand(100000, 999999),
            ]);
            $patient = $user->patient()->create([
                'date_naissance' => $naissance,
                'sexe'           => $sexe,
                'cin'            => 'AB' . rand(100000, 999999),
            ]);
            $patient->dossierMedical()->create([
                'antecedents'  => $i === 0 ? 'Hypertension artérielle' : null,
                'allergies'    => $i === 1 ? 'Pénicilline' : null,
                'groupe_sanguin' => ['A+','B+','O+','AB+','O-'][$i],
            ]);
            $patients[] = $patient;
        }
 
        // ── RDVs et Consultations de démonstration ──
        $statuts = ['en_attente', 'confirme', 'termine'];
        foreach ($patients as $idx => $patient) {
            // RDV passé (terminé)
            $rdv = RendezVous::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecins[$idx % count($medecins)]->id,
                'date_heure' => now()->subDays(rand(5, 30))->setHour(9)->setMinute(0),
                'motif'      => 'Consultation de routine',
                'statut'     => 'termine',
            ]);
 
            $consultation = Consultation::create([
                'rendezvous_id' => $rdv->id,
                'medecin_id'    => $rdv->medecin_id,
                'patient_id'    => $rdv->patient_id,
                'date_heure'    => $rdv->date_heure,
                'diagnostic'    => 'Bonne santé générale',
                'compte_rendu'  => 'Patient en bonne santé. Aucune anomalie détectée.',
                'prix'          => 200,
            ]);
 
            Ordonnance::create([
                'consultation_id' => $consultation->id,
                'medicaments' => [
                    ['nom' => 'Doliprane', 'dosage' => '1000mg', 'posologie' => '3x/jour', 'duree' => '5 jours'],
                ],
                'instructions' => 'Prendre après les repas. Boire suffisamment d\'eau.',
            ]);
 
            // RDV futur (en attente ou confirmé)
            RendezVous::create([
                'patient_id' => $patient->id,
                'medecin_id' => $medecins[($idx + 1) % count($medecins)]->id,
                'date_heure' => now()->addDays(rand(1, 14))->setHour(10 + $idx)->setMinute(30),
                'motif'      => 'Suivi médical',
                'statut'     => $statuts[$idx % 2],
            ]);
        }
 
        // Un patient avec email de test facile
        $user = User::create([
            'nom'       => 'Test',
            'prenom'    => 'Patient',
            'email'     => 'patient@cabinet.ma',
            'password'  => Hash::make('password'),
            'role'      => 'patient',
        ]);
        $patient = $user->patient()->create([
            'date_naissance' => '1990-01-01',
            'sexe'           => 'M',
        ]);
        $patient->dossierMedical()->create([]);
    }
}
 