<?php

namespace App\Filament\Resources\KaiChatMessages\Schemas;

use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KaiChatMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->schema([
                        TextEntry::make('user.email')
                            ->label('Student'),
                        TextEntry::make('role')
                            ->badge(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('content')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Provider')
                    ->schema([
                        TextEntry::make('driver')
                            ->badge(),
                        TextEntry::make('provider')
                            ->placeholder('Local'),
                        TextEntry::make('model')
                            ->placeholder('Local'),
                        TextEntry::make('error_code')
                            ->placeholder('None'),
                    ])
                    ->columns(4),
                Section::make('Context Keys')
                    ->schema([
                        CodeEntry::make('context_keys')
                            ->placeholder('None')
                            ->columnSpanFull(),
                    ]),
                Section::make('Metadata')
                    ->schema([
                        CodeEntry::make('metadata')
                            ->placeholder('None')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
