<?php

namespace App\Filament\Widgets;

use App\Models\StudentProfile;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StudentStatusOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Identity / People';

    protected ?string $description = 'Student profiles by status';

    protected static ?int $sort = 20;

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User && $user->can('students.view');
    }

    protected function getStats(): array
    {
        $counts = StudentProfile::query()
            ->select('status')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            Stat::make('Active students', Number::format((int) ($counts['active'] ?? 0)))
                ->color('success'),
            Stat::make('Inactive students', Number::format((int) ($counts['inactive'] ?? 0)))
                ->color('gray'),
        ];
    }
}
