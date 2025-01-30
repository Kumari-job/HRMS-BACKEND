<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'attendance'=>'required|array',
            'attendance.*.employee_id' => 'required|integer|exists:employees,id',
            'attendance.*.status' => 'required|in:present,absent,late',
            'date' => 'required_without:date_nepali|date|before_or_equal:today',
            'date_nepali' => 'required_without:date|date',
        ];
    }
}
