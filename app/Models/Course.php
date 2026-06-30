<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'name',
    'code',
    'description',
    'credit_hours',
    'lecture_hours',
    'tutorial_hours',
    'practical_hours',
    'status',
])]
class Course extends Model
{
    use LogsActivity;

    protected $attributes = [
        'status' => 'active',
    ];

    public function curriculumCourses(): HasMany
    {
        return $this->hasMany(CurriculumCourse::class);
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
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
            'credit_hours' => 'integer',
            'lecture_hours' => 'integer',
            'tutorial_hours' => 'integer',
            'practical_hours' => 'integer',
        ];
    }
}
