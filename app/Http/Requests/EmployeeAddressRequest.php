<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeAddressRequest extends FormRequest
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
            'p_country' => 'required|string',
            'p_district' => 'required|string',
            'p_vdc_or_municipality' => 'required|string',
            'p_street' => 'required|string',
            'p_ward' => 'required|string',
            'p_state' => 'required|string',
            'p_house_number' => 'nullable|string',
            'p_zip_code' => 'required|string',
            't_country' => 'required|string',
            't_district' => 'required|string',
            't_vdc_or_municipality' => 'required|string',
            't_street' => 'required|string',
            't_ward' => 'required|string',
            't_state' => 'required|string',
            't_house_number' => 'nullable|string',
            't_zip_code' => 'required|string',
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
