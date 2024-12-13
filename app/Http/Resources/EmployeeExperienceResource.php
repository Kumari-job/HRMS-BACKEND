<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeExperienceResource extends JsonResource
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
            'designation' => $this->designation,
            'industry' => $this->industry,
            'job_level' => $this->job_level,
            'company' => $this->company,
            'experience_letter' => $this->experience_letter,
            'experience_letter_path' => $this->experience_letter_path,
            'from_date'=> $this->from_date,
            'to_date' => $this->to_date
        ];
    }
}
