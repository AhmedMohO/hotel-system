<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceptionistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $receptionist = $this->route('receptionist');
        $receptionistId = is_object($receptionist) ? $receptionist->id : $receptionist;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $receptionistId],
            'password' => ['nullable', 'string', 'min:6'],
            'national_id' => ['required', 'string', 'unique:users,national_id,' . $receptionistId],
            'avatar_image' => \App\Services\AvatarService::rules(),
        ];
    }
}
