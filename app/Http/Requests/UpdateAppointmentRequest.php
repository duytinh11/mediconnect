<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');
        $user = $this->user();

        if (!$appointment || !$user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'doctor') {
            return optional($user->doctor)->id === $appointment->doctor_id;
        }

        if ($user->role === 'patient') {
            return optional($user->patient)->id === $appointment->patient_id;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'scheduled_at' => 'sometimes|date|after:now',
            'status' => 'sometimes|in:pending,confirmed,completed,canceled',
            'reason' => 'sometimes|nullable|string|max:500',
        ];
    }
}
