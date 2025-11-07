<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'patient_id' => Patient::factory(),
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'canceled']),
            'reason' => $this->faker->sentence(),
        ];
    }
}
