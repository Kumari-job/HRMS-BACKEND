<?php

namespace App\Http\Resources;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
            'idp_user_id' => $this->idp_user_id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'image_path' => $this->image_path,
            'employee_id' => $this->employee_id,
            'selected_company' => Auth::user()->selectedCompany,
            'is_password_changed' => Auth::user()->is_password_changed,
            'last_login' => Carbon::parse($this->last_login)->diffForHumans(),
            'attendance' => AttendanceResource::collection($this->whenLoaded('attendances')),
            'employee' => new EmployeeResource($this->whenLoaded('employee')),
            'role' => RoleResource::collection($this->whenLoaded('roles'))

        ];
    }
}
