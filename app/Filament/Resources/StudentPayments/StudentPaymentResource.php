<?php

namespace App\Filament\Resources\StudentPayments;

use App\Filament\Resources\StudentPayments\Pages\CreateStudentPayment;
use App\Filament\Resources\StudentPayments\Pages\EditStudentPayment;
use App\Filament\Resources\StudentPayments\Pages\ListStudentPayments;
use App\Filament\Resources\StudentPayments\Schemas\StudentPaymentForm;
use App\Filament\Resources\StudentPayments\Tables\StudentPaymentsTable;
use App\Models\StudentPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class StudentPaymentResource extends Resource
{
    protected static ?string $model = StudentPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static string|UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Student Payments';

    protected static ?string $modelLabel = 'Student Payment';

    protected static ?string $pluralModelLabel = 'Student Payments';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return StudentPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentPaymentsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'receivedBy',
                'studentFee.feeType',
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
            'index' => ListStudentPayments::route('/'),
            'create' => CreateStudentPayment::route('/create'),
            'edit' => EditStudentPayment::route('/{record}/edit'),
        ];
    }
}
