<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable([
    'academic_year_id',
    'program_id',
    'name',
    'code',
    'description',
    'opens_at',
    'closes_at',
    'capacity',
    'status',
    'remarks',
])]
class AdmissionBatch extends Model
{
    use LogsActivity;

    protected $attributes = [
        'status' => 'draft',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function admissionApplications(): HasMany
    {
        return $this->hasMany(AdmissionApplication::class);
    }

    public function scopeAcceptingApplications(Builder $query, ?CarbonInterface $date = null): Builder
    {
        $date ??= today();

        return $query
            ->where('status', 'open')
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('program_id')
                    ->orWhereHas('program', fn (Builder $query) => $query->where('status', 'active'));
            })
            ->where(function (Builder $query) use ($date): void {
                $query
                    ->whereNull('opens_at')
                    ->orWhereDate('opens_at', '<=', $date);
            })
            ->where(function (Builder $query) use ($date): void {
                $query
                    ->whereNull('closes_at')
                    ->orWhereDate('closes_at', '>=', $date);
            });
    }

    public function isAcceptingApplications(?CarbonInterface $date = null): bool
    {
        $date ??= today();

        return $this->status === 'open'
            && ($this->program_id === null || $this->program()->where('status', 'active')->exists())
            && ($this->opens_at === null || $this->opens_at->lte($date))
            && ($this->closes_at === null || $this->closes_at->gte($date));
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
            'opens_at' => 'date',
            'closes_at' => 'date',
            'capacity' => 'integer',
        ];
    }
}
