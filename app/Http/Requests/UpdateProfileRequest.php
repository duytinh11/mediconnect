<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes','email',
                Rule::unique('users','email')->ignore($this->user()->id),
            ],
            'phone' => [
                'sometimes','string','max:20',
                Rule::unique('users','phone')->ignore($this->user()->id),
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'avatar' => 'sometimes|file|image|max:2048',
            'address' => 'sometimes|string|max:255',
            'gender' => 'sometimes|nullable|in:male,female,other',
            'dob' => 'sometimes|nullable|date',
        ];
    }
}
