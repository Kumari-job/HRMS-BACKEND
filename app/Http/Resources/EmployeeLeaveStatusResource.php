<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLeaveStatusResource extends JsonResource
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
            'employee_leave_id' => $this->employee_leave_id,
            'requested_to_id' => $this->requested_to,
            'status' => $this->status,
            'remark' => $this->remark,
            'requested_to' => new UserResource($this->whenLoaded('requestedTo')),
        ];
    }
}
