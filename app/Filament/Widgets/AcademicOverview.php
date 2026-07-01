<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Department;
use App\Models\StudentEnrollment;
use App\Models\Timetable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AcademicOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Academic';

    protected static ?int $sort = 30;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Departments', Number::format(Department::query()->count())),
            Stat::make('Active enrollments', Number::format(
                StudentEnrollment::query()->where('status', 'active')->count(),
            )),
            Stat::make('Courses', Number::format(Course::query()->count())),
            Stat::make('Timetables', Number::format(Timetable::query()->count())),
        ];
    }
}
