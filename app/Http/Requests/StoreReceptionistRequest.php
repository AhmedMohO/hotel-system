<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceptionistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'national_id' => ['required', 'string', 'unique:users,national_id'],
            'avatar_image' => ['nullable', 'image', 'mimes:jpg,jpeg', 'max:2048'],
        ];
    }
}
