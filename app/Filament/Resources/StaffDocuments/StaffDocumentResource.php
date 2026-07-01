<?php

namespace App\Filament\Resources\StaffDocuments;

use App\Filament\Resources\StaffDocuments\Pages\CreateStaffDocument;
use App\Filament\Resources\StaffDocuments\Pages\EditStaffDocument;
use App\Filament\Resources\StaffDocuments\Pages\ListStaffDocuments;
use App\Filament\Resources\StaffDocuments\Schemas\StaffDocumentForm;
use App\Filament\Resources\StaffDocuments\Tables\StaffDocumentsTable;
use App\Models\StaffDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StaffDocumentResource extends Resource
{
    protected static ?string $model = StaffDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'HR';

    protected static ?string $navigationLabel = 'Staff Documents';

    protected static ?string $modelLabel = 'Staff Document';

    protected static ?string $pluralModelLabel = 'Staff Documents';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return StaffDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StaffDocumentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
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
            'index' => ListStaffDocuments::route('/'),
            'create' => CreateStaffDocument::route('/create'),
            'edit' => EditStaffDocument::route('/{record}/edit'),
        ];
    }
}
