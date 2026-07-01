<?php

namespace App\Filament\Resources\StudentFees;

use App\Filament\Resources\StudentFees\Pages\CreateStudentFee;
use App\Filament\Resources\StudentFees\Pages\EditStudentFee;
use App\Filament\Resources\StudentFees\Pages\ListStudentFees;
use App\Filament\Resources\StudentFees\Schemas\StudentFeeForm;
use App\Filament\Resources\StudentFees\Tables\StudentFeesTable;
use App\Models\StudentFee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentFeeResource extends Resource
{
    protected static ?string $model = StudentFee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Student Fees';

    protected static ?string $modelLabel = 'Student Fee';

    protected static ?string $pluralModelLabel = 'Student Fees';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return StudentFeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentFeesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'academicYear',
                'feeType',
                'semester',
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
            'index' => ListStudentFees::route('/'),
            'create' => CreateStudentFee::route('/create'),
            'edit' => EditStudentFee::route('/{record}/edit'),
        ];
    }
}
