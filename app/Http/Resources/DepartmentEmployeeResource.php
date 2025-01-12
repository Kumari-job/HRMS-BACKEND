<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentEmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $pivot = $this->pivot;

        return [
            'id' => $pivot->id ?? $this->id,
            'department_id' => $pivot->department_id ?? $this->department_id,
            'employee_id' => $pivot->employee_id ?? $this->employee_id,
            'designation' => $pivot->designation ?? $this->designation,
            'department_name' => $this->name ?? null,
            'telephone' => $pivot->telephone ?? $this->telephone,
            'email' => $pivot->email ?? $this->email,
            'mobile' => $pivot->mobile ?? $this->mobile,

            'joined_at' => $pivot->joined_at ?? $this->joined_at,
            'joined_at_formatted' => $pivot && $pivot->joined_at
                ? Carbon::parse($pivot->joined_at)->format('Y-m-d')
                : ($this->joined_at ? Carbon::parse($this->joined_at)->format('Y-m-d') : null),
            'joined_at_nepali' => $pivot && $pivot->joined_at
                ? DateHelper::englishToNepali($pivot->joined_at, 'Y-m-d')
                : ($this->joined_at ? DateHelper::englishToNepali($this->joined_at, 'Y-m-d') : null),
            'joined_at_nepali_formatted' => $pivot && $pivot->joined_at
                ? DateHelper::englishToNepali($pivot->joined_at)
                : ($this->joined_at ? DateHelper::englishToNepali($this->joined_at) : null),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
        ];
    }
}
