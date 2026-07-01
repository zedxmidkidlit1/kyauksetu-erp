<?php

namespace App\Filament\Resources\ResultBatches;

use App\Filament\Resources\ResultBatches\Pages\CreateResultBatch;
use App\Filament\Resources\ResultBatches\Pages\EditResultBatch;
use App\Filament\Resources\ResultBatches\Pages\ListResultBatches;
use App\Filament\Resources\ResultBatches\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\ResultBatches\Schemas\ResultBatchForm;
use App\Filament\Resources\ResultBatches\Tables\ResultBatchesTable;
use App\Models\ResultBatch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ResultBatchResource extends Resource
{
    protected static ?string $model = ResultBatch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Exams & Results';

    protected static ?string $navigationLabel = 'Result Batches';

    protected static ?string $modelLabel = 'Result Batch';

    protected static ?string $pluralModelLabel = 'Result Batches';

    protected static ?int $navigationSort = 50;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ResultBatchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResultBatchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResultBatches::route('/'),
            'create' => CreateResultBatch::route('/create'),
            'edit' => EditResultBatch::route('/{record}/edit'),
        ];
    }
}
