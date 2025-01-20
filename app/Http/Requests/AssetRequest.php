<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
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
            'asset_category_id' => 'required|exists:asset_categories,id',
            'vendor_id' => 'required|exists:vendors,id',
            'code' => [
                'required',
                'string',
            ],
            'title' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'cost' => 'required|numeric',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string',
            'purchased_at' => 'required_if:purchased_at_nepali,null|date',
            'purchased_at_nepali' => 'required_if:purchased_at,null|date',
            'warranty_end_at' => 'nullable|date',
            'warranty_end_at_nepali' => 'nullable|date',
            'warranty_image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'guarantee_end_at' => 'nullable|date',
            'guarantee_end_at_nepali' => 'nullable|date',
            'guarantee_image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:new,used,disposed,damaged,maintenance',
            'depreciation_type' => 'nullable|string',
            'depreciation_rate' => 'nullable|numeric',
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
