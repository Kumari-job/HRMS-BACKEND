<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeExperienceRequest extends FormRequest
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
            'designation' => 'required|string',
            'industry' => 'required|string',
            'job_level' => 'required|string',
            'company' => 'required|string',
            'experience_letter' => 'nullable|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'from_date' => 'required_if:from_date_nepali,null|date|before:to_date',
            'from_date_nepali' => 'required_if:from_date,null|date|before:to_date_nepali',
            'to_date' => 'required_if:to_date_nepali,null|date',
            'to_date_nepali' => 'required_if:to_date,null|date',
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
