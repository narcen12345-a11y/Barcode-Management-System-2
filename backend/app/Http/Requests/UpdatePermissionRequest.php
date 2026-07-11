<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('permission');

        return [
            'name' => ['sometimes', 'string', 'max:100', "unique:permissions,name,{$id}"],
            'display_name' => ['sometimes', 'string', 'max:100'],
            'module' => ['sometimes', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
