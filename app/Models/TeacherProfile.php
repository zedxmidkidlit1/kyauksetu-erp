<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'user_id',
    'staff_no',
    'institutional_email',
    'department_id',
    'position',
    'rank',
    'status',
])]
class TeacherProfile extends Model
{
    use LogsActivity;

    protected $attributes = [
        'status' => 'active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function teachingAssignments(): HasMany
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function staffEmployments(): HasMany
    {
        return $this->hasMany(StaffEmployment::class);
    }

    public function staffLeaveRequests(): HasMany
    {
        return $this->hasMany(StaffLeaveRequest::class);
    }

    public function staffDocuments(): HasMany
    {
        return $this->hasMany(StaffDocument::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('iam')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
