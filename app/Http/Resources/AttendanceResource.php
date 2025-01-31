<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'is_present' => $this->is_present,
            'date' => $this->date,
            'punch_in_at' => $this->punch_in_at,
            'punch_in_ip' => $this->punch_in_ip,
            'late_punch_in' => $this->late_punch_in,
            'punch_out_at' => $this->punch_out_at,
            'punch_out_ip' => $this->punch_out_ip,
            'remark' => $this->remark,
            'is_approved' => $this->is_approved,
            'created_by_id' => $this->created_by,
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'employee' => new UserResource($this->whenLoaded('employee')),
        ];
    }
}
