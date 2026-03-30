<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // number is immutable — not allowed in update
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }
}