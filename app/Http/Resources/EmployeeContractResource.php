<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'probation_end_at' => $this->probation_end_at,
            'probation_end_at_formatted' => Carbon::parse($this->probation_end_at)->format('d M Y'),
            'gross_salary' => $this->gross_salary,
            'basic_salary' => $this->basic_salary,
            'pf_from_employee' => $this->pf_from_employee,
            'extra_pf_from_employee' => $this->extra_pf_from_employee,
            'pf_from_company' => $this->pf_from_company,
            'ssf_from_employee' => $this->ssf_from_employee,
            'extra_ssf_from_employee' => $this->extra_ssf_from_employee,
            'ssf_from_company' => $this->ssf_from_company,
            'gratuity' => $this->gratuity,
            'cit_amount' => $this->cit_amount,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => new UserResource($this->updatedBy)
        ];
    }
}
