<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeBenefitResource extends JsonResource
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
            'employee_id' => $this->id,
            'pan' => $this->pan,
            'ssf' => $this->ssf,
            'cit' => $this->cit,
            'pf' => $this->pf,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => new UserResource($this->updatedBy)
        ];
    }
}
