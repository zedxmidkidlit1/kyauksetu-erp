<?php

namespace App\Filament\Resources\Assets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset tag')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assetCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('asset_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('building.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('assetCategory')
                    ->relationship('assetCategory', 'name')
                    ->label('Category')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('asset_status')
                    ->options([
                        'available' => 'Available',
                        'assigned' => 'Assigned',
                        'maintenance' => 'Maintenance',
                        'damaged' => 'Damaged',
                        'lost' => 'Lost',
                        'retired' => 'Retired',
                    ]),
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('building')
                    ->relationship('building', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('room')
                    ->relationship('room', 'name')
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
