<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceSession;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AttendanceOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Attendance';

    protected static ?int $sort = 40;

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->can('attendance_sessions.view');
    }

    protected function getStats(): array
    {
        $counts = AttendanceSession::query()
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            Stat::make('Attendance sessions', Number::format(AttendanceSession::query()->count()))
                ->description('Total sessions'),
            Stat::make('Draft', Number::format((int) ($counts['draft'] ?? 0)))
                ->color('gray'),
            Stat::make('Open', Number::format((int) ($counts['open'] ?? 0)))
                ->color('info'),
            Stat::make('Submitted', Number::format((int) ($counts['submitted'] ?? 0)))
                ->color('warning'),
            Stat::make('Approved', Number::format((int) ($counts['approved'] ?? 0)))
                ->color('success'),
            Stat::make('Cancelled', Number::format((int) ($counts['cancelled'] ?? 0)))
                ->color('danger'),
        ];
    }
}
