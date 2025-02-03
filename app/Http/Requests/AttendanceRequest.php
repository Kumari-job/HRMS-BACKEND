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
            'employee_id' => 'required|integer|exists:users,id',
            'is_present' => 'required|boolean',
            'date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'punch_in_at' => 'required|date',
            'punch_out_at' => 'nullable|date',

        ];
    }
}
