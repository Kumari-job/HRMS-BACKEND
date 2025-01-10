<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyProfileResource extends JsonResource
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
            'company_id' => $this->company_id,
            'fiscal_calendar_type' => $this->fiscal_calendar_type,
            'fiscal_start_month' => $this->fiscal_start_month,
            'week_start_day' => $this->week_start_day,
            'week_end_day' => $this->week_end_day,
            'weekly_leaves' => json_decode($this->weekly_leaves),
            'country' => $this->country
        ];
    }
}
