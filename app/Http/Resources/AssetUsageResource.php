<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetUsageResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'assigned_at' => Carbon::parse($this->assigned_at)->format('Y-m-d'),
            'assigned_at_formatted' => Carbon::parse($this->assigned_at)->format('d M Y'),
            'assigned_end_at' => Carbon::parse($this->assigned_end_at)->format('Y-m-d'),
            'assigned_end_at_formatted' => Carbon::parse($this->assigned_end_at)->format('d M Y'),
            'assigned_by' => new UserResource($this->whenLoaded('assignedBy')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'asset' => new AssetResource($this->whenLoaded('asset')),
        ];
    }
}
