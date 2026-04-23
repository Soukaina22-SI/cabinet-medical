<?php
// database/factories/UserFactory.php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'      => fake()->name(),
            'email'     => fake()->unique()->safeEmail(),
            'password'  => Hash::make('password'),
            'role'      => 'patient',
            'phone'     => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function doctor(): static
    {
        return $this->state([
            'role'       => 'medecin',
            'speciality' => fake()->randomElement(['Généraliste','Cardiologue','Pédiatre','Dermatologue']),
        ]);
    }

    public function secretary(): static
    {
        return $this->state(['role' => 'secretaire']);
    }
}
