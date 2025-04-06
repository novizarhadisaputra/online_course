<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            "id" => ["required", "exists:users,id"],
            "name" => ["required"],
            "description" => ["nullable", "string"],
            "email" => ["required", "unique:users,email,$this->id,id"],
            "phone" => ["required", "min_digits:13"],
            "password" => ["required"],
            "gender" => ["required", "mimes:male,female"],
        ];
    }
}
