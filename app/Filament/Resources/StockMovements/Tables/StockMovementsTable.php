<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stockItem.name')
                    ->label('Stock item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('movement_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('movement_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('handledBy.email')
                    ->label('Handled by')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('stockItem')
                    ->relationship('stockItem', 'name')
                    ->label('Stock item')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('movement_type')
                    ->options([
                        'in' => 'In',
                        'out' => 'Out',
                        'adjustment' => 'Adjustment',
                        'transfer' => 'Transfer',
                    ]),
                SelectFilter::make('handledBy')
                    ->relationship('handledBy', 'email')
                    ->label('Handled by')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
