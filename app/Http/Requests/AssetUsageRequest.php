<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssetUsageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:employees,id',
            'assigned_at' => 'required_if:assigned_at_nepali,null|date',
            'assigned_at_nepali' => 'required_if:assigned_at,null|date',
            'assigned_end_at' => 'nullable|date|after_or_equal:assigned_at',
            'assigned_end_at_nepali' => 'nullable|date|after_or_equal:assigned_at_nepali',
            'assigned_by' => 'required|integer|exists:users,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = [
                'error' => true,
                'errors' => $validator->errors(),
                'message' => 'There are some issues in the form.'
            ];

            throw new HttpResponseException(response()->json($response, 422));
        }

        parent::failedValidation($validator);
    }
}
