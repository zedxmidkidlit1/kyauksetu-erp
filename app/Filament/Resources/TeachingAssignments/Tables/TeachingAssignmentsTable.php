<?php

namespace App\Filament\Resources\TeachingAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeachingAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacherProfile.staff_no')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('academicYear.name')
                    ->sortable(),
                TextColumn::make('semester.name')
                    ->sortable(),
                TextColumn::make('classSection.name')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('teacherProfile')
                    ->relationship('teacherProfile', 'staff_no')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('course')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('academicYear')
                    ->relationship('academicYear', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('semester')
                    ->relationship('semester', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('classSection')
                    ->relationship('classSection', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'completed' => 'Completed',
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
