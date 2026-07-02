<?php

namespace App\Http\Requests\Teacher;

use App\Models\AssessmentComponent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateStudentMarksRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'records' => ['required', 'array'],
            'records.*.marks_obtained' => ['nullable', 'numeric', 'min:0'],
            'records.*.remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $component = $this->route('component');

                if (! $component instanceof AssessmentComponent) {
                    return;
                }

                foreach ((array) $this->input('records', []) as $recordKey => $record) {
                    $marks = $record['marks_obtained'] ?? null;

                    if ($marks !== null && $marks !== '' && (float) $marks > (float) $component->max_marks) {
                        $validator->errors()->add(
                            "records.{$recordKey}.marks_obtained",
                            'Marks cannot exceed the assessment component maximum.',
                        );
                    }
                }
            },
        ];
    }
}
