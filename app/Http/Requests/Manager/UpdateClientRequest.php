<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Manager');
    }

    public function rules(): array
    {
        $clientId = $this->route('client')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('clients', 'email')->ignore($clientId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'mobile' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:Male,Female'],
            'avatar_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
