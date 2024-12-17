<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Carbon\Carbon;
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
            'shortlisted_at_formatted' => $this->shortlisted_at ? Carbon::parse($this->shortlisted_at)->format('d M Y') : null,
            'shortlisted_at_nepali' => $this->shortlisted_at ? DateHelper::englishToNepali($this->shortlisted_at,'Y-m-d') : null,
            'shortlisted_at_nepali_formatted' => $this->shortlisted_at ? DateHelper::englishToNepali($this->shortlisted_at): null,
            'interviewed_at' => $this->interviewed_at,
            'interviewed_at_formatted' => $this->interviewed_at ? Carbon::parse($this->interviewed_at)->format('d M Y') : null,
            'interviewed_at_nepali' => $this->interviewed_at ? DateHelper::englishToNepali($this->interviewed_at,'Y-m-d') : null,
            'interviewed_at_nepali_formatted' => $this->interviewed_at ? DateHelper::englishToNepali($this->interviewed_at): null,
            'offered_at' => $this->offered_at,
            'offered_at_formatted' => $this->interviewed_at ? Carbon::parse($this->offered_at)->format('d M Y') : null,
            'offered_at_nepali' => $this->interviewed_at ? DateHelper::englishToNepali($this->offered_at,'Y-m-d') : null,
            'offered_at_nepali_formatted' => $this->interviewed_at ? DateHelper::englishToNepali($this->offered_at): null,
            'offer_letter' => $this->offer_letter,
            'offered_by' => new UserResource($this->offeredBy),
            'joined_at' => $this->joined_at,
            'joined_at_formatted' => $this->interviewed_at ? Carbon::parse($this->joined_at)->format('d M Y') : null,
            'joined_at_nepali' => $this->interviewed_at ? DateHelper::englishToNepali($this->joined_at,'Y-m-d') : null,
            'joined_at_nepali_formatted' => $this->interviewed_at ? DateHelper::englishToNepali($this->joined_at): null,
            'employee_type' => $this->employee_type
        ];
    }
}
