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
            'country' => ['required'],
            'street_line1' => ['required'],
            'street_line2' => ['nullable'],
            'city' => ['required'],
            'province' => ['required'],
            'state' => ['required'],
            'postal_code' => ['required'],
        ];
    }
}
