<?php
// database/factories/ConsultationFactory.php
namespace Database\Factories;

use App\Models\{Appointment, Patient, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'patient_id'     => Patient::factory(),
            'doctor_id'      => User::factory()->doctor(),
            'symptoms'       => fake()->randomElement([
                'Douleur abdominale intense',
                'Fièvre à 38.5°C avec frissons',
                'Toux persistante depuis 2 semaines',
                'Mal de tête récurrent',
                'Fatigue chronique',
            ]),
            'diagnosis' => fake()->randomElement([
                'Gastro-entérite virale',
                'Grippe saisonnière',
                'Bronchite aiguë',
                'Migraine',
                'Syndrome de fatigue chronique',
            ]),
            'notes'          => fake()->optional()->sentence(),
            'temperature'    => fake()->randomFloat(1, 36.0, 39.5),
            'blood_pressure' => rand(110, 145) . '/' . rand(65, 95),
            'weight'         => fake()->randomFloat(1, 45, 120),
            'height'         => fake()->randomFloat(1, 150, 195),
        ];
    }
}
