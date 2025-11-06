<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address' => fake()->streetAddress(),
            'gender' => fake()->randomElement(['male','female','other']),
            'dob' => fake()->date(),
        ];
    }
}
