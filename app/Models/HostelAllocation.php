<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'student_profile_id',
    'hostel_id',
    'hostel_room_id',
    'hostel_bed_id',
    'allocated_at',
    'vacated_at',
    'allocation_status',
    'allocated_by',
    'vacated_by',
    'remarks',
])]
class HostelAllocation extends Model
{
    use LogsActivity;

    protected $attributes = [
        'allocation_status' => 'active',
    ];

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function hostelRoom(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class);
    }

    public function hostelBed(): BelongsTo
    {
        return $this->belongsTo(HostelBed::class);
    }

    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function vacatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vacated_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('hostel')
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
            'allocated_at' => 'datetime',
            'vacated_at' => 'datetime',
        ];
    }
}
