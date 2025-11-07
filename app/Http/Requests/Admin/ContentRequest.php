<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:120',
            'body' => 'required|string',
            'published' => 'required|boolean',
            'image' => 'sometimes|file|image|max:2048',
        ];
    }
}
