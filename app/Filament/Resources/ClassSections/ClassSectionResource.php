<?php

namespace App\Filament\Resources\ClassSections;

use App\Filament\Resources\ClassSections\Pages\CreateClassSection;
use App\Filament\Resources\ClassSections\Pages\EditClassSection;
use App\Filament\Resources\ClassSections\Pages\ListClassSections;
use App\Filament\Resources\ClassSections\Schemas\ClassSectionForm;
use App\Filament\Resources\ClassSections\Tables\ClassSectionsTable;
use App\Models\ClassSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClassSectionResource extends Resource
{
    protected static ?string $model = ClassSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Academic Structure';

    protected static ?string $navigationLabel = 'Class Sections';

    protected static ?string $modelLabel = 'Class Section';

    protected static ?string $pluralModelLabel = 'Class Sections';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return ClassSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClassSectionsTable::configure($table);
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
            'index' => ListClassSections::route('/'),
            'create' => CreateClassSection::route('/create'),
            'edit' => EditClassSection::route('/{record}/edit'),
        ];
    }
}
