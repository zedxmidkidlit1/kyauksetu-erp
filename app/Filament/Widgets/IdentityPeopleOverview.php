<?php

namespace App\Filament\Widgets;

use App\Models\StaffProfile;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class IdentityPeopleOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Identity / People';

    protected static ?int $sort = 10;

    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user instanceof User
            && $user->can('users.view')
            && $user->can('students.view')
            && $user->can('teachers.view');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Users', Number::format(User::query()->count()))
                ->description('Total login accounts'),
            Stat::make('Students', Number::format(StudentProfile::query()->count()))
                ->description('Student profiles'),
            Stat::make('Teachers', Number::format(TeacherProfile::query()->count()))
                ->description('Teacher profiles'),
            Stat::make('Staff', Number::format(StaffProfile::query()->count()))
                ->description('Staff profiles'),
        ];
    }
}
