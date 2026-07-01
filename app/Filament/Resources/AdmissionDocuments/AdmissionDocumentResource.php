<?php

namespace App\Filament\Resources\AdmissionDocuments;

use App\Filament\Resources\AdmissionDocuments\Pages\CreateAdmissionDocument;
use App\Filament\Resources\AdmissionDocuments\Pages\EditAdmissionDocument;
use App\Filament\Resources\AdmissionDocuments\Pages\ListAdmissionDocuments;
use App\Filament\Resources\AdmissionDocuments\Schemas\AdmissionDocumentForm;
use App\Filament\Resources\AdmissionDocuments\Tables\AdmissionDocumentsTable;
use App\Models\AdmissionDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AdmissionDocumentResource extends Resource
{
    protected static ?string $model = AdmissionDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Documents';

    protected static ?string $modelLabel = 'Admission Document';

    protected static ?string $pluralModelLabel = 'Admission Documents';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return AdmissionDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionDocumentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'admissionApplication',
                'applicant',
                'verifiedBy',
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
            'index' => ListAdmissionDocuments::route('/'),
            'create' => CreateAdmissionDocument::route('/create'),
            'edit' => EditAdmissionDocument::route('/{record}/edit'),
        ];
    }
}
