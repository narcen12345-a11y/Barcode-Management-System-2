<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'material_type_id' => ['required', 'integer', 'exists:material_types,id'],
            'material_model_id' => ['required', 'integer', 'exists:material_models,id'],
            'material_code' => ['required', 'string', 'max:50', 'unique:materials,material_code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
