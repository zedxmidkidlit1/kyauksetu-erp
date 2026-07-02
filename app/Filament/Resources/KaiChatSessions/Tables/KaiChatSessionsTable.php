<?php

namespace App\Filament\Resources\KaiChatSessions\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KaiChatSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->placeholder('Untitled')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('driver')
                    ->badge()
                    ->sortable(),
                TextColumn::make('provider')
                    ->badge()
                    ->placeholder('Local')
                    ->sortable(),
                TextColumn::make('model')
                    ->placeholder('Local')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('last_message_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ]),
                SelectFilter::make('driver')
                    ->options([
                        'local' => 'Local',
                        'external' => 'External',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
