<?php

namespace App\Filament\Resources\KaiChatMessages\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KaiChatMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->badge()
                    ->sortable(),
                TextColumn::make('content')
                    ->limit(80)
                    ->searchable(),
                TextColumn::make('context_keys')
                    ->label('Context keys')
                    ->formatStateUsing(fn (?array $state): string => collect($state ?? [])->join(', '))
                    ->placeholder('None'),
                TextColumn::make('driver')
                    ->badge()
                    ->sortable(),
                TextColumn::make('provider')
                    ->badge()
                    ->placeholder('Local')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'user' => 'User',
                        'assistant' => 'Assistant',
                        'system' => 'System',
                    ]),
                SelectFilter::make('driver')
                    ->options([
                        'local' => 'Local',
                        'external' => 'External',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
