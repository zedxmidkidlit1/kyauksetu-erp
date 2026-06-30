<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
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
