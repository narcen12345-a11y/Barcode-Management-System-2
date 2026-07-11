<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarcodeHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'barcode_id' => $this->barcode_id,
            'field_name' => $this->field_name,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'changed_by' => $this->whenLoaded('changedBy', fn() => [
                'id' => $this->changedBy->id,
                'username' => $this->changedBy->username,
                'full_name' => $this->changedBy->full_name,
            ]),
            'change_reason' => $this->change_reason,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
