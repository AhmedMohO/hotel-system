<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number'   => ['required', 'string', 'min:4', Rule::unique('rooms', 'number')->ignore($this->room)],
            'floor_id' => ['required', 'exists:floors,id'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price'    => ['required', 'numeric', 'min:0'],
        ];
    }
}