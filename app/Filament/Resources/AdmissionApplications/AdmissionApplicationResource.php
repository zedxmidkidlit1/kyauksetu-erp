<?php

namespace App\Filament\Resources\AdmissionApplications;

use App\Filament\Resources\AdmissionApplications\Pages\CreateAdmissionApplication;
use App\Filament\Resources\AdmissionApplications\Pages\EditAdmissionApplication;
use App\Filament\Resources\AdmissionApplications\Pages\ListAdmissionApplications;
use App\Filament\Resources\AdmissionApplications\Schemas\AdmissionApplicationForm;
use App\Filament\Resources\AdmissionApplications\Tables\AdmissionApplicationsTable;
use App\Models\AdmissionApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AdmissionApplicationResource extends Resource
{
    protected static ?string $model = AdmissionApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Applications';

    protected static ?string $modelLabel = 'Admission Application';

    protected static ?string $pluralModelLabel = 'Admission Applications';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return AdmissionApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionApplicationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'academicYear',
                'admissionBatch',
                'admissionDecision',
                'applicant',
                'major',
                'program',
                'studentProfile',
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
            'index' => ListAdmissionApplications::route('/'),
            'create' => CreateAdmissionApplication::route('/create'),
            'edit' => EditAdmissionApplication::route('/{record}/edit'),
        ];
    }
}
