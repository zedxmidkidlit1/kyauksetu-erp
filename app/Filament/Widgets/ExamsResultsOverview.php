<?php

namespace App\Filament\Widgets;

use App\Models\ExamSchedule;
use App\Models\ResultBatch;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ExamsResultsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Exams / Results';

    protected static ?int $sort = 50;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $batchCounts = ResultBatch::query()
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            Stat::make('Exam schedules', Number::format(ExamSchedule::query()->count()))
                ->description('Total scheduled exam records'),
            Stat::make('Result batches', Number::format(ResultBatch::query()->count()))
                ->description('Total batches'),
            Stat::make('Draft batches', Number::format((int) ($batchCounts['draft'] ?? 0)))
                ->color('gray'),
            Stat::make('Prepared batches', Number::format((int) ($batchCounts['prepared'] ?? 0)))
                ->color('info'),
            Stat::make('Reviewed batches', Number::format((int) ($batchCounts['reviewed'] ?? 0)))
                ->color('warning'),
            Stat::make('Approved batches', Number::format((int) ($batchCounts['approved'] ?? 0)))
                ->color('success'),
            Stat::make('Published batches', Number::format((int) ($batchCounts['published'] ?? 0)))
                ->color('success'),
            Stat::make('Locked batches', Number::format((int) ($batchCounts['locked'] ?? 0)))
                ->color('gray'),
            Stat::make('Cancelled batches', Number::format((int) ($batchCounts['cancelled'] ?? 0)))
                ->color('danger'),
        ];
    }
}
