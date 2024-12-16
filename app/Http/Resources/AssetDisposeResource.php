<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetDisposeResource extends JsonResource
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
            'details' => $this->details,
            'asset_id' => $this->asset_id,
            'disposed_at' => Carbon::parse($this->disposed_at)->format('Y-m-d'),
            'disposed_at_formatted' => Carbon::parse($this->disposed_at)->format('d M Y'),
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'disposed_by' => new UserResource($this->whenLoaded('disposedBy')),
        ];
    }
}
