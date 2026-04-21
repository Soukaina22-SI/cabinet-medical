<?php
// ============================================================
// tests/Unit/PatientTest.php
// ============================================================
namespace Tests\Unit;
 
use Tests\TestCase;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
 
class PatientTest extends TestCase
{
    use RefreshDatabase;
 
    /** @test */
    public function test_patient_age_calcul_correct()
    {
        $user = User::factory()->create(['role' => 'patient']);
        $patient = $user->patient()->create([
            'date_naissance' => now()->subYears(30)->format('Y-m-d'),
            'sexe' => 'F',
        ]);
 
        $this->assertEquals(30, $patient->age);
    }
 
    /** @test */
    public function test_dossier_medical_cree_avec_patient()
    {
        $user    = User::factory()->create(['role' => 'patient']);
        $patient = $user->patient()->create(['date_naissance' => '1990-01-01', 'sexe' => 'M']);
        $patient->dossierMedical()->create([]);
 
        $this->assertNotNull($patient->dossierMedical);
    }
 
    /** @test */
    public function test_user_role_helpers()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $medecin = User::factory()->create(['role' => 'medecin']);
        $patient = User::factory()->create(['role' => 'patient']);
 
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isMedecin());
        $this->assertTrue($medecin->isMedecin());
        $this->assertTrue($patient->isPatient());
    }
 
    /** @test */
    public function test_nom_complet_attribute()
    {
        $user = User::factory()->create([
            'nom'    => 'Benali',
            'prenom' => 'Hassan',
            'role'   => 'patient',
        ]);
 
        $this->assertEquals('Hassan Benali', $user->nom_complet);
    }
}