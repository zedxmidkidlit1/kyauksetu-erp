<?php

namespace App\Filament\Resources\StudentStatusHistories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentStatusHistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('studentProfile.student_no')
                    ->label('Student no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('old_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('new_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('effective_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('changedBy.email')
                    ->label('Changed by')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('studentProfile')
                    ->relationship('studentProfile', 'student_no')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('new_status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                        'withdrawn' => 'Withdrawn',
                        'graduated' => 'Graduated',
                        'transferred' => 'Transferred',
                    ]),
                SelectFilter::make('changedBy')
                    ->relationship('changedBy', 'email')
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
