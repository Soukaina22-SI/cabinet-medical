<?php
// tests/Feature/StatisticsTest.php
namespace Tests\Feature;

use App\Models\{Appointment, Consultation, Patient, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin  = User::factory()->create(['role' => 'admin']);
        $this->doctor = User::factory()->create(['role' => 'medecin']);
    }

    public function test_admin_can_access_statistics_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.statistics'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.statistics');
    }

    public function test_statistics_page_has_required_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.statistics'));

        $response->assertViewHasAll([
            'kpis',
            'appointmentsPerDay',
            'appointmentsPerDoctor',
            'appointmentsByHour',
            'newPatientsPerMonth',
            'statusBreakdown',
            'period',
        ]);
    }

    public function test_statistics_period_filter_works(): void
    {
        foreach (['week', 'month', 'year'] as $period) {
            $response = $this->actingAs($this->admin)
                ->get(route('admin.statistics', ['period' => $period]));

            $response->assertStatus(200);
            $response->assertViewHas('period', $period);
        }
    }

    public function test_kpis_count_new_patients_correctly(): void
    {
        // Create 3 patients this month and 2 last month
        Patient::factory()->count(3)->create();
        Patient::factory()->count(2)->create([
            'created_at' => now()->subMonths(2),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.statistics', ['period' => 'month']));

        $kpis = $response->viewData('kpis');
        $this->assertEquals(3, $kpis['new_patients']);
    }

    public function test_doctor_cannot_access_statistics(): void
    {
        $response = $this->actingAs($this->doctor)
            ->get(route('admin.statistics'));

        $response->assertStatus(403);
    }

    public function test_cancellation_rate_calculated_correctly(): void
    {
        $patient = Patient::factory()->create();

        // 8 confirmed + 2 cancelled = 20% cancellation
        Appointment::factory()->count(8)->create([
            'doctor_id'  => $this->doctor->id,
            'patient_id' => $patient->id,
            'status'     => 'confirmed',
        ]);
        Appointment::factory()->count(2)->create([
            'doctor_id'  => $this->doctor->id,
            'patient_id' => $patient->id,
            'status'     => 'cancelled',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.statistics', ['period' => 'year']));

        $kpis = $response->viewData('kpis');
        $this->assertEquals(20.0, $kpis['cancellation_rate']);
    }
}
