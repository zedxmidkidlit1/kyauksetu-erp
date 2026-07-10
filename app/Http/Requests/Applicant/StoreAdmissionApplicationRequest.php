<?php

namespace App\Http\Requests\Applicant;

use App\Models\AdmissionBatch;
use App\Models\Major;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'program_id' => ['nullable', Rule::exists('programs', 'id')->where('status', 'active')],
            'major_id' => ['nullable', Rule::exists('majors', 'id')->where('status', 'active')],
            'remarks' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $batch = AdmissionBatch::query()->find($this->integer('admission_batch_id'));

                if (! $batch instanceof AdmissionBatch) {
                    return;
                }

                if (! $batch->isAcceptingApplications()) {
                    $validator->errors()->add('admission_batch_id', 'This admission batch is not currently accepting applications.');
                }

                $submittedProgramId = $this->filled('program_id') ? $this->integer('program_id') : null;

                if ($batch->program_id && $submittedProgramId && $batch->program_id !== $submittedProgramId) {
                    $validator->errors()->add('program_id', 'The selected program does not match this admission batch.');
                }

                $major = $this->filled('major_id')
                    ? Major::query()->find($this->integer('major_id'))
                    : null;
                $programId = $batch->program_id ?? $submittedProgramId ?? $major?->program_id;

                if ($major instanceof Major && $major->program_id !== $programId) {
                    $validator->errors()->add('major_id', 'The selected major does not belong to the selected program.');
                }
            },
        ];
    }
}
