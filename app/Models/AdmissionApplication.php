<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        ];
    }
}
