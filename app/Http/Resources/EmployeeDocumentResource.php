<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDocumentResource extends JsonResource
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
            'citizenship_front' => $this->citizenship_front,
            'citizenship_back' => $this->citizenship_back,
            'citizenship_front_path' => $this->citizenship_front_path,
            'citizenship_back_path' => $this->citizenship_back_path,
            'driving_license' => $this->driving_license,
            'driving_license_path' => $this->driving_license_path,
            'passport' => $this->passport,
            'passport_path' => $this->passport_path,
            'pan_card' => $this->pan_card,
            'pan_card_path' => $this->pan_card_path
        ];
    }
}
