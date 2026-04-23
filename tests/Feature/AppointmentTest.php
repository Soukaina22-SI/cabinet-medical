<?php
// tests/Feature/AppointmentTest.php
namespace Tests\Feature;

use App\Models\{Appointment, DoctorSchedule, Patient, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private User    $doctor;
    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin  = User::factory()->create(['role' => 'admin']);
        $this->doctor = User::factory()->create(['role' => 'medecin', 'speciality' => 'Généraliste']);
        $this->patient = Patient::factory()->create();

        // Doctor works Monday–Friday 09:00–17:00
        for ($day = 1; $day <= 5; $day++) {
            DoctorSchedule::create([
                'doctor_id'    => $this->doctor->id,
                'day_of_week'  => $day,
                'start_time'   => '09:00',
                'end_time'     => '17:00',
                'is_available' => true,
            ]);
        }
    }

    public function test_admin_can_view_appointments_list(): void
    {
        Appointment::factory()->count(3)->create([
            'doctor_id'  => $this->doctor->id,
            'patient_id' => $this->patient->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('appointments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('appointments');
    }

    public function test_admin_can_create_appointment(): void
    {
        $date = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);

        $response = $this->actingAs($this->admin)->post(route('appointments.store'), [
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'appointment_date' => $date->format('Y-m-d H:i:s'),
            'reason'           => 'Consultation générale',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $this->patient->id,
            'doctor_id'  => $this->doctor->id,
            'status'     => 'pending',
        ]);
    }

    public function test_appointment_requires_future_date(): void
    {
        $response = $this->actingAs($this->admin)->post(route('appointments.store'), [
            'patient_id'       => $this->patient->id,
            'doctor_id'        => $this->doctor->id,
            'appointment_date' => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('appointment_date');
    }

    public function test_doctor_can_update_appointment_status(): void
    {
        $appt = Appointment::factory()->create([
            'doctor_id'  => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'status'     => 'pending',
        ]);

        $response = $this->actingAs($this->doctor)
            ->patchJson(route('appointments.update-status', $appt), ['status' => 'confirmed']);

        $response->assertOk();
        $this->assertDatabaseHas('appointments', ['id' => $appt->id, 'status' => 'confirmed']);
    }

    public function test_available_slots_returns_slots_for_valid_schedule(): void
    {
        // Find next Monday
        $nextMonday = now()->next('Monday');

        $response = $this->actingAs($this->admin)
            ->getJson(route('appointments.available-slots', [
                'doctor_id' => $this->doctor->id,
                'date'      => $nextMonday->format('Y-m-d'),
            ]));

        $response->assertOk();
        $response->assertJsonStructure(['slots']);
        $this->assertNotEmpty($response->json('slots'));
    }

    public function test_patient_cannot_access_admin_dashboard(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);

        $response = $this->actingAs($patient)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }
}


// ================================================================
// tests/Feature/PatientTest.php
// ================================================================
namespace Tests\Feature;

use App\Models\{Patient, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_patients_list(): void
    {
        Patient::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.patients.index'));

        $response->assertStatus(200);
        $response->assertViewHas('patients');
    }

    public function test_admin_can_create_patient(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.patients.store'), [
            'first_name'    => 'Mohamed',
            'last_name'     => 'Alaoui',
            'phone'         => '0612345678',
            'date_of_birth' => '1990-05-15',
            'gender'        => 'male',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'first_name' => 'Mohamed',
            'last_name'  => 'Alaoui',
        ]);
    }

    public function test_patient_requires_valid_date_of_birth(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.patients.store'), [
            'first_name'    => 'Test',
            'last_name'     => 'User',
            'phone'         => '0600000000',
            'date_of_birth' => now()->addDay()->format('Y-m-d'), // future date
            'gender'        => 'male',
        ]);

        $response->assertSessionHasErrors('date_of_birth');
    }

    public function test_patient_cin_must_be_unique(): void
    {
        Patient::factory()->create(['cin' => 'AB123456']);

        $response = $this->actingAs($this->admin)->post(route('admin.patients.store'), [
            'first_name'    => 'Other',
            'last_name'     => 'Patient',
            'phone'         => '0600000000',
            'cin'           => 'AB123456', // duplicate
            'date_of_birth' => '1990-01-01',
            'gender'        => 'male',
        ]);

        $response->assertSessionHasErrors('cin');
    }

    public function test_admin_can_search_patients(): void
    {
        Patient::factory()->create(['first_name' => 'Youssef', 'last_name' => 'Tazi']);
        Patient::factory()->create(['first_name' => 'Aicha',   'last_name' => 'Benali']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.patients.index', ['search' => 'Tazi']));

        $response->assertStatus(200);
        $response->assertSee('Tazi');
        $response->assertDontSee('Benali');
    }

    public function test_admin_can_delete_patient(): void
    {
        $patient = Patient::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.patients.destroy', $patient));

        $response->assertRedirect(route('admin.patients.index'));
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }
}
