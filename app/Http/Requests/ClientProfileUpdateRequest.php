<?php

namespace App\Http\Requests;

use App\Services\AvatarService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('client') !== null;
    }

    public function rules(): array
    {
        $client = $this->user('client');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($client?->id),
            ],
            'mobile' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:Male,Female'],
            'avatar_image' => AvatarService::rules(),
            'remove_avatar' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}

