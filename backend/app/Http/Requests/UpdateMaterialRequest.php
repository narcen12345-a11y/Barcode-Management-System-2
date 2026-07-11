<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $materialId = $this->route('material');

        return [
            'material_type_id' => ['sometimes', 'integer', 'exists:material_types,id'],
            'material_model_id' => ['sometimes', 'integer', 'exists:material_models,id'],
            'material_code' => ['sometimes', 'string', 'max:50', "unique:materials,material_code,{$materialId}"],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
