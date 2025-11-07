<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        $patient = $this->route('patient');

        return [
            'name' => 'sometimes|string|max:255',
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($patient->user_id),
            ],
            'status' => 'sometimes|in:active,inactive',
            'address' => 'sometimes|string|max:255',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'dob' => 'sometimes|nullable|date',
        ];
    }
}
