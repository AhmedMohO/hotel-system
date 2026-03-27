<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $manager = $this->route('manager');
        $managerId = is_object($manager) ? $manager->id : $manager;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $managerId],
            'password' => ['nullable', 'string', 'min:6'],
            'national_id' => ['required', 'string', 'unique:users,national_id,' . $managerId],
            'avatar_image' => \App\Services\AvatarService::rules(),
        ];
    }
}
