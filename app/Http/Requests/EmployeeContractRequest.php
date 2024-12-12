<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeContractRequest extends FormRequest
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
            'employee_id' => 'required|integer|exists:employees,id',
            'contract_type' => 'required|string',
            'job_description' => 'required|string',
            'gross_salary' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric',
            'pf_from_employee' => 'nullable|numeric',
            'pf_from_company' => 'nullable|numeric',
            'gratuity' => 'nullable|numeric',
            'cit_percentage' => 'nullable|numeric',
            'cit_amount' => 'nullable|numeric',
            'ssf_amount' => 'nullable|numeric',
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
