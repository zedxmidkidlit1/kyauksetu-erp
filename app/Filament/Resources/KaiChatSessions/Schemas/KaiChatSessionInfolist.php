<?php

namespace App\Filament\Resources\KaiChatSessions\Schemas;

use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KaiChatSessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Session')
                    ->schema([
                        TextEntry::make('title')
                            ->placeholder('Untitled'),
                        TextEntry::make('user.email')
                            ->label('Student'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('last_message_at')
                            ->dateTime(),
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
                    ])
                    ->columns(3),
                Section::make('Metadata')
                    ->schema([
                        CodeEntry::make('metadata')
                            ->placeholder('None')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
