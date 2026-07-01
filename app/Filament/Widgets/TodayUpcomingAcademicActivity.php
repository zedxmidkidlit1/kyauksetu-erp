<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use App\Models\ExamSchedule;
use App\Models\TimetableSlot;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class TodayUpcomingAcademicActivity extends StatsOverviewWidget
{
    protected ?string $heading = 'Today / Upcoming';

    protected ?string $description = 'Read-only academic activity snapshot';

    protected static ?int $sort = 70;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $today = today();
        $now = now();
        $dayOfWeek = strtolower($today->format('l'));

        return [
            Stat::make('Today\'s timetable slots', Number::format(
                TimetableSlot::query()
                    ->where('day_of_week', $dayOfWeek)
                    ->where('status', 'scheduled')
                    ->count(),
            ))
                ->description('Scheduled slots for '.$today->format('D, M j')),
            Stat::make('Upcoming exams', Number::format(
                ExamSchedule::query()
                    ->whereDate('exam_date', '>=', $today)
                    ->where('status', '!=', 'cancelled')
                    ->count(),
            ))
                ->description('Exam schedules from today onward'),
            Stat::make('Active announcements', Number::format(
                Announcement::query()
                    ->where('status', 'published')
                    ->where(function (Builder $query) use ($now): void {
                        $query
                            ->whereNull('publish_at')
                            ->orWhere('publish_at', '<=', $now);
                    })
                    ->where(function (Builder $query) use ($now): void {
                        $query
                            ->whereNull('expires_at')
                            ->orWhere('expires_at', '>=', $now);
                    })
                    ->count(),
            ))
                ->description('Published and not expired'),
        ];
    }
}
