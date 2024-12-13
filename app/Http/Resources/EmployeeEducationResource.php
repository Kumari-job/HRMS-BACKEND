<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeEducationResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'degree' => $this->degree,
            'field_of_study' => $this->field_of_study,
            'institution' => $this->institution,
            'university_board' => $this->university_board,
            'certificate' => $this->certificate,
            'certificate_path' => $this->certificate_path,
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => new UserResource($this->updatedBy)
        ];
    }
}
