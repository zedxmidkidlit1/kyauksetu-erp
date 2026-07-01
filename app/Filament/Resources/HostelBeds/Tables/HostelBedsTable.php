<?php

namespace App\Filament\Resources\HostelBeds\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HostelBedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostelRoom.room_no')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bed_no')
                    ->label('Bed no.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bed_status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('hostelRoom')
                    ->relationship('hostelRoom', 'room_no')
                    ->label('Room')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('bed_status')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'damaged' => 'Damaged',
                        'maintenance' => 'Maintenance',
                        'retired' => 'Retired',
                    ]),
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
