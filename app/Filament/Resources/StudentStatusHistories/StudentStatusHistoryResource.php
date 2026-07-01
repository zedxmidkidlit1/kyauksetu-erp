<?php

namespace App\Filament\Resources\StudentStatusHistories;

use App\Filament\Resources\StudentStatusHistories\Pages\CreateStudentStatusHistory;
use App\Filament\Resources\StudentStatusHistories\Pages\EditStudentStatusHistory;
use App\Filament\Resources\StudentStatusHistories\Pages\ListStudentStatusHistories;
use App\Filament\Resources\StudentStatusHistories\Schemas\StudentStatusHistoryForm;
use App\Filament\Resources\StudentStatusHistories\Tables\StudentStatusHistoriesTable;
use App\Models\StudentStatusHistory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StudentStatusHistoryResource extends Resource
{
    protected static ?string $model = StudentStatusHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowPath;

    protected static string|UnitEnum|null $navigationGroup = 'SIS';

    protected static ?string $navigationLabel = 'Status History';

    protected static ?string $modelLabel = 'Student Status History';

    protected static ?string $pluralModelLabel = 'Student Status Histories';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return StudentStatusHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentStatusHistoriesTable::configure($table);
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
            'index' => ListStudentStatusHistories::route('/'),
            'create' => CreateStudentStatusHistory::route('/create'),
            'edit' => EditStudentStatusHistory::route('/{record}/edit'),
        ];
    }
}
