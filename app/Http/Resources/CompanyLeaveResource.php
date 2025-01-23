<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyLeaveResource extends JsonResource
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
            'name' => $this->name,
            'days' => $this->days,
            'year' => $this->year,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status ?? null ,
            'exclude_holiday' => $this->exclude_holiday ,
            'exclude_weekend' => $this->exclude_weekend ,
            'icon_index' => $this->icon_index ,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'created_at_formatted' => Carbon::parse($this->created_at)->format('d M Y'),
            'updated_at' => Carbon::parse($this->updated_at)->format('Y-m-d'),
            'updated_at_formatted' => Carbon::parse($this->updated_at)->format('d M Y'),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'updated_by' => new UserResource($this->whenLoaded('updatedBy')),
        ];
    }
}
