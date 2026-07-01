<?php

namespace App\Filament\Resources\AdmissionDecisions;

use App\Filament\Resources\AdmissionDecisions\Pages\CreateAdmissionDecision;
use App\Filament\Resources\AdmissionDecisions\Pages\EditAdmissionDecision;
use App\Filament\Resources\AdmissionDecisions\Pages\ListAdmissionDecisions;
use App\Filament\Resources\AdmissionDecisions\Schemas\AdmissionDecisionForm;
use App\Filament\Resources\AdmissionDecisions\Tables\AdmissionDecisionsTable;
use App\Models\AdmissionDecision;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AdmissionDecisionResource extends Resource
{
    protected static ?string $model = AdmissionDecision::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Decisions';

    protected static ?string $modelLabel = 'Admission Decision';

    protected static ?string $pluralModelLabel = 'Admission Decisions';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return AdmissionDecisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionDecisionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'admissionApplication',
                'admissionApplication.applicant',
                'decidedBy',
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
            'index' => ListAdmissionDecisions::route('/'),
            'create' => CreateAdmissionDecision::route('/create'),
            'edit' => EditAdmissionDecision::route('/{record}/edit'),
        ];
    }
}
