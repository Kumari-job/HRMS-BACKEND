<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'department_id' => $this->department_id,
            'employee_id' => $this->employee_id,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
        ];
    }
}
