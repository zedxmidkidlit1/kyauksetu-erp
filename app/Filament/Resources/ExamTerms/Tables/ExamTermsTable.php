<?php

namespace App\Filament\Resources\ExamTerms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExamTermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('academicYear.name')
                    ->sortable(),
                TextColumn::make('semester.name')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('exam_type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
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
                SelectFilter::make('exam_type')
                    ->options([
                        'midterm' => 'Midterm',
                        'final' => 'Final',
                        'supplementary' => 'Supplementary',
                        'practical' => 'Practical',
                        'quiz' => 'Quiz',
                        'other' => 'Other',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'active' => 'Active',
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
