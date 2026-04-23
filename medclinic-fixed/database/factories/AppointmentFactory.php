<?php
// database/factories/AppointmentFactory.php
namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_id'       => Patient::factory(),
            'doctor_id'        => User::factory()->doctor(),
            'appointment_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'status'           => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'reason'           => fake()->randomElement([
                'Douleur abdominale', 'Fièvre', 'Contrôle annuel',
                'Mal de tête', 'Toux persistante', 'Consultation générale',
            ]),
            'reminder_sent' => false,
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

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }

    public function completed(): static
    {
        return $this->state([
            'status'           => 'completed',
            'appointment_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function today(): static
    {
        return $this->state([
            'appointment_date' => now()->setHour(rand(9, 16))->setMinute(0)->setSecond(0),
        ]);
    }
}
