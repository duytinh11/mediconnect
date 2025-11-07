<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        $doctor = $this->route('doctor');

        return [
            'name' => 'sometimes|string|max:255',
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($doctor->user_id),
            ],
            'status' => 'sometimes|in:active,inactive',
            'city_id' => 'sometimes|nullable|exists:cities,id',
            'specialty' => 'sometimes|string|max:255',
            'license_number' => 'sometimes|nullable|string|max:50',
            'degrees' => 'sometimes|nullable|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'available_slots' => 'sometimes|array',
        ];
    }
}
