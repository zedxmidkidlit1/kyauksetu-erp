<?php

namespace App\Filament\Resources\TeacherProfiles;

use App\Filament\Resources\TeacherProfiles\Pages\CreateTeacherProfile;
use App\Filament\Resources\TeacherProfiles\Pages\EditTeacherProfile;
use App\Filament\Resources\TeacherProfiles\Pages\ListTeacherProfiles;
use App\Filament\Resources\TeacherProfiles\Schemas\TeacherProfileForm;
use App\Filament\Resources\TeacherProfiles\Tables\TeacherProfilesTable;
use App\Models\TeacherProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TeacherProfileResource extends Resource
{
    protected static ?string $model = TeacherProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'People & Profiles';

    protected static ?string $navigationLabel = 'Teachers';

    protected static ?string $modelLabel = 'Teacher Profile';

    protected static ?string $pluralModelLabel = 'Teacher Profiles';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return TeacherProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeacherProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeacherProfiles::route('/'),
            'create' => CreateTeacherProfile::route('/create'),
            'edit' => EditTeacherProfile::route('/{record}/edit'),
        ];
    }
}
