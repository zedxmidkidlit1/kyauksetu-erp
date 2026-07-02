<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'admission_batch_id',
    'applicant_id',
    'academic_year_id',
    'program_id',
    'major_id',
    'application_no',
    'applied_at',
    'application_status',
    'student_profile_id',
    'converted_by',
    'converted_at',
    'remarks',
])]
class AdmissionApplication extends Model
{
    use LogsActivity;

    protected $attributes = [
        'application_status' => 'draft',
    ];

    public function admissionBatch(): BelongsTo
    {
        return $this->belongsTo(AdmissionBatch::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }

    public function admissionDocuments(): HasMany
    {
        return $this->hasMany(AdmissionDocument::class);
    }

    public function admissionDecision(): HasOne
    {
        return $this->hasOne(AdmissionDecision::class);
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function convertedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_by');
    }

    public function isAcceptedForConversion(): bool
    {
        return $this->application_status === 'accepted'
            || $this->admissionDecision?->decision_status === 'accepted';
    }

    public function isConverted(): bool
    {
        return $this->student_profile_id !== null || $this->converted_at !== null;
    }

    public function convertToStudent(User $convertedBy): StudentProfile
    {
        return DB::transaction(function () use ($convertedBy): StudentProfile {
            $application = self::query()
                ->with(['academicYear', 'admissionBatch.academicYear', 'admissionDecision', 'applicant.user', 'major'])
                ->lockForUpdate()
                ->findOrFail($this->getKey());

            if ($application->isConverted()) {
                throw new RuntimeException('This application has already been converted.');
            }

            if (! $application->isAcceptedForConversion()) {
                throw new RuntimeException('Only accepted applications can be converted.');
            }

            $applicant = $application->applicant;
            $studentUser = $applicant->user;

            if (! $studentUser && $applicant->email) {
                $studentUser = User::query()
                    ->where('email', $applicant->email)
                    ->first();

                if ($studentUser) {
                    $applicant->update(['user_id' => $studentUser->id]);
                }
            }

            if (! $studentUser) {
                throw new RuntimeException('The applicant must have a linked user before conversion.');
            }

            if ($studentUser->studentProfile()->exists()) {
                throw new RuntimeException('The linked user already has a student profile.');
            }

            $academicYearId = $application->academic_year_id ?? $application->admissionBatch?->academic_year_id;
            $programId = $application->program_id ?? $application->admissionBatch?->program_id;
            $majorId = $application->major_id;
            $academicYear = $application->academicYear ?? $application->admissionBatch?->academicYear;

            if (! $academicYearId || ! $programId || ! $majorId) {
                throw new RuntimeException('Academic year, program, and major are required before conversion.');
            }

            Role::firstOrCreate([
                'name' => 'student',
                'guard_name' => 'web',
            ]);

            $studentUser->assignRole('student');

            $convertedAt = now();
            $studentProfile = StudentProfile::create([
                'user_id' => $studentUser->id,
                'student_no' => $this->newStudentNumber($application),
                'institutional_email' => $studentUser->email,
                'first_name' => $applicant->first_name,
                'last_name' => $applicant->last_name,
                'date_of_birth' => $applicant->date_of_birth,
                'phone' => $applicant->phone,
                'department_id' => $application->major?->department_id,
                'program_id' => $programId,
                'major_id' => $majorId,
                'academic_year_id' => $academicYearId,
                'admission_year' => $academicYear?->start_date?->year ?? (int) $convertedAt->format('Y'),
                'status' => 'active',
                'enrolled_at' => $convertedAt->toDateString(),
            ]);

            StudentEnrollment::create([
                'student_profile_id' => $studentProfile->id,
                'academic_year_id' => $academicYearId,
                'program_id' => $programId,
                'major_id' => $majorId,
                'year_level' => 1,
                'status' => 'active',
                'enrolled_at' => $convertedAt->toDateString(),
                'remarks' => "Created from admission application {$application->application_no}.",
            ]);

            $application->update([
                'student_profile_id' => $studentProfile->id,
                'converted_by' => $convertedBy->id,
                'converted_at' => $convertedAt,
            ]);

            return $studentProfile;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('admissions')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    private function newStudentNumber(self $application): string
    {
        $academicYear = $application->academicYear ?? $application->admissionBatch?->academicYear;
        $year = $academicYear?->start_date?->format('Y') ?? now()->format('Y');
        $baseNumber = 'STU-'.$year.'-'.str_pad((string) $application->id, 6, '0', STR_PAD_LEFT);
        $number = $baseNumber;
        $suffix = 1;

        while (StudentProfile::query()->where('student_no', $number)->exists()) {
            $number = $baseNumber.'-'.$suffix;
            $suffix++;
        }

        return $number;
    }
}
