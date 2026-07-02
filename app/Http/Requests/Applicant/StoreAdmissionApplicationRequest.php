<?php

namespace App\Http\Requests\Applicant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdmissionApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('applicant') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admission_batch_id' => ['required', 'exists:admission_batches,id'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'major_id' => ['nullable', 'exists:majors,id'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
