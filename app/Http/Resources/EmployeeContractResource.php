<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeContractResource extends JsonResource
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
            'contract_type' => $this->contract_type,
            'job_description' => $this->job_description,
            'gross_salary' => $this->gross_salary,
            'basic_salary' => $this->basic_salary,
            'pf_from_employee' => $this->pf_from_employee,
            'pf_from_company' => $this->pf_from_company,
            'gratuity' => $this->gratuity,
            'cit_percent' => $this->cit_percentage,
            'cit_amount' => $this->cit_amount,
            'ssf_amount' => $this->ssf_amount,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => new UserResource($this->updatedBy)
        ];
    }
}
