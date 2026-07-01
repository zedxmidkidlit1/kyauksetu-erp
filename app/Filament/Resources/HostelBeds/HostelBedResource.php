<?php

namespace App\Filament\Resources\HostelBeds;

use App\Filament\Resources\HostelBeds\Pages\CreateHostelBed;
use App\Filament\Resources\HostelBeds\Pages\EditHostelBed;
use App\Filament\Resources\HostelBeds\Pages\ListHostelBeds;
use App\Filament\Resources\HostelBeds\Schemas\HostelBedForm;
use App\Filament\Resources\HostelBeds\Tables\HostelBedsTable;
use App\Models\HostelBed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HostelBedResource extends Resource
{
    protected static ?string $model = HostelBed::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Hostel';

    protected static ?string $navigationLabel = 'Hostel Beds';

    protected static ?string $modelLabel = 'Hostel Bed';

    protected static ?string $pluralModelLabel = 'Hostel Beds';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return HostelBedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HostelBedsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('hostelRoom.hostel');
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
            'index' => ListHostelBeds::route('/'),
            'create' => CreateHostelBed::route('/create'),
            'edit' => EditHostelBed::route('/{record}/edit'),
        ];
    }
}
