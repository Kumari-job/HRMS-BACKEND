<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mobile' => 'required',
            'address' => 'required',
            'gender' => 'required',
            'date_of_birth' => 'required_without:date_of_birth_nepali|date',
            'date_of_birth_nepali' => 'required_without:date_of_birth|date',
            'citizenship_number' => 'required',
            'citizenship_front_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'citizenship_back_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_number' => 'nullable',
        ];
    }
}
