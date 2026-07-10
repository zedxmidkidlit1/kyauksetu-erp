<?php

namespace App\Filament\Widgets;

use App\Models\Announcement;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class CommunicationOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Communication';

    protected static ?int $sort = 60;

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->can('announcements.view');
    }

    protected function getStats(): array
    {
        $counts = Announcement::query()
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            Stat::make('Published announcements', Number::format((int) ($counts['published'] ?? 0)))
                ->description('Total published'),
            Stat::make('Draft', Number::format((int) ($counts['draft'] ?? 0)))
                ->color('gray'),
            Stat::make('Scheduled', Number::format((int) ($counts['scheduled'] ?? 0)))
                ->color('info'),
            Stat::make('Archived', Number::format((int) ($counts['archived'] ?? 0)))
                ->color('gray'),
            Stat::make('Cancelled', Number::format((int) ($counts['cancelled'] ?? 0)))
                ->color('danger'),
        ];
    }
}
