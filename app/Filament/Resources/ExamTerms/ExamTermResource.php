<?php

namespace App\Filament\Resources\ExamTerms;

use App\Filament\Resources\ExamTerms\Pages\CreateExamTerm;
use App\Filament\Resources\ExamTerms\Pages\EditExamTerm;
use App\Filament\Resources\ExamTerms\Pages\ListExamTerms;
use App\Filament\Resources\ExamTerms\RelationManagers\SchedulesRelationManager;
use App\Filament\Resources\ExamTerms\Schemas\ExamTermForm;
use App\Filament\Resources\ExamTerms\Tables\ExamTermsTable;
use App\Models\ExamTerm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExamTermResource extends Resource
{
    protected static ?string $model = ExamTerm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Academic Operations';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExamTermForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamTermsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExamTerms::route('/'),
            'create' => CreateExamTerm::route('/create'),
            'edit' => EditExamTerm::route('/{record}/edit'),
        ];
    }
}
