<?php

namespace App\Filament\Resources\StaffEmployments;

use App\Filament\Resources\StaffEmployments\Pages\CreateStaffEmployment;
use App\Filament\Resources\StaffEmployments\Pages\EditStaffEmployment;
use App\Filament\Resources\StaffEmployments\Pages\ListStaffEmployments;
use App\Filament\Resources\StaffEmployments\Schemas\StaffEmploymentForm;
use App\Filament\Resources\StaffEmployments\Tables\StaffEmploymentsTable;
use App\Models\StaffEmployment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StaffEmploymentResource extends Resource
{
    protected static ?string $model = StaffEmployment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Briefcase;

    protected static string|UnitEnum|null $navigationGroup = 'HR';

    protected static ?string $navigationLabel = 'Staff Employments';

    protected static ?string $modelLabel = 'Staff Employment';

    protected static ?string $pluralModelLabel = 'Staff Employments';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return StaffEmploymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffEmploymentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'department',
                'staffPosition',
                'staffProfile',
                'teacherProfile',
                'user',
            ]);
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
            'index' => ListStaffEmployments::route('/'),
            'create' => CreateStaffEmployment::route('/create'),
            'edit' => EditStaffEmployment::route('/{record}/edit'),
        ];
    }
}
