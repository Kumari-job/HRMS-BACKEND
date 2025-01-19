<?php

namespace App\Http\Resources;

use App\Models\Employee;
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
            'selected_company' => Auth::user()->selectedCompany->company_id,
        ];
    }
}
