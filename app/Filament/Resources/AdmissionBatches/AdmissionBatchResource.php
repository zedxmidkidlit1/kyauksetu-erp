<?php

namespace App\Filament\Resources\AdmissionBatches;

use App\Filament\Resources\AdmissionBatches\Pages\CreateAdmissionBatch;
use App\Filament\Resources\AdmissionBatches\Pages\EditAdmissionBatch;
use App\Filament\Resources\AdmissionBatches\Pages\ListAdmissionBatches;
use App\Filament\Resources\AdmissionBatches\Schemas\AdmissionBatchForm;
use App\Filament\Resources\AdmissionBatches\Tables\AdmissionBatchesTable;
use App\Models\AdmissionBatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AdmissionBatchResource extends Resource
{
    protected static ?string $model = AdmissionBatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Admissions';

    protected static ?string $navigationLabel = 'Admission Batches';

    protected static ?string $modelLabel = 'Admission Batch';

    protected static ?string $pluralModelLabel = 'Admission Batches';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return AdmissionBatchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdmissionBatchesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'academicYear',
                'program',
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
            'index' => ListAdmissionBatches::route('/'),
            'create' => CreateAdmissionBatch::route('/create'),
            'edit' => EditAdmissionBatch::route('/{record}/edit'),
        ];
    }
}
