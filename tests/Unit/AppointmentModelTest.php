<?php
// tests/Unit/AppointmentModelTest.php
namespace Tests\Unit;

use App\Models\{Appointment, Patient, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_helpers(): void
    {
        $appt = new Appointment(['status' => 'pending']);
        $this->assertTrue($appt->isPending());
        $this->assertFalse($appt->isConfirmed());

        $appt->status = 'confirmed';
        $this->assertTrue($appt->isConfirmed());
        $this->assertFalse($appt->isPending());

        $appt->status = 'cancelled';
        $this->assertTrue($appt->isCancelled());

        $appt->status = 'completed';
        $this->assertTrue($appt->isCompleted());
    }

    public function test_status_badge_returns_html(): void
    {
        $appt = new Appointment(['status' => 'confirmed']);
        $this->assertStringContainsString('badge', $appt->status_badge);
        $this->assertStringContainsString('Confirmé', $appt->status_badge);
    }

    public function test_today_scope(): void
    {
        $doctor  = User::factory()->create(['role' => 'medecin']);
        $patient = Patient::factory()->create();

        // Today
        Appointment::factory()->create([
            'doctor_id'        => $doctor->id,
            'patient_id'       => $patient->id,
            'appointment_date' => now()->setHour(10),
        ]);

        // Yesterday
        Appointment::factory()->create([
            'doctor_id'        => $doctor->id,
            'patient_id'       => $patient->id,
            'appointment_date' => now()->subDay(),
        ]);

        $this->assertEquals(1, Appointment::today()->count());
    }

    public function test_upcoming_scope_excludes_cancelled(): void
    {
        $doctor  = User::factory()->create(['role' => 'medecin']);
        $patient = Patient::factory()->create();

        Appointment::factory()->create([
            'doctor_id'        => $doctor->id,
            'patient_id'       => $patient->id,
            'appointment_date' => now()->addDay(),
            'status'           => 'confirmed',
        ]);

        Appointment::factory()->create([
            'doctor_id'        => $doctor->id,
            'patient_id'       => $patient->id,
            'appointment_date' => now()->addDay(),
            'status'           => 'cancelled',
        ]);

        $this->assertEquals(1, Appointment::upcoming()->count());
    }
}
