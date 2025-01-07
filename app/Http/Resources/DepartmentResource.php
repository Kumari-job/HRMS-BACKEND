<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'name' => $this->name,
            'branch' => $this->whenLoaded('branch'),
            'head_of_department' => $this->whenLoaded('headOfDepartment'),
            'created_by' => $this->created_by,
            'employees_count' => $this->employees_count
        ];
    }
}
