<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PayrollSettingResource extends JsonResource
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
            'cit_number' => $this->cit_number,
            'pf_number' => $this->pf_number,
            'ssf_number' => $this->ssf_number,
            'bank_name' => $this->bank_name,
            'bank_branch_name' => $this->bank_branch_name,
            'bank_account_name' => $this->bank_account_name,
            'bank_account_number' => $this->bank_account_number
        ];
    }
}
