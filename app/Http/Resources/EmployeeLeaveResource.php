<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLeaveResource extends JsonResource
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
            'leave_id' => $this->leave_id,
            'leave_type' => $this->leave_type,
            'reason' => $this->reason,
            'start_date' => $this->start_date,
            'start_date_formatted' => Carbon::parse($this->start_date)->format('d M Y'),
            'end_date' => $this->end_date,
            'end_date_formatted' => Carbon::parse($this->end_date)->format('d M Y'),
            'start_time' => $this->start_time ? Carbon::parse($this->star_time)->format('H:i') : null,
            'end_time' => $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : null,
            'leave' => new CompanyLeaveResource($this->whenLoaded('leave')),
            'leave_status' => EmployeeLeaveStatusResource::collection($this->whenLoaded('leaveStatus')),
        ];
    }
}
