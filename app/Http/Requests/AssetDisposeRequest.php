<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AssetDisposeRequest extends FormRequest
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
        $disposeId = $this->route('id');
        return [
            'asset_id' => [
                'required',
                'string',
                Rule::unique('asset_dispose', 'asset_id')->ignore($disposeId)
            ],
            'details' => 'required|string',
            'disposed_at' => 'required_if:disposed_at,null|date',
            'disposed_at_nepali ' => 'required_if:disposed_at,null|date',
            'disposed_by' => 'required|string|exists:users,id',
        ];
    }
    public function messages(): array
    {
        return [
            'asset_id.unique' => 'Asset has already been disposed.',
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
