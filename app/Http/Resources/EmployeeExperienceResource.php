<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Carbon\Carbon;
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
            'from_date_formatted' => Carbon::parse($this->from_date)->format('d M Y'),
            'from_date_nepali_formatted' => DateHelper::englishToNepali($this->from_date),
            'from_date_nepali' => DateHelper::englishToNepali($this->from_date, 'Y-m-d'),
            'to_date' => $this->to_date,
            'to_date_formatted' => Carbon::parse($this->to_date)->format('d M Y'),
            'to_date_nepali_formatted' => DateHelper::englishToNepali($this->to_date),
            'to_date_nepali' => DateHelper::englishToNepali($this->to_date, 'Y-m-d'),
        ];
    }
}
