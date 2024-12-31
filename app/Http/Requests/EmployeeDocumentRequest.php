<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EmployeeDocumentRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'citizenship_front' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'citizenship_back' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'driving_license' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'passport' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_card' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
