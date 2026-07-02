<?php

namespace App\Filament\Resources\KaiChatSessions;

use App\Filament\Resources\KaiChatSessions\Pages\ListKaiChatSessions;
use App\Filament\Resources\KaiChatSessions\Pages\ViewKaiChatSession;
use App\Filament\Resources\KaiChatSessions\Schemas\KaiChatSessionInfolist;
use App\Filament\Resources\KaiChatSessions\Tables\KaiChatSessionsTable;
use App\Models\KaiChatSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class KaiChatSessionResource extends Resource
{
    protected static ?string $model = KaiChatSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'KAI';

    protected static ?string $navigationLabel = 'Chat Sessions';

    protected static ?string $modelLabel = 'KAI Chat Session';

    protected static ?string $pluralModelLabel = 'KAI Chat Sessions';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    public static function infolist(Schema $schema): Schema
    {
        return KaiChatSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KaiChatSessionsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->withCount('messages');
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
            'index' => ListKaiChatSessions::route('/'),
            'view' => ViewKaiChatSession::route('/{record}'),
        ];
    }
}
