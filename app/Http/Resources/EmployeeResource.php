<?php

namespace App\Http\Resources;

use App\Models\EmployeeAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'email' => $this->email,
            'image' => $this->image,
            'mobile' => $this->mobile,
            'address' => $this->address,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'image_path' => $this->image_path,
            'marital_status' => $this->marital_status,
            'blood_group' => $this->blood_group,
            'religion' => $this->religion,
            'employee_address' => new EmployeeAddressResource($this->whenLoaded('employeeAddress')),
            'employee_benefit' => new EmployeeBenefitResource($this->whenLoaded('employeeBenefit')),
            'employee_contracts' => EmployeeContractResource::collection($this->whenLoaded('employeeContracts')),
            'employee_document' => new EmployeeDocumentResource($this->whenLoaded('employeeDocument')),
            'employee_educations' => EmployeeEducationResource::collection($this->whenLoaded('employeeEducations')),
        ];
    }
}
