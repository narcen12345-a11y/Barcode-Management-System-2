<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarcodeDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'barcode_id' => $this->barcode_id,
            'material' => new MaterialResource($this->whenLoaded('material')),
            'site' => new SiteResource($this->whenLoaded('site')),
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'created_by' => $this->whenLoaded('createdBy', fn() => [
                'id' => $this->createdBy->id,
                'username' => $this->createdBy->username,
                'full_name' => $this->createdBy->full_name,
            ]),
            'updated_by' => $this->whenLoaded('updatedBy', fn() => [
                'id' => $this->updatedBy->id,
                'username' => $this->updatedBy->username,
                'full_name' => $this->updatedBy->full_name,
            ]),
            'histories' => BarcodeHistoryResource::collection($this->whenLoaded('histories')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'deleted_at' => $this->deleted_at?->toISOString(),
        ];
    }
}
