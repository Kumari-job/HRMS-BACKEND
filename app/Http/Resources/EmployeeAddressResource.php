<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAddressResource extends JsonResource
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
            'p_country' => $this->p_country,
            'p_district' => $this->p_district,
            'p_vdc_or_municipality' => $this->p_vdc_or_municipality,
            'p_ward' => $this->p_ward,
            'p_state' => $this->p_state,
            'p_street' => $this->p_street,
            'p_house_number' => $this->p_house_number,
            'p_zip_code' => $this->p_zip_code,
            't_country' => $this->t_country,
            't_district' => $this->t_district,
            't_vdc_or_municipality' => $this->t_vdc_or_municipality,
            't_ward' => $this->t_ward,
            't_state' => $this->t_state,
            't_street' => $this->t_street,
            't_house_number' => $this->t_house_number,
            't_zip_code' => $this->t_zip_code,
            'created_by' => new UserResource($this->createdBy),
            'updated_by' => new UserResource($this->updatedBy)
        ];
    }
}
