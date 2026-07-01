<?php

namespace App\Filament\Resources\BookCopies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookCopiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('book.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('accession_no')
                    ->label('Accession no.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('barcode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('copy_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('shelf_location')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('book')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('copy_status')
                    ->options([
                        'available' => 'Available',
                        'borrowed' => 'Borrowed',
                        'lost' => 'Lost',
                        'damaged' => 'Damaged',
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
