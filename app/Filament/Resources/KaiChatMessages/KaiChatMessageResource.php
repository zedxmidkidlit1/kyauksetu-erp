<?php

namespace App\Filament\Resources\KaiChatMessages;

use App\Filament\Resources\KaiChatMessages\Pages\ListKaiChatMessages;
use App\Filament\Resources\KaiChatMessages\Pages\ViewKaiChatMessage;
use App\Filament\Resources\KaiChatMessages\Schemas\KaiChatMessageInfolist;
use App\Filament\Resources\KaiChatMessages\Tables\KaiChatMessagesTable;
use App\Models\KaiChatMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class KaiChatMessageResource extends Resource
{
    protected static ?string $model = KaiChatMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|UnitEnum|null $navigationGroup = 'KAI';

    protected static ?string $navigationLabel = 'Chat Messages';

    protected static ?string $modelLabel = 'KAI Chat Message';

    protected static ?string $pluralModelLabel = 'KAI Chat Messages';

    protected static ?int $navigationSort = 20;

    public static function infolist(Schema $schema): Schema
    {
        return KaiChatMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KaiChatMessagesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['session', 'user']);
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
            'index' => ListKaiChatMessages::route('/'),
            'view' => ViewKaiChatMessage::route('/{record}'),
        ];
    }
}
