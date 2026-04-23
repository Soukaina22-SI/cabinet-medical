<?php
// tests/Feature/ConsultationTest.php
namespace Tests\Feature;

use App\Models\{Appointment, Consultation, DoctorSchedule, Patient, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    private User    $doctor;
    private User    $admin;
    private Patient $patient;
    private Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor  = User::factory()->create(['role' => 'medecin']);
        $this->admin   = User::factory()->create(['role' => 'admin']);
        $this->patient = Patient::factory()->create();

        $this->appointment = Appointment::factory()->create([
            'doctor_id'  => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'status'     => 'confirmed',
        ]);
    }

    public function test_doctor_can_view_consultation_form(): void
    {
        $response = $this->actingAs($this->doctor)
            ->get(route('consultations.create', $this->appointment));

        $response->assertStatus(200);
        $response->assertViewHas('appointment');
    }

    public function test_doctor_can_create_consultation(): void
    {
        $response = $this->actingAs($this->doctor)
            ->post(route('consultations.store', $this->appointment), [
                'symptoms'  => 'Douleur thoracique et essoufflement',
                'diagnosis' => 'Bronchite légère',
                'notes'     => 'Repos recommandé pendant 5 jours',
                'temperature' => 37.8,
                'blood_pressure' => '120/80',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('consultations', [
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'doctor_id'      => $this->doctor->id,
            'diagnosis'      => 'Bronchite légère',
        ]);
    }

    public function test_consultation_marks_appointment_as_completed(): void
    {
        $this->actingAs($this->doctor)
            ->post(route('consultations.store', $this->appointment), [
                'symptoms'  => 'Fièvre à 39°C',
                'diagnosis' => 'Grippe',
            ]);

        $this->assertDatabaseHas('appointments', [
            'id'     => $this->appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_consultation_with_prescription_creates_prescription_items(): void
    {
        $this->actingAs($this->doctor)
            ->post(route('consultations.store', $this->appointment), [
                'symptoms'    => 'Infection',
                'diagnosis'   => 'Amygdalite bactérienne',
                'medications' => [
                    [
                        'name'         => 'Amoxicilline',
                        'dosage'       => '500mg',
                        'frequency'    => '3 fois/jour',
                        'duration'     => '7 jours',
                        'instructions' => 'Prendre avec de la nourriture',
                    ]
                ],
            ]);

        $consultation = Consultation::where('appointment_id', $this->appointment->id)->first();

        $this->assertNotNull($consultation);
        $this->assertNotNull($consultation->prescription);
        $this->assertDatabaseHas('prescription_items', [
            'medication_name' => 'Amoxicilline',
            'dosage'          => '500mg',
        ]);
    }

    public function test_consultation_cannot_be_created_twice_for_same_appointment(): void
    {
        // Create first consultation
        Consultation::factory()->create([
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'doctor_id'      => $this->doctor->id,
        ]);

        // Try to access create form again — should redirect
        $response = $this->actingAs($this->doctor)
            ->get(route('consultations.create', $this->appointment));

        $response->assertRedirect();
    }

    public function test_non_doctor_cannot_create_consultation(): void
    {
        $secretary = User::factory()->create(['role' => 'secretaire']);

        $response = $this->actingAs($secretary)
            ->post(route('consultations.store', $this->appointment), [
                'symptoms'  => 'Test',
                'diagnosis' => 'Test',
            ]);

        // Should be forbidden (403) or unauthorized
        $response->assertStatus(403);
    }

    public function test_doctor_can_view_consultation_details(): void
    {
        $consultation = Consultation::factory()->create([
            'appointment_id' => $this->appointment->id,
            'patient_id'     => $this->patient->id,
            'doctor_id'      => $this->doctor->id,
        ]);

        $response = $this->actingAs($this->doctor)
            ->get(route('consultations.show', $consultation));

        $response->assertStatus(200);
        $response->assertViewHas('consultation');
    }

    public function test_consultation_requires_symptoms_and_diagnosis(): void
    {
        $response = $this->actingAs($this->doctor)
            ->post(route('consultations.store', $this->appointment), [
                'symptoms' => '', // missing
                'diagnosis' => '', // missing
            ]);

        $response->assertSessionHasErrors(['symptoms', 'diagnosis']);
    }
}
