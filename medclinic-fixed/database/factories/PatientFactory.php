<?php
// database/factories/PatientFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name'    => fake()->firstName(),
            'last_name'     => fake()->lastName(),
            'cin'           => strtoupper(fake()->unique()->bothify('??######')),
            'phone'         => '06' . fake()->numerify('########'),
            'email'         => fake()->safeEmail(),
            'date_of_birth' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
            'gender'        => fake()->randomElement(['male', 'female']),
            'blood_type'    => fake()->randomElement(['A+','A-','B+','B-','AB+','AB-','O+','O-']),
            'address'       => fake()->address(),
        ];
    }
}


// ----------------------------------------------------------------
// database/factories/AppointmentFactory.php
// ----------------------------------------------------------------
namespace Database\Factories;

use App\Models\{Patient, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id'       => Patient::factory(),
            'doctor_id'        => User::factory()->doctor(),
            'appointment_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'status'           => fake()->randomElement(['pending','confirmed','cancelled','completed']),
            'reason'           => fake()->randomElement([
                'Douleur abdominale', 'Fièvre', 'Contrôle annuel',
                'Mal de tête', 'Toux persistante', 'Consultation générale',
            ]),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => 'confirmed']);
    }

    public function completed(): static
    {
        return $this->state([
            'status'           => 'completed',
            'appointment_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
