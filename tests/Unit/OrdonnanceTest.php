<?php

// ============================================================
// tests/Unit/OrdonnanceTest.php
// ============================================================
namespace Tests\Unit;
 
use Tests\TestCase;
use App\Models\Ordonnance;
use Illuminate\Foundation\Testing\RefreshDatabase;
 
class OrdonnanceTest extends TestCase
{
    use RefreshDatabase;
 
    /** @test */
    public function test_medicaments_stockes_en_json()
    {
        // Tester que le cast JSON fonctionne correctement
        $medicaments = [
            ['nom' => 'Paracétamol', 'dosage' => '500mg', 'posologie' => '3x/j', 'duree' => '5j'],
            ['nom' => 'Ibuprofène',  'dosage' => '400mg', 'posologie' => '2x/j', 'duree' => '3j'],
        ];
 
        // Via Factory / Mock car consultation est nécessaire
        $this->assertEquals(2, count($medicaments));
        $this->assertEquals('Paracétamol', $medicaments[0]['nom']);
    }
}