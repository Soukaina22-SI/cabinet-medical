<?php
// tests/Unit/PatientModelTest.php
namespace Tests\Unit;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_name_accessor(): void
    {
        $patient = new Patient([
            'first_name' => 'Mohamed',
            'last_name'  => 'Alaoui',
        ]);

        $this->assertEquals('Mohamed Alaoui', $patient->full_name);
    }

    public function test_age_accessor(): void
    {
        $patient = Patient::factory()->create([
            'date_of_birth' => Carbon::now()->subYears(30)->format('Y-m-d'),
        ]);

        $this->assertEquals(30, $patient->age);
    }

    public function test_search_scope_by_first_name(): void
    {
        Patient::factory()->create(['first_name' => 'Youssef', 'last_name' => 'Tazi']);
        Patient::factory()->create(['first_name' => 'Aicha',   'last_name' => 'Benali']);

        $results = Patient::search('Youssef')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Youssef', $results->first()->first_name);
    }

    public function test_search_scope_by_cin(): void
    {
        Patient::factory()->create(['cin' => 'AB123456', 'first_name' => 'Test']);
        Patient::factory()->create(['cin' => 'ZZ999999', 'first_name' => 'Other']);

        $results = Patient::search('AB123456')->get();
        $this->assertCount(1, $results);
    }

    public function test_search_scope_is_case_insensitive(): void
    {
        Patient::factory()->create(['first_name' => 'Mohamed', 'last_name' => 'Alaoui']);

        $results = Patient::search('MOHAMED')->get();
        $this->assertCount(1, $results);
    }
}
