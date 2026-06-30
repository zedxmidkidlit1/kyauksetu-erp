<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'result_batch_id',
    'student_course_result_id',
    'student_enrollment_id',
    'course_id',
    'status',
    'remarks',
])]
class ResultBatchItem extends Model
{
    use LogsActivity;

    protected $attributes = [
        'status' => 'included',
    ];

    public function resultBatch(): BelongsTo
    {
        return $this->belongsTo(ResultBatch::class);
    }

    public function studentCourseResult(): BelongsTo
    {
        return $this->belongsTo(StudentCourseResult::class);
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('academic')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
