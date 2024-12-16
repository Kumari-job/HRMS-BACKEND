<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetMaintenanceResource extends JsonResource
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
            'problem' => $this->problem,
            'details' => $this->details,
            'start_date' => Carbon::parse($this->start_date)->format('Y-m-d'),
            'start_date_formatted' => Carbon::parse($this->start_date)->format('d M Y'),
            'end_date' => Carbon::parse($this->end_date)->format('Y-m-d'),
            'end_date_formatted' => Carbon::parse($this->end_date)->format('d M Y'),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
            'asset'=> new AssetResource($this->whenLoaded('asset')),
        ];
    }
}
