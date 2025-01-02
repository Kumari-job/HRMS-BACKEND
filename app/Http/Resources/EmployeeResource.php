<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use App\Models\EmployeeAddress;
use Carbon\Carbon;
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
            'general_information' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'image' => $this->image,
                'mobile' => $this->mobile,
                'address' => $this->address,
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'date_of_birth_formatted' => $this->date_of_birth ? Carbon::parse($this->date_of_birth)->format('d M Y') : null,
                'date_of_birth_nepali' => $this->date_of_birth ? DateHelper::englishToNepali($this->date_of_birth,'Y-m-d') : null,
                'date_of_birth_nepali_formatted' => $this->date_of_birth ? DateHelper::englishToNepali($this->date_of_birth) : null,
                'image_path' => $this->image_path,
                'marital_status' => $this->marital_status,
                'blood_group' => $this->blood_group,
                'religion' => $this->religion,
                'total_experience' => $this->total_experience
            ],
            'employee_address' => new EmployeeAddressResource($this->whenLoaded('employeeAddress')),
            'employee_benefit' => new EmployeeBenefitResource($this->whenLoaded('employeeBenefit')),
            'employee_contracts' => EmployeeContractResource::collection($this->whenLoaded('employeeContracts')),
            'employee_document' => new EmployeeDocumentResource($this->whenLoaded('employeeDocument')),
            'employee_educations' => EmployeeEducationResource::collection($this->whenLoaded('employeeEducations')),
            'employee_experiences' => EmployeeExperienceResource::collection($this->whenLoaded('employeeExperiences')),
            'employee_families' => EmployeeFamilyResource::collection($this->whenLoaded('employeeFamilies')),
            'employee_onboarding' => EmployeeOnboardingResource::collection($this->whenLoaded('employeeOnboardings')),
            'employee_banks' => EmployeeBankResource::collection($this->whenLoaded('employeeBanks')),
            'department_employee' => DepartmentEmployeeResource::collection($this->whenLoaded('departments')),
            'asset_usage' => AssetUsageResource::collection($this->whenLoaded('assetUsages')),
        ];
    }
}
