<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
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
            'assigned_at_nepali_formatted' => DateHelper::englishToNepali($this->assigned_at),
            'assigned_at_nepali' => DateHelper::englishToNepali($this->assigned_at,'Y-m-d'),
            'assigned_end_at' => $this->assigned_end_at ? Carbon::parse($this->assigned_end_at)->format('Y-m-d') : null,
            'assigned_end_at_formatted' => $this->assigned_end_at ?  Carbon::parse($this->assigned_end_at)->format('d M Y'): null,
            'assigned_end_at_nepali_formatted' => $this->assigned_end_at ? DateHelper::englishToNepali($this->assigned_end_at): null,
            'assigned_end_at_nepali' => $this->assigned_end_at ? DateHelper::englishToNepali($this->assigned_end_at,'Y-m-d') : null,
            'assigned_by' => new UserResource($this->whenLoaded('assignedBy')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'asset' => new AssetResource($this->whenLoaded('asset')),
        ];
    }
}
