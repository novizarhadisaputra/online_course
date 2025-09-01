<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TranscriptRequest extends FormRequest
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
            'student_id' => 'sometimes|uuid|exists:users,id',
            'semester' => 'sometimes|integer|min:1|max:20',
            'academic_year' => 'sometimes|integer|min:2020|max:' . (date('Y') + 5),
            'is_graduated' => 'sometimes|boolean',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1'
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
            'student_id.uuid' => 'Student ID must be a valid UUID format.',
            'student_id.exists' => 'The specified student does not exist.',
            'semester.integer' => 'Semester must be a valid integer.',
            'semester.min' => 'Semester must be at least 1.',
            'semester.max' => 'Semester cannot exceed 20.',
            'academic_year.integer' => 'Academic year must be a valid integer.',
            'academic_year.min' => 'Academic year must be at least 2020.',
            'academic_year.max' => 'Academic year cannot exceed ' . (date('Y') + 5) . '.',
            'is_graduated.boolean' => 'Graduation status must be true or false.',
            'per_page.integer' => 'Per page must be a valid integer.',
            'per_page.min' => 'Per page must be at least 1.',
            'per_page.max' => 'Per page cannot exceed 100.',
            'page.integer' => 'Page must be a valid integer.',
            'page.min' => 'Page must be at least 1.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'Student ID',
            'semester' => 'Semester',
            'academic_year' => 'Academic Year',
            'is_graduated' => 'Graduation Status',
            'per_page' => 'Items Per Page',
            'page' => 'Page Number'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 422)
        );
    }
}