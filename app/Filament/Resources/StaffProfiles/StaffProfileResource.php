<?php

namespace App\Filament\Resources\StaffProfiles;

use App\Filament\Resources\StaffProfiles\Pages\CreateStaffProfile;
use App\Filament\Resources\StaffProfiles\Pages\EditStaffProfile;
use App\Filament\Resources\StaffProfiles\Pages\ListStaffProfiles;
use App\Filament\Resources\StaffProfiles\Schemas\StaffProfileForm;
use App\Filament\Resources\StaffProfiles\Tables\StaffProfilesTable;
use App\Models\StaffProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StaffProfileResource extends Resource
{
    protected static ?string $model = StaffProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Profiles';

    public static function form(Schema $schema): Schema
    {
        return StaffProfileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffProfilesTable::configure($table);
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
            'index' => ListStaffProfiles::route('/'),
            'create' => CreateStaffProfile::route('/create'),
            'edit' => EditStaffProfile::route('/{record}/edit'),
        ];
    }
}
