<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'citizenship_front' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'citizenship_back' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'driving_license' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'passport' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pan_card' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
