<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(fn () => ['role' => 'doctor', 'status' => 'active']),
            'city_id' => City::factory(),
            'specialty' => $this->faker->randomElement(['Cardiology', 'Neurology', 'Pediatrics']),
            'license_number' => 'LIC-' . $this->faker->unique()->randomNumber(4),
            'degrees' => 'MD',
            'bio' => $this->faker->paragraph(),
            'available_slots' => [],
            'status' => 'active',
        ];
    }
}
