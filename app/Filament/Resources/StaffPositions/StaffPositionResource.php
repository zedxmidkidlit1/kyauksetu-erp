<?php

namespace App\Filament\Resources\StaffPositions;

use App\Filament\Resources\StaffPositions\Pages\CreateStaffPosition;
use App\Filament\Resources\StaffPositions\Pages\EditStaffPosition;
use App\Filament\Resources\StaffPositions\Pages\ListStaffPositions;
use App\Filament\Resources\StaffPositions\Schemas\StaffPositionForm;
use App\Filament\Resources\StaffPositions\Tables\StaffPositionsTable;
use App\Models\StaffPosition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StaffPositionResource extends Resource
{
    protected static ?string $model = StaffPosition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static string|UnitEnum|null $navigationGroup = 'HR';

    protected static ?string $navigationLabel = 'Staff Positions';

    protected static ?string $modelLabel = 'Staff Position';

    protected static ?string $pluralModelLabel = 'Staff Positions';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return StaffPositionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffPositionsTable::configure($table);
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
            'index' => ListStaffPositions::route('/'),
            'create' => CreateStaffPosition::route('/create'),
            'edit' => EditStaffPosition::route('/{record}/edit'),
        ];
    }
}
