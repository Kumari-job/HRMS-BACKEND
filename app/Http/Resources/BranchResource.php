<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'location' => $this->location,
            'employee_id' => $this->employee_id,
            'contact_number' => $this->contact_number,
            'established_date' => $this->established_date,
            'established_at_nepali' => DateHelper::englishToNepali($this->established_date)
        ];
    }
}
