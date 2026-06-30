<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'student_enrollment_id',
    'academic_year_id',
    'semester_id',
    'course_id',
    'grade_scale_id',
    'total_marks',
    'percentage',
    'grade',
    'grade_point',
    'result_status',
    'calculated_by',
    'calculated_at',
    'approved_by',
    'approved_at',
    'remarks',
])]
class StudentCourseResult extends Model
{
    use LogsActivity;

    protected $attributes = [
        'result_status' => 'draft',
    ];

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function gradeScale(): BelongsTo
    {
        return $this->belongsTo(GradeScale::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function resultBatchItems(): HasMany
    {
        return $this->hasMany(ResultBatchItem::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('academic')
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
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
            'grade_point' => 'decimal:2',
            'calculated_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }
}
