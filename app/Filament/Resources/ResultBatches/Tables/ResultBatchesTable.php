<?php

namespace App\Filament\Resources\ResultBatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResultBatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicYear.name')
                    ->sortable(),
                TextColumn::make('semester.name')
                    ->sortable(),
                TextColumn::make('classSection.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('examTerm.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('academicYear')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('program')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('major')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('classSection')
                    ->relationship('classSection', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('examTerm')
                    ->relationship('examTerm', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'prepared' => 'Prepared',
                        'reviewed' => 'Reviewed',
                        'approved' => 'Approved',
                        'published' => 'Published',
                        'locked' => 'Locked',
                        'cancelled' => 'Cancelled',
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
