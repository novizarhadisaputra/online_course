<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'label' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['nullable'],
            'email' => ['required', 'email'],
            'phone' => ['required'],
            'street_line1' => ['required'],
            'street_line2' => ['nullable'],
            'country' => ['required'],
            'province_id' => ['required', 'exists:provinces,id'],
            'regency_id' => ['required', 'exists:regencies,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'village_id' => ['required', 'exists:villages,id'],
            'postal_code' => ['required'],
            'status' => ['required', 'boolean'],
            'note' => ['nullable']
        ];
    }
}
