<?php

namespace App\Http\Requests\PrivateClass;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
            'id' => ['required', 'uuid', 'exists:private_classes,id'],
            'qty' => ['required', 'numeric', 'min:1'],
            'price_id' => ['required', 'exists:prices,id']
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id.required' => __('api.validation.required', ['attribute' => 'private class ID']),
            'id.uuid' => __('api.validation.uuid', ['attribute' => 'private class ID']),
            'id.exists' => __('api.validation.exists', ['attribute' => 'private class']),
            'qty.required' => __('api.validation.required', ['attribute' => 'quantity']),
            'qty.numeric' => __('api.validation.numeric', ['attribute' => 'quantity']),
            'qty.min' => __('api.validation.min', ['attribute' => 'quantity', 'min' => 1]),
            'price_id.required' => __('api.validation.required', ['attribute' => 'price ID']),
            'price_id.exists' => __('api.validation.exists', ['attribute' => 'price'])
        ];
    }
}