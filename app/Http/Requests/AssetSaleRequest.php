<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AssetSaleRequest extends FormRequest
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
        $saleId = $this->route('id');
        return [
            'asset_id' => [
                'required',
                'string',
                Rule::unique('asset_sales', 'asset_id')->ignore($saleId)
            ],
            'price' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'sold_to' => 'required|string',
            'sold_by' => 'required|integer|exists:users,id',
        ];
    }
    public function messages(): array
    {
        return [
            'asset_id.unique' => 'Asset has already been sold.',
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
