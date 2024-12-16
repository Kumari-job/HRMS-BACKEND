<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asset_id' => $this->asset_id,
            'price' => $this->price,
            'details' => $this->details,
            'sold_to' => $this->sold_to,
            'sold_by' => new UserResource($this->whenLoaded('soldBy')),
            'asset' => new AssetResource($this->whenLoaded('asset')),
        ];
    }
}
