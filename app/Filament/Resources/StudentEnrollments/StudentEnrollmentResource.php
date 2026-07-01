<?php

namespace App\Filament\Resources\StudentEnrollments;

use App\Filament\Resources\StudentEnrollments\Pages\CreateStudentEnrollment;
use App\Filament\Resources\StudentEnrollments\Pages\EditStudentEnrollment;
use App\Filament\Resources\StudentEnrollments\Pages\ListStudentEnrollments;
use App\Filament\Resources\StudentEnrollments\Schemas\StudentEnrollmentForm;
use App\Filament\Resources\StudentEnrollments\Tables\StudentEnrollmentsTable;
use App\Models\StudentEnrollment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StudentEnrollmentResource extends Resource
{
    protected static ?string $model = StudentEnrollment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'SIS';

    protected static ?string $navigationLabel = 'Student Enrollments';

    protected static ?string $modelLabel = 'Student Enrollment';

    protected static ?string $pluralModelLabel = 'Student Enrollments';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return StudentEnrollmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentEnrollmentsTable::configure($table);
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
            'index' => ListStudentEnrollments::route('/'),
            'create' => CreateStudentEnrollment::route('/create'),
            'edit' => EditStudentEnrollment::route('/{record}/edit'),
        ];
    }
}
