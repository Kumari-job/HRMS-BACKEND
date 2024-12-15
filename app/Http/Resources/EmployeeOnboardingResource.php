<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeOnboardingResource extends JsonResource
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
            'shortlisted_at' => $this->shortlisted_at,
            'interviewed_at' => $this->interviewed_at,
            'offered_at' => $this->offered_at,
            'offer_letter' => $this->offer_letter,
            'offered_by' => new UserResource($this->offeredBy),
            'joined_at' => $this->joined_at,
            'employee_type' => $this->employee_type
        ];
    }
}
