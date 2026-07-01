<?php

namespace App\Filament\Resources\StudentCourseResults;

use App\Filament\Resources\StudentCourseResults\Pages\CreateStudentCourseResult;
use App\Filament\Resources\StudentCourseResults\Pages\EditStudentCourseResult;
use App\Filament\Resources\StudentCourseResults\Pages\ListStudentCourseResults;
use App\Filament\Resources\StudentCourseResults\Schemas\StudentCourseResultForm;
use App\Filament\Resources\StudentCourseResults\Tables\StudentCourseResultsTable;
use App\Models\StudentCourseResult;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StudentCourseResultResource extends Resource
{
    protected static ?string $model = StudentCourseResult::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Exams & Results';

    protected static ?string $navigationLabel = 'Course Results';

    protected static ?string $modelLabel = 'Student Course Result';

    protected static ?string $pluralModelLabel = 'Student Course Results';

    protected static ?int $navigationSort = 40;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return StudentCourseResultForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentCourseResultsTable::configure($table);
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
            'index' => ListStudentCourseResults::route('/'),
            'create' => CreateStudentCourseResult::route('/create'),
            'edit' => EditStudentCourseResult::route('/{record}/edit'),
        ];
    }
}
