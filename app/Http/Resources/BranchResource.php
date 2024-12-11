<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Carbon\Carbon;
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
            'established_date_formatted' => Carbon::parse($this->established_date)->format('d M Y'),
            'established_date_nepali' => DateHelper::englishToNepali($this->established_date,'Y-m-d'),
            'established_date_nepali_formatted' => DateHelper::englishToNepali($this->established_date),

        ];
    }
}
