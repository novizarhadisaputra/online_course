<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Password;
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
            "first_name" => ["required", "string"],
            "last_name" => ["nullable", "string"],
            "description" => ["nullable", "string"],
            "email" => ["nullable", "unique:users,email,$this->id,id"],
            "phone" => [
                "nullable",
                "min:10",
                "unique:users,phone,$this->id,id"
            ],
            "password" => [
                "nullable",
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            "gender" => ["nullable", "in:male,female"],
        ];
    }
}
