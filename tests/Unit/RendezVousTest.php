<?php
// ============================================================
// tests/Unit/RendezVousTest.php
// ============================================================
namespace Tests\Unit;
 
use Tests\TestCase;
use App\Models\User;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\RendezVous;
use Illuminate\Foundation\Testing\RefreshDatabase;
 
class RendezVousTest extends TestCase
{
    use RefreshDatabase;
 
    private Medecin $medecin;
    private Patient $patient;
 
    protected function setUp(): void
    {
        parent::setUp();
 
        $userMed = User::factory()->create(['role' => 'medecin']);
        $this->medecin = $userMed->medecin()->create([
            'specialite' => 'Cardiologie',
            'num_ordre'  => 'ORD-TEST-01',
            'duree_rdv'  => 30,
            'heure_debut'=> '08:00',
            'heure_fin'  => '17:00',
        ]);
 
        $userPat = User::factory()->create(['role' => 'patient']);
        $this->patient = $userPat->patient()->create([
            'date_naissance' => '1990-01-01',
            'sexe' => 'M',
        ]);
    }
 
    /** @test */
    public function test_rdv_creation_successful()
    {
        $rdv = RendezVous::create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'date_heure' => now()->addDay()->setHour(9)->setMinute(0),
            'motif'      => 'Test consultation',
            'statut'     => 'en_attente',
        ]);
 
        $this->assertDatabaseHas('rendezvous', [
            'id'     => $rdv->id,
            'statut' => 'en_attente',
        ]);
    }
 
    /** @test */
    public function test_rdv_confirmer_changes_statut()
    {
        $rdv = RendezVous::create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'date_heure' => now()->addDays(2),
            'motif'      => 'Test',
            'statut'     => 'en_attente',
        ]);
 
        $rdv->confirmer();
        $this->assertEquals('confirme', $rdv->fresh()->statut);
    }
 
    /** @test */
    public function test_rdv_annuler_changes_statut()
    {
        $rdv = RendezVous::create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'date_heure' => now()->addDays(3),
            'motif'      => 'Test',
            'statut'     => 'confirme',
        ]);
 
        $rdv->annuler();
        $this->assertEquals('annule', $rdv->fresh()->statut);
    }
 
    /** @test */
    public function test_disponibilites_retourne_creneaux()
    {
        $slots = $this->medecin->getDisponibilites(now()->addDays(5)->format('Y-m-d'));
 
        $this->assertIsArray($slots);
        $this->assertNotEmpty($slots);
        $this->assertArrayHasKey('heure', $slots[0]);
        $this->assertArrayHasKey('disponible', $slots[0]);
    }
 
    /** @test */
    public function test_creneau_marque_indisponible_si_rdv_existe()
    {
        $dateFuture = now()->addDays(5)->setHour(9)->setMinute(0);
 
        RendezVous::create([
            'patient_id' => $this->patient->id,
            'medecin_id' => $this->medecin->id,
            'date_heure' => $dateFuture,
            'motif'      => 'Déjà pris',
            'statut'     => 'confirme',
        ]);
 
        $slots = $this->medecin->getDisponibilites($dateFuture->format('Y-m-d'));
        $creneau9h = collect($slots)->firstWhere('heure', '09:00');
 
        $this->assertFalse($creneau9h['disponible']);
    }
}