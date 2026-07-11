<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'username' => ['sometimes', 'string', 'min:3', 'max:50', "unique:users,username,{$userId}"],
            'email' => ['sometimes', 'email', 'max:255', "unique:users,email,{$userId}"],
            'password' => ['sometimes', 'string', 'min:6', 'max:100'],
            'full_name' => ['sometimes', 'string', 'max:255'],
            'role_ids' => ['sometimes', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'Username sudah digunakan.',
            'username.min' => 'Username minimal 3 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password minimal 6 karakter.',
            'role_ids.*.exists' => 'Role tidak ditemukan.',
        ];
    }
}
