<?php

namespace App\Http\Requests;

use App\Enums\BarcodeStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateBarcodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $barcodeId = $this->route('barcode');

        return [
            'material_id' => ['sometimes', 'required', 'integer', 'exists:materials,id'],
            'site_id' => ['sometimes', 'required', 'integer', 'exists:sites,id'],
            'serial_number' => ['sometimes', 'required', 'string', 'max:255', 'unique:barcodes,serial_number,' . $barcodeId],
            'status' => ['sometimes', 'required', 'string', new Enum(BarcodeStatusEnum::class)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'material_id.required' => 'Material wajib dipilih.',
            'material_id.exists' => 'Material tidak valid.',
            'site_id.required' => 'Site wajib dipilih.',
            'site_id.exists' => 'Site tidak valid.',
            'serial_number.required' => 'Serial Number wajib diisi.',
            'serial_number.unique' => 'Serial Number sudah digunakan.',
            'status.required' => 'Status wajib dipilih.',
            'status.Illuminate\Validation\Rules\Enum' => 'Status tidak valid. Pilih NEW atau OLD.',
        ];
    }
}
