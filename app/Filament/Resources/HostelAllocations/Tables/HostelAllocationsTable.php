<?php

namespace App\Filament\Resources\HostelAllocations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class HostelAllocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentProfile.student_no')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hostelRoom.room_no')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hostelBed.bed_no')
                    ->label('Bed')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('allocated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('vacated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('allocation_status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('studentProfile')
                    ->relationship('studentProfile', 'student_no')
                    ->label('Student')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('hostel')
                    ->relationship('hostel', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('hostelRoom')
                    ->relationship('hostelRoom', 'room_no')
                    ->label('Room')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('hostelBed')
                    ->relationship('hostelBed', 'bed_no')
                    ->label('Bed')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('allocation_status')
                    ->options([
                        'active' => 'Active',
                        'vacated' => 'Vacated',
                        'cancelled' => 'Cancelled',
                        'transferred' => 'Transferred',
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
