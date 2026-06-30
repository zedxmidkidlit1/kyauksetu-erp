<?php

namespace App\Filament\Resources\AssessmentComponents;

use App\Filament\Resources\AssessmentComponents\Pages\CreateAssessmentComponent;
use App\Filament\Resources\AssessmentComponents\Pages\EditAssessmentComponent;
use App\Filament\Resources\AssessmentComponents\Pages\ListAssessmentComponents;
use App\Filament\Resources\AssessmentComponents\RelationManagers\StudentMarksRelationManager;
use App\Filament\Resources\AssessmentComponents\Schemas\AssessmentComponentForm;
use App\Filament\Resources\AssessmentComponents\Tables\AssessmentComponentsTable;
use App\Models\AssessmentComponent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AssessmentComponentResource extends Resource
{
    protected static ?string $model = AssessmentComponent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Academic Operations';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AssessmentComponentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssessmentComponentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            StudentMarksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssessmentComponents::route('/'),
            'create' => CreateAssessmentComponent::route('/create'),
            'edit' => EditAssessmentComponent::route('/{record}/edit'),
        ];
    }
}
