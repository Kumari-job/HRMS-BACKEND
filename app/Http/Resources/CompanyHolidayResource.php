<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyHolidayResource extends JsonResource
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
            'date' => $this->date,
            'date_formatted' => Carbon::parse($this->date)->format('d M Y'),
            'date_nepali' => DateHelper::englishToNepali($this->date,'Y-m-d'),
            'date_nepali_formatted' => DateHelper::englishToNepali($this->date,'d M Y'),
            'holiday' => $this->holiday,
            'description' => $this->description,
            'females_only' => $this->females_only,
            'holiday_for_religion' => $this->holiday_for_religion,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
        ];
    }
}
