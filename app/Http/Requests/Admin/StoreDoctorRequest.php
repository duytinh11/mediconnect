<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8',
            'status' => 'nullable|in:active,inactive',
            'city_id' => 'nullable|exists:cities,id',
            'specialty' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:50',
            'degrees' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'available_slots' => 'nullable|array',
        ];
    }
}
